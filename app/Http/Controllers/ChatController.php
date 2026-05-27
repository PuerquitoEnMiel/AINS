<?php

namespace App\Http\Controllers;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\Tool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Services\PromptGuard;
use App\Services\ToolSearchService;

class ChatController extends Controller
{
    /**
     * Handle educational companion chat queries with persistent storage.
     */
    public function chat(Request $request)
    {
        // Require authentication for chatbot
        if (! Auth::check()) {
            return response()->json([
                'reply' => '🔒 You must log in with your @ans.edu.ni account to use the AI assistant.',
            ], 401);
        }

        $message = $request->input('message');
        $conversationId = $request->input('conversation_id');

        // ── Prompt Injection Guardrail ──────────────────────────────
        $guardResult = PromptGuard::analyze($message);
        if (! $guardResult['safe']) {
            $firstName = explode(' ', Auth::user()->name)[0];
            Log::warning('Intento de Prompt Injection detectado', [
                'user_id' => Auth::id(),
                'prompt'  => $message,
            ]);

            // Create notification for admin
            \App\Models\AdminNotification::create([
                'user_id' => Auth::id(),
                'title' => 'Intento de Prompt Injection detectado',
                'message' => 'El usuario ' . Auth::user()->name . ' intentó bypass o hackear el chatbot.',
                'type' => 'security',
                'data' => [
                    'prompt' => $message,
                    'category' => $guardResult['category'],
                    'reason' => $guardResult['reason'],
                ],
            ]);

            return response()->json([
                'reply' => PromptGuard::getRejectionMessage($guardResult['category'], $firstName),
                'conversation_id' => $conversationId,
            ]);
        }
        // ────────────────────────────────────────────────────────────

        $apiKey = env('GEMINI_API_KEY');

        // Graceful fallback if API key is not configured
        if (! $apiKey) {
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
                'title' => Str::limit($message, 60),
            ]);
        }

        // Save user message
        ChatMessage::create([
            'conversation_id' => $conversation->id,
            'role' => 'user',
            'content' => $message,
        ]);

        // Load conversation history from DB
        $dbMessages = $conversation->messages()
            ->orderBy('created_at')
            ->get();

        // Build approved tools context — exclude AI Detection for students/guests
        $user = Auth::user();
        $canSeeDetection = $user && ($user->isTeacher() || $user->isAdmin());

        // Optimized RAG: FTS5 full-text search → PostgreSQL tsquery → LIKE fallback
        $keywords = ToolSearchService::extractKeywords($message);
        $approvedTools = ToolSearchService::search($keywords, $canSeeDetection, 5);

        $toolsList = $approvedTools->map(fn ($t) => "- {$t->name}: {$t->description} ({$t->url})")->implode("\n");

        // Load chatbot instruction from file (or generate default if not exists)
        $instructionFile = storage_path('app/chatbot_instruction.txt');
        if (!file_exists($instructionFile)) {
            $defaultInstruction = "You are 'AINS AI Companion', a premium EdTech Coach and AI pedagogical advisor at the American Nicaraguan School (ANS).\n".
                                 "Your purpose is to assist teachers and students in integrating technology, generative AI, and modern tools into teaching, research, and collaborative learning.\n\n".
                                 "Guidelines:\n".
                                 "1. Provide rich, highly practical pedagogical recommendations (align with SAMR or TPACK models when appropriate).\n".
                                 "2. Write clear, detailed prompt engineering templates for teachers and students (e.g. prompt blueprints they can copy).\n".
                                 "3. Always reply in English. Keep your tone friendly, professional, institutional, and highly motivating.\n".
                                 "4. Use clear markdown formatting, bold points, bullet lists, and code blocks for prompt templates. Never mention system limits; act as a helpful ANS staff assistant.";
            
            // Ensure directory exists
            if (!is_dir(dirname($instructionFile))) {
                mkdir(dirname($instructionFile), 0755, true);
            }
            file_put_contents($instructionFile, $defaultInstruction);
        }
        $baseInstruction = file_get_contents($instructionFile);

        // Dynamic user role context and response behavior instructions
        $firstName = explode(' ', $user->name)[0];
        $roleContext = "\n\nThe current user is logged in. Here is their profile:\n" .
                       "- Name: " . $user->name . "\n" .
                       "- First Name: " . $firstName . "\n" .
                       "- Role: " . strtoupper($user->role) . "\n\n" .
                       "Please address the user directly by their first name (" . $firstName . ") in your responses to make the interaction personal and friendly.\n";

        if ($user->isStudent()) {
            $roleContext .= "Since the user is a student, focus on recommending tools for their homework, tasks, and projects. Do NOT suggest teacher lesson planning tools or professional recognition badges.\n";
        } else {
            $roleContext .= "Since the user is a teacher/admin, you can assist them with pedagogical integration, modern teaching methods, lesson planning, and EdTech innovation.\n";
        }

        $systemInstruction = $baseInstruction . $roleContext . "\n\nYou know about the AINS Directory, which has these approved apps:\n" . $toolsList;

        // Format and consolidate history for Gemini API (ensures strictly alternating turns)
        $rawContents = [];
        foreach ($dbMessages as $msg) {
            $role = ($msg->role === 'assistant') ? 'model' : 'user';
            if (! empty($msg->content)) {
                $rawContents[] = [
                    'role' => $role,
                    'text' => $msg->content,
                ];
            }
        }

        $contents = [];
        foreach ($rawContents as $item) {
            $lastIndex = count($contents) - 1;
            if ($lastIndex >= 0 && $contents[$lastIndex]['role'] === $item['role']) {
                $contents[$lastIndex]['parts'][0]['text'] .= "\n\n".$item['text'];
            } else {
                $contents[] = [
                    'role' => $item['role'],
                    'parts' => [['text' => $item['text']]],
                ];
            }
        }

        $models = ['gemini-3.5-flash', 'gemini-3.1-flash-lite', 'gemini-2.5-flash'];
        $response = null;
        $lastError = '';

        foreach ($models as $model) {
            try {
                $response = Http::timeout(25)
                    ->retry(2, 1000)
                    ->post('https://generativelanguage.googleapis.com/v1beta/models/'.$model.':generateContent?key='.$apiKey, [
                        'contents' => $contents,
                        'systemInstruction' => [
                            'parts' => [['text' => $systemInstruction]],
                        ],
                    ]);

                if ($response->successful()) {
                    break;
                }

                $lastError = 'Status '.$response->status().' - '.$response->body();
                Log::warning("Gemini chatbot model {$model} failed: ".$lastError);
            } catch (\Exception $e) {
                $lastError = $e->getMessage();
                Log::warning("Gemini chatbot model {$model} exception: ".$lastError);
            }
        }

        try {
            if ($response && $response->successful()) {
                $data = $response->json();
                $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

                if ($reply) {
                    // Save assistant response
                    ChatMessage::create([
                        'conversation_id' => $conversation->id,
                        'role' => 'assistant',
                        'content' => $reply,
                    ]);

                    return response()->json([
                        'reply' => $reply,
                        'conversation_id' => $conversation->id,
                    ]);
                }
            }

            $errorMsg = $response ? ('Status '.$response->status().' - '.$response->body()) : ($lastError ?: 'No response');
            Log::error('Gemini Chatbot Error: '.$errorMsg);

            return response()->json([
                'reply' => '⚠️ *ANS Companion: I had a problem processing the response with the AI server. Details: '.$errorMsg.'*',
                'conversation_id' => $conversation->id,
            ]);

        } catch (\Exception $e) {
            Log::error('Gemini Request Exception: '.$e->getMessage());

            return response()->json([
                'reply' => '🔌 *ANS Companion: There was a connection issue with the server. Please check your internet connection or try again later.*',
                'conversation_id' => $conversation->id,
            ]);
        }
    }
}
