<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tool;
use App\Models\ToolInsight;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ToolInsightController extends Controller
{
    public function generate(Tool $tool)
    {
        $reviews = $tool->reviews()->with('user')->get();
        $reviewCount = $reviews->count();

        if ($reviewCount < 3) {
            return redirect()->back()->with('error', 'At least 3 reviews are required to generate AI insights.');
        }

        $apiKey = env('GEMINI_API_KEY');
        if (!$apiKey) {
            return redirect()->back()->with('error', 'Gemini API Key is not configured.');
        }

        $reviewsText = $reviews->map(function ($r) {
            return "- [Calificación: {$r->rating}/5] Comentario: " . ($r->comment ?: '(Sin comentario)');
        })->implode("\n");

        $prompt = "Analyze the following teacher reviews for the educational tool: '{$tool->name}' (Description: {$tool->description}).\n\n" .
                  "Reviews:\n" .
                  $reviewsText . "\n\n" .
                  "Generate a summary and pedagogical insights. Your response MUST be a valid JSON object matching this schema exactly (do not output any other text or explanation, respond ONLY with raw JSON):\n" .
                  "{\n" .
                  "  \"summary\": \"A concise 2-3 sentence overview in English synthesizing the overall sentiment and pedagogical value.\",\n" .
                  "  \"pros\": [\"pro 1 in English\", \"pro 2 in English\", \"pro 3 in English\"],\n" .
                  "  \"cons\": [\"con 1 in English\", \"con 2 in English\", \"con 3 in English\"],\n" .
                  "  \"best_for_grades\": [\"Grade levels in English, e.g. Pre-K, K-2, Elementary, Middle School, High School\"],\n" .
                  "  \"best_use_cases\": [\"Key educational use cases in English, e.g. Formative Assessment, Cooperative Learning\"]\n" .
                  "}\n\n" .
                  "Note: translate the fields to English as requested. Do not include markdown code block syntax (like ```json) in your output, just the raw JSON.";

        try {
            $response = Http::timeout(30)->withoutVerifying()->post(
                'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $apiKey,
                [
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [['text' => $prompt]],
                        ],
                    ],
                ]
            );

            if ($response->successful()) {
                $data = $response->json();
                $rawText = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

                if ($rawText) {
                    $text = trim($rawText);
                    if (str_starts_with($text, '```json')) {
                        $text = substr($text, 7);
                    }
                    if (str_starts_with($text, '```')) {
                        $text = substr($text, 3);
                    }
                    if (str_ends_with($text, '```')) {
                        $text = substr($text, 0, -3);
                    }
                    $text = trim($text);

                    $insightData = json_decode($text, true);

                    if (json_last_error() === JSON_ERROR_NONE && isset($insightData['summary'], $insightData['pros'], $insightData['cons'])) {
                        $tool->insight()->updateOrCreate(
                            ['tool_id' => $tool->id],
                            [
                                'summary' => $insightData['summary'],
                                'pros' => $insightData['pros'],
                                'cons' => $insightData['cons'],
                                'best_for_grades' => $insightData['best_for_grades'] ?? [],
                                'best_use_cases' => $insightData['best_use_cases'] ?? [],
                                'generated_at' => Carbon::now(),
                                'review_count_at_generation' => $reviewCount,
                            ]
                        );

                        return redirect()->back()->with('success', 'AI insights successfully generated for ' . $tool->name);
                    } else {
                        Log::error('Invalid JSON structure from Gemini: ' . $text);
                        return redirect()->back()->with('error', 'Error processing the AI assistant response format.');
                    }
                }
            }

            Log::error('Gemini Insight Error: Status ' . $response->status() . ' - ' . $response->body());
            return redirect()->back()->with('error', 'Error connecting to the AI server.');

        } catch (\Exception $e) {
            Log::error('Gemini Insight Exception: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error generating AI insights: ' . $e->getMessage());
        }
    }
}
