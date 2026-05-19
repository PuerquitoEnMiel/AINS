<?php

namespace App\Http\Controllers;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    /**
     * Handle educational companion chat queries with persistent storage.
     */
    public function chat(Request $request)
    {
        // Require authentication for chatbot
        if (!Auth::check()) {
            return response()->json([
                'reply' => "🔒 Debes iniciar sesión con tu cuenta @ans.edu.ni para usar el asistente de IA."
            ], 401);
        }

        $message = $request->input('message');
        $conversationId = $request->input('conversation_id');

        $apiKey = env('GEMINI_API_KEY');

        // Graceful fallback if API key is not configured
        if (!$apiKey) {
            return response()->json([
                'reply' => "👋 **Hola! Soy AINS AI Companion**, tu asesor personal de tecnología educativa en ANS.\n\nPara activar mi inteligencia de lenguaje en tiempo real, pídele al administrador que configure la variable `GEMINI_API_KEY` en el archivo `.env`.\n\nMientras tanto, ¡puedo recomendarte excelentes herramientas aprobadas de nuestro catálogo! \n- Usa **Stitch** para una planificación curricular impecable.\n- Usa **Pomelo** para automatizar tus tareas de gestión escolar.\n- Prueba **NotebookLM** para analizar y resumir artículos académicos de forma interactiva."
            ]);
        }

        // Get or create conversation
        $conversation = null;
        if ($conversationId) {
            $conversation = ChatConversation::where('id', $conversationId)
                ->where('user_id', Auth::id())
                ->first();
        }

        if (!$conversation) {
            $conversation = ChatConversation::create([
                'user_id' => Auth::id(),
                'title' => \Illuminate\Support\Str::limit($message, 60),
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

        // Build approved tools context from real database
        $approvedTools = \App\Models\Tool::approved()->get(['name', 'description', 'url', 'category']);
        $toolsList = $approvedTools->map(fn($t) => "- {$t->name}: {$t->description} ({$t->url})")->implode("\n");

        // Deep pedagogical and institutional system prompt
        $systemInstruction = "You are 'AINS AI Companion', a premium EdTech Coach and AI pedagogical advisor at the American Nicaraguan School (ANS). " .
                             "Your purpose is to assist teachers and students in integrating technology, generative AI, and modern tools into teaching, research, and collaborative learning. " .
                             "You know about the AINS Directory, which has these approved apps:\n" .
                             $toolsList . "\n\n" .
                             "Guidelines:\n" .
                             "1. Provide rich, highly practical pedagogical recommendations (align with SAMR or TPACK models when appropriate).\n" .
                             "2. Write clear, detailed prompt engineering templates for teachers and students (e.g. prompt blueprints they can copy).\n" .
                             "3. Always reply in the user's language (Spanish or English). Keep your tone friendly, professional, institutional, and highly motivating.\n" .
                             "4. Use clear markdown formatting, bold points, bullet lists, and code blocks for prompt templates. Never mention system limits; act as a helpful ANS staff assistant.";

        // Format history for Gemini API
        $contents = [];
        foreach ($dbMessages as $msg) {
            $role = ($msg->role === 'assistant') ? 'model' : 'user';
            if (!empty($msg->content)) {
                $contents[] = [
                    'role' => $role,
                    'parts' => [['text' => $msg->content]]
                ];
            }
        }

        try {
            $response = Http::timeout(25)->withoutVerifying()->withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $apiKey, [
                'contents' => $contents,
                'systemInstruction' => [
                    'parts' => [['text' => $systemInstruction]]
                ]
            ]);

            if ($response->successful()) {
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

            Log::error('Gemini API Error: Status ' . $response->status() . ' - ' . $response->body());
            
            return response()->json([
                'reply' => "⚠️ *ANS Companion: Tuve un problema al procesar la respuesta con el servidor de inteligencia artificial. Por favor intenta de nuevo en unos momentos.*",
                'conversation_id' => $conversation->id,
            ]);

        } catch (\Exception $e) {
            Log::error('Gemini Request Exception: ' . $e->getMessage());
            return response()->json([
                'reply' => "🔌 *ANS Companion: Hubo un problema de conexión con el servidor. Verifica tu conexión a internet o intenta más tarde.*",
                'conversation_id' => $conversation->id,
            ]);
        }
    }
}
