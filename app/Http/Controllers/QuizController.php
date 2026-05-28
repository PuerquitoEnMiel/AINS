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
            return back()->with('error', 'The quiz has no questions.');
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
            ->with('success', $passed ? 'Congratulations! You earned the badge.' : 'Quiz not passed. Keep trying!');
    }

    public function generateQuiz(Badge $badge)
    {
        $apiKey = env('GEMINI_API_KEY');
        if (! $apiKey) {
            return response()->json(['error' => 'Gemini API Key is not configured.'], 500);
        }

        $prompt = "Generate an educational and professional quiz of 5 multiple-choice questions in English about the topic or pedagogical tool: \"{$badge->name}\" ({$badge->description}).\n\n" .
                  "The format must be strictly a JSON array of objects with the following structure and exact keys in lowercase:\n" .
                  "[\n" .
                  "  {\n" .
                  "    \"question\": \"What is the question...?\",\n" .
                  "    \"options\": {\n" .
                  "      \"a\": \"Option A\",\n" .
                  "      \"b\": \"Option B\",\n" .
                  "      \"c\": \"Option C\",\n" .
                  "      \"d\": \"Option D\"\n" .
                  "    },\n" .
                  "    \"correct\": \"a\",\n" .
                  "    \"explanation\": \"Pedagogical explanation of the correct answer.\"\n" .
                  "  }\n" .
                  "]\n\n" .
                  "Ensure that the 'correct' key contains only a single letter ('a', 'b', 'c', or 'd'). Respond ONLY with valid JSON. Do not use markdown code blocks, do not add text before or after the JSON.";

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
                            'description' => 'Demonstrate your knowledge about ' . $badge->name,
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
                return response()->json(['error' => 'The AI response does not have a valid structured question format.'], 500);
            }

            Log::error('Gemini Quiz API Error: ' . $response->body());
            return response()->json(['error' => 'AI server response error.'], 500);

        } catch (\Exception $e) {
            Log::error('Gemini Quiz Exception: ' . $e->getMessage());
            return response()->json(['error' => 'AI Exception: ' . $e->getMessage()], 500);
        }
    }
}
