<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    /**
     * Handle educational companion chat queries securely.
     */
    public function chat(Request $request)
    {
        $message = $request->input('message');
        $history = $request->input('history', []);

        $apiKey = env('GEMINI_API_KEY');

        // Graceful fallback if API key is not configured
        if (!$apiKey) {
            return response()->json([
                'reply' => "👋 **Hola! Soy AINS AI Companion**, tu asesor personal de tecnología educativa en ANS.\n\nPara activar mi inteligencia de lenguaje en tiempo real, pídele al administrador que configure la variable `GEMINI_API_KEY` en el archivo `.env`.\n\nMientras tanto, ¡puedo recomendarte excelentes herramientas aprobadas de nuestro catálogo! \n- Usa **Stitch** para una planificación curricular impecable.\n- Usa **Pomelo** para automatizar tus tareas de gestión escolar.\n- Prueba **NotebookLM** para analizar y resumir artículos académicos de forma interactiva."
            ]);
        }

        // Deep pedagogical and institutional system prompt
        $systemInstruction = "You are 'AINS AI Companion', a premium EdTech Coach and AI pedagogical advisor at the American Nicaraguan School (ANS). " .
                             "Your purpose is to assist teachers and students in integrating technology, generative AI, and modern tools into teaching, research, and collaborative learning. " .
                             "You know about the AINS Directory, which has approved apps like:\n" .
                             "- Stitch: A specialized AI lesson planner for teachers to design rubrics, syllabus outlines, and customized lessons.\n" .
                             "- Pomelo: An EdTech automation assistant to streamline administrative workflows and grade calculations.\n" .
                             "- Antigravity: The custom pair-programming coding assistant and development agent.\n" .
                             "- Flow: Collaborative planning and workflow tracking dashboard for team projects.\n" .
                             "- Canva AI, NotebookLM, Suno AI, Gemini, ChatGPT, Perplexity (approved educational platforms).\n\n" .
                             "Guidelines:\n" .
                             "1. Provide rich, highly practical pedagogical recommendations (align with SAMR or TPACK models when appropriate).\n" .
                             "2. Write clear, detailed prompt engineering templates for teachers and students (e.g. prompt blueprints they can copy).\n" .
                             "3. Always reply in the user's language (Spanish or English). Keep your tone friendly, professional, institutional, and highly motivating.\n" .
                             "4. Use clear markdown formatting, bold points, bullet lists, and code blocks for prompt templates. Never mention system limits; act as a helpful ANS staff assistant.";

        // Format history for Gemini API standard chat structure
        $contents = [];
        foreach ($history as $msg) {
            $role = (isset($msg['role']) && $msg['role'] === 'assistant') ? 'model' : 'user';
            $text = $msg['content'] ?? '';
            if (!empty($text)) {
                $contents[] = [
                    'role' => $role,
                    'parts' => [['text' => $text]]
                ];
            }
        }

        // Append current message
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $message]]
        ];

        try {
            $response = Http::withoutVerifying()->withHeaders([
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
                    return response()->json(['reply' => $reply]);
                }
            }

            Log::error('Gemini API Error: Status ' . $response->status() . ' - ' . $response->body());
            
            return response()->json([
                'reply' => "⚠️ *ANS Companion: Tuve un problema al procesar la respuesta con el servidor de inteligencia artificial. Por favor intenta de nuevo en unos momentos.*"
            ]);

        } catch (\Exception $e) {
            Log::error('Gemini Request Exception: ' . $e->getMessage());
            return response()->json([
                'reply' => "🔌 *ANS Companion: Hubo un problema de conexión con el servidor. Verifica tu conexión a internet o intenta más tarde.*"
            ]);
        }
    }
}
