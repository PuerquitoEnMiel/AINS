<?php

namespace App\Http\Controllers;

use App\Models\AdminNotification;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Services\GeminiService;
use App\Services\PromptGuard;
use App\Services\ToolSearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    public function __construct(protected GeminiService $gemini) {}

    /**
     * Handle educational companion chat queries with persistent storage.
     */
    public function chat(Request $request)
    {
        if (! Auth::check()) {
            return response()->json([
                'reply' => '🔒 You must log in with your @ans.edu.ni account to use the AI assistant.',
            ], 401);
        }

        $message        = $request->input('message');
        $conversationId = $request->input('conversation_id');

        // ── Prompt Injection Guardrail ──────────────────────────────
        $guardResult = PromptGuard::analyze($message);
        if (! $guardResult['safe']) {
            $firstName = explode(' ', Auth::user()->name)[0];
            Log::warning('Prompt injection attempt detected', [
                'user_id' => Auth::id(),
                'prompt'  => $message,
            ]);

            AdminNotification::create([
                'user_id' => Auth::id(),
                'title'   => 'Prompt Injection Attempt Detected',
                'message' => 'User ' . Auth::user()->name . ' attempted to bypass or exploit the chatbot.',
                'type'    => 'security',
                'data'    => [
                    'prompt'   => $message,
                    'category' => $guardResult['category'],
                    'reason'   => $guardResult['reason'],
                ],
            ]);

            return response()->json([
                'reply'           => PromptGuard::getRejectionMessage($guardResult['category'], $firstName),
                'conversation_id' => $conversationId,
            ]);
        }
        // ────────────────────────────────────────────────────────────

        if (! $this->gemini->isConfigured()) {
            $firstName = explode(' ', Auth::user()->name)[0];
            return response()->json([
                'reply' => "👋 **Hello, {$firstName}! I am AINS AI Companion**, your personal EdTech advisor at ANS.\n\nTo activate my real-time language intelligence, please ask the administrator to configure the `GEMINI_API_KEY` variable in the `.env` file.\n\nIn the meantime, I can recommend excellent approved tools from our catalog! \n- Use **Stitch** for flawless curriculum planning.\n- Use **Pomelo** to automate your school management tasks.\n- Try **NotebookLM** to interactively analyze and summarize academic articles.",
            ]);
        }

        // Get or create conversation
        $conversation = null;
        if ($conversationId) {
            $conversation = ChatConversation::where('id', $conversationId)
                ->where('user_id', Auth::id())
                ->first();
        }

        if (! $conversation) {
            $conversation = ChatConversation::create([
                'user_id' => Auth::id(),
                'title'   => Str::limit($message, 60),
            ]);
        }

        // Save user message
        ChatMessage::create([
            'conversation_id' => $conversation->id,
            'role'            => 'user',
            'content'         => $message,
        ]);

        // Load conversation history
        $dbMessages = $conversation->messages()->orderBy('created_at')->get();

        // Build tool context via RAG
        $user            = Auth::user();
        $canSeeDetection = $user && ($user->isTeacher() || $user->isAdmin());
        $keywords        = ToolSearchService::extractKeywords($message);
        $approvedTools   = ToolSearchService::search($keywords, $canSeeDetection, 5);
        $toolsList       = $approvedTools->map(fn ($t) => "- {$t->name}: {$t->description} ({$t->url})")->implode("\n");

        // Load system instruction
        $systemInstruction = $this->buildSystemInstruction($user, $toolsList);

        // Build strictly-alternating Gemini contents array
        $contents = $this->buildContents($dbMessages);

        // Call Gemini
        $reply = $this->gemini->chat($contents, $systemInstruction);

        if ($reply) {
            ChatMessage::create([
                'conversation_id' => $conversation->id,
                'role'            => 'assistant',
                'content'         => $reply,
            ]);

            return response()->json([
                'reply'           => $reply,
                'conversation_id' => $conversation->id,
            ]);
        }

        return response()->json([
            'reply'           => '⚠️ *ANS Companion: I had a problem processing the response. Please try again.*',
            'conversation_id' => $conversation->id,
        ]);
    }

    // ── Private Helpers ──────────────────────────────────────────

    protected function buildSystemInstruction($user, string $toolsList): string
    {
        $instructionFile = storage_path('app/chatbot_instruction.txt');

        $base = \Illuminate\Support\Facades\Cache::rememberForever(\App\Support\CacheKeys::CHATBOT_INSTRUCTION, function () use ($instructionFile) {
            if (! file_exists($instructionFile)) {
                $default = "You are 'AINS AI Companion', a premium EdTech Coach and AI pedagogical advisor at the American Nicaraguan School (ANS).\n" .
                           "Your purpose is to assist teachers and students in integrating technology, generative AI, and modern tools into teaching, research, and collaborative learning.\n\n" .
                           "Guidelines:\n" .
                           "1. Provide rich, highly practical pedagogical recommendations (align with SAMR or TPACK models when appropriate).\n" .
                           "2. Write clear, detailed prompt engineering templates for teachers and students (e.g. prompt blueprints they can copy).\n" .
                           "3. Always reply in English. Keep your tone friendly, professional, institutional, and highly motivating.\n" .
                           "4. Use clear markdown formatting, bold points, bullet lists, and code blocks for prompt templates. Never mention system limits; act as a helpful ANS staff assistant.";

                if (! is_dir(dirname($instructionFile))) {
                    mkdir(dirname($instructionFile), 0755, true);
                }
                file_put_contents($instructionFile, $default);
            }
            return file_get_contents($instructionFile);
        });

        $firstName = explode(' ', $user->name)[0];

        $roleContext = "\n\nThe current user is logged in. Here is their profile:\n" .
                      "- Name: {$user->name}\n" .
                      "- First Name: {$firstName}\n" .
                      "- Role: " . strtoupper($user->role) . "\n\n" .
                      "Please address the user directly by their first name ({$firstName}) in your responses.\n";

        if ($user->isStudent()) {
            $roleContext .= "Since the user is a student, focus on recommending tools for their homework, tasks, and projects. Do NOT suggest teacher lesson planning tools or professional recognition badges.\n";
        } else {
            $roleContext .= "Since the user is a teacher/admin, you can assist them with pedagogical integration, modern teaching methods, lesson planning, and EdTech innovation.\n";
        }

        return $base . $roleContext . "\n\nYou know about the AINS Directory, which has these approved apps:\n" . $toolsList;
    }

    /**
     * Merge same-role consecutive messages, ensure alternating user/model turns.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $dbMessages
     * @return array<int, array>
     */
    protected function buildContents($dbMessages): array
    {
        $raw = [];
        foreach ($dbMessages as $msg) {
            $role = ($msg->role === 'assistant') ? 'model' : 'user';
            if (! empty($msg->content)) {
                $raw[] = ['role' => $role, 'text' => $msg->content];
            }
        }

        $contents  = [];
        foreach ($raw as $item) {
            $lastIndex = count($contents) - 1;
            if ($lastIndex >= 0 && $contents[$lastIndex]['role'] === $item['role']) {
                $contents[$lastIndex]['parts'][0]['text'] .= "\n\n" . $item['text'];
            } else {
                $contents[] = [
                    'role'  => $item['role'],
                    'parts' => [['text' => $item['text']]],
                ];
            }
        }

        return $contents;
    }
}
