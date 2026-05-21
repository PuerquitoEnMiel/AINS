<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Badge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class QuizController extends Controller
{
    public function show(Quiz $quiz)
    {
        $badge = $quiz->badge;
        $hasBadge = auth()->user()->hasBadge($badge->slug);
        
        return view('quizzes.show', compact('quiz', 'badge', 'hasBadge'));
    }

    public function submit(Request $request, Quiz $quiz)
    {
        $badge = $quiz->badge;
        $questions = $quiz->questions;
        $submittedAnswers = $request->input('answers', []);
        
        $totalQuestions = count($questions);
        if ($totalQuestions === 0) {
            return back()->with('error', 'El cuestionario no tiene preguntas.');
        }
        
        $correctCount = 0;
        $gradedQuestions = [];
        
        foreach ($questions as $index => $q) {
            $userAns = $submittedAnswers[$index] ?? null;
            $correctAns = $q['correct'];
            $isCorrect = ($userAns === $correctAns);
            
            if ($isCorrect) {
                $correctCount++;
            }
            
            $gradedQuestions[] = [
                'question' => $q['question'],
                'options' => $q['options'],
                'correct' => $correctAns,
                'user_answer' => $userAns,
                'is_correct' => $isCorrect,
                'explanation' => $q['explanation'] ?? '',
            ];
        }
        
        $score = round(($correctCount / $totalQuestions) * 100);
        $passed = ($score >= $quiz->passing_score);
        
        $attempt = QuizAttempt::create([
            'user_id' => auth()->id(),
            'quiz_id' => $quiz->id,
            'answers' => $gradedQuestions,
            'score' => $score,
            'passed' => $passed,
        ]);
        
        if ($passed && ! auth()->user()->hasBadge($badge->slug)) {
            auth()->user()->badges()->attach($badge->id, [
                'earned_at' => now(),
                'score' => $score,
            ]);
        }
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'passed' => $passed,
                'score' => $score,
                'correct_count' => $correctCount,
                'total_questions' => $totalQuestions,
                'attempt_id' => $attempt->id,
                'graded' => $gradedQuestions,
                'badge_url' => route('badges.show', $badge->slug),
            ]);
        }
        
        return redirect()->route('badges.show', $badge->slug)
            ->with('success', $passed ? '¡Felicidades! Has ganado la insignia.' : 'Cuestionario no aprobado. ¡Sigue intentando!');
    }

    public function generateQuiz(Badge $badge)
    {
        $apiKey = env('GEMINI_API_KEY');
        if (! $apiKey) {
            return response()->json(['error' => 'API Key de Gemini no configurada.'], 500);
        }

        $prompt = "Genera un cuestionario educativo y profesional de 5 preguntas de opción múltiple en español sobre el tema o herramienta pedagógica: \"{$badge->name}\" ({$badge->description}).\n\n" .
                  "El formato debe ser estrictamente un arreglo JSON de objetos con la siguiente estructura y llaves exactas en minúscula:\n" .
                  "[\n" .
                  "  {\n" .
                  "    \"question\": \"¿Cuál es la pregunta...?\",\n" .
                  "    \"options\": {\n" .
                  "      \"a\": \"Opción A\",\n" .
                  "      \"b\": \"Opción B\",\n" .
                  "      \"c\": \"Opción C\",\n" .
                  "      \"d\": \"Opción D\"\n" .
                  "    },\n" .
                  "    \"correct\": \"a\",\n" .
                  "    \"explanation\": \"Explicación pedagógica de la respuesta correcta.\"\n" .
                  "  }\n" .
                  "]\n\n" .
                  "Asegúrate de que la llave 'correct' contenga únicamente una sola letra ('a', 'b', 'c' o 'd'). Responde ÚNICAMENTE con el código JSON válido. No uses bloques de código markdown, no agregues texto antes ni después del JSON.";

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
                $rawJson = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
                
                $rawJson = trim($rawJson);
                if (str_starts_with($rawJson, '```json')) {
                    $rawJson = substr($rawJson, 7);
                }
                if (str_ends_with($rawJson, '```')) {
                    $rawJson = substr($rawJson, 0, -3);
                }
                $rawJson = trim($rawJson);

                $questions = json_decode($rawJson, true);

                if (is_array($questions) && ! empty($questions)) {
                    $quiz = Quiz::updateOrCreate(
                        ['badge_id' => $badge->id],
                        [
                            'title' => 'Quiz: ' . $badge->name,
                            'description' => 'Demuestra tus conocimientos sobre ' . $badge->name,
                            'questions' => $questions,
                            'passing_score' => 80,
                        ]
                    );

                    return response()->json([
                        'success' => true,
                        'quiz' => $quiz,
                    ]);
                }
                
                Log::error('Gemini Quiz Invalid JSON Structure: ' . $rawJson);
                return response()->json(['error' => 'La respuesta de IA no tiene un formato estructurado de preguntas válido.'], 500);
            }

            Log::error('Gemini Quiz API Error: ' . $response->body());
            return response()->json(['error' => 'Error de respuesta del servidor de IA.'], 500);

        } catch (\Exception $e) {
            Log::error('Gemini Quiz Exception: ' . $e->getMessage());
            return response()->json(['error' => 'Excepción de IA: ' . $e->getMessage()], 500);
        }
    }
}
