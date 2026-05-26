<?php

namespace App\Http\Controllers;

use App\Models\LessonPlan;
use App\Models\Tool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LessonPlanController extends Controller
{
    public function index()
    {
        $plans = LessonPlan::where('user_id', auth()->id())->latest()->get();

        return view('lesson_plans.index', compact('plans'));
    }

    public function create()
    {
        return view('lesson_plans.create');
    }

    public function generate(Request $request)
    {
        set_time_limit(300);

        $request->validate([
            'title' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'grade_level' => 'required|string|max:255',
            'objectives' => 'required|string',
            'duration' => 'required|string|max:255',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,jpg,jpeg,png,webp,mp3,wav,mp4,mov|max:51200', // 50MB limit
        ]);

        $apiKey = env('GEMINI_API_KEY');
        if (! $apiKey) {
            return response()->json([
                'error' => 'Gemini API Key is not configured in the .env file.',
            ], 500);
        }

        // Get approved tools context
        $tools = Tool::approved()->with('categoryRelation')->get();
        $toolsContext = $tools->map(function ($t) {
            $catName = $t->categoryRelation?->name ?? 'Other';
            return "- {$t->name} (Category: {$catName}): {$t->description}";
        })->implode("\n");

        $prompt = "Design a detailed and professional lesson plan for the American Nicaraguan School (ANS).\n\n".
                  "Class details:\n".
                  "- Title/Topic: {$request->title}\n".
                  "- Subject: {$request->subject}\n".
                  "- Grade Level: {$request->grade_level}\n".
                  "- Learning Objectives: {$request->objectives}\n".
                  "- Duration: {$request->duration}\n\n";

        if ($request->hasFile('attachment')) {
            $prompt .= "I have attached a reference document/image. Please analyze the attached content and incorporate details/materials from it into this lesson plan.\n\n";
        }

        $prompt .= "Pedagogical guidelines:\n".
                   "1. Structure the plan into clear phases: Warm-up, Activities, Wrap-up, and Assessment.\n".
                   "2. Explicitly integrate one or more of the following educational technology tools from the approved AINS catalog into the class development. Mention the exact name of the tool in bold (e.g. **Canva**, **Kahoot**):\n".
                   $toolsContext."\n\n".
                   "3. Add suggestions for AI prompts or templates applicable for this specific lesson.\n".
                   "4. Respond in English. Use clean, professional Markdown formatting with subheadings, bulleted lists, and code blocks for templates.";

        $parts = [
            ['text' => $prompt]
        ];

        if ($request->hasFile('attachment')) {
            try {
                $file = $request->file('attachment');
                $mimeType = $file->getMimeType();
                $base64Data = base64_encode(file_get_contents($file->getRealPath()));
                $parts[] = [
                    'inlineData' => [
                        'mimeType' => $mimeType,
                        'data' => $base64Data,
                    ]
                ];
            } catch (\Exception $e) {
                Log::error('Gemini Attachment Read Error: ' . $e->getMessage());
            }
        }

        $models = ['gemini-3.5-flash', 'gemini-3.1-flash-lite', 'gemini-2.5-flash'];
        $response = null;
        $lastError = '';

        foreach ($models as $model) {
            try {
                $response = Http::timeout(180)
                    ->withoutVerifying()
                    ->retry(2, 1000)
                    ->post(
                        'https://generativelanguage.googleapis.com/v1beta/models/'.$model.':generateContent?key='.$apiKey,
                        [
                            'contents' => [
                                [
                                    'role' => 'user',
                                    'parts' => $parts,
                                ],
                            ],
                        ]
                    );

                if ($response->successful()) {
                    break;
                }

                $lastError = 'Status '.$response->status().' - '.$response->body();
                Log::warning("Gemini model {$model} failed: ".$lastError);
            } catch (\Exception $e) {
                $lastError = $e->getMessage();
                Log::warning("Gemini model {$model} exception: ".$lastError);
            }
        }

        try {
            if ($response && $response->successful()) {
                $data = $response->json();
                $markdown = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

                if ($markdown) {
                    // Identify which tools from the DB were mentioned in the markdown
                    $detectedToolIds = [];
                    foreach ($tools as $tool) {
                        if (stripos($markdown, $tool->name) !== false) {
                            $detectedToolIds[] = $tool->id;
                        }
                    }

                    return response()->json([
                        'markdown' => $markdown,
                        'tool_ids' => $detectedToolIds,
                    ]);
                }
            }

            $errorMsg = $response ? ('Status '.$response->status().' - '.$response->body()) : ($lastError ?: 'No response');
            Log::error('Gemini Planner Error: '.$errorMsg);

            return response()->json([
                'error' => 'Error generating the lesson plan from the AI server: '.$errorMsg,
            ], 500);

        } catch (\Exception $e) {
            Log::error('Gemini Planner Exception: '.$e->getMessage());

            return response()->json([
                'error' => 'Connection error with the AI assistant: '.$e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'grade_level' => 'required|string|max:255',
            'objectives' => 'required|string',
            'duration' => 'required|string|max:255',
            'content' => 'required|string',
            'selected_tools' => 'nullable|array',
        ]);

        $plan = LessonPlan::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'subject' => $request->subject,
            'grade_level' => $request->grade_level,
            'objectives' => $request->objectives,
            'duration' => $request->duration,
            'content' => $request->input('content'),
            'selected_tools' => $request->selected_tools ?? [],
        ]);

        return response()->json([
            'success' => true,
            'id' => $plan->id,
            'url' => route('lesson-plans.show', $plan),
        ]);
    }

    public function show(LessonPlan $lessonPlan)
    {
        if ($lessonPlan->user_id !== auth()->id()) {
            abort(403, 'Unauthorized.');
        }

        $tools = [];
        if (! empty($lessonPlan->selected_tools)) {
            $tools = Tool::approved()
                ->whereIn('id', $lessonPlan->selected_tools)
                ->get();
        }

        return view('lesson_plans.show', compact('lessonPlan', 'tools'));
    }

    public function export(LessonPlan $lessonPlan)
    {
        if ($lessonPlan->user_id !== auth()->id()) {
            abort(403, 'Unauthorized.');
        }

        $token = session('google_access_token');
        if (! $token) {
            if (request()->ajax()) {
                session(['google_export_redirect' => route('lesson-plans.export', $lessonPlan)]);
                return response()->json([
                    'redirect' => route('auth.google.export-authorize'),
                ]);
            }
            session(['google_export_redirect' => route('lesson-plans.export', $lessonPlan)]);
            return redirect()->route('auth.google.export-authorize');
        }

        try {
            $createResponse = Http::timeout(20)->withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ])->withoutVerifying()->post('https://docs.googleapis.com/v1/documents', [
                'title' => $lessonPlan->title,
            ]);

            if ($createResponse->status() === 401) {
                session()->forget('google_access_token');
                if (request()->ajax()) {
                    session(['google_export_redirect' => route('lesson-plans.export', $lessonPlan)]);
                    return response()->json([
                        'redirect' => route('auth.google.export-authorize'),
                    ]);
                }
                session(['google_export_redirect' => route('lesson-plans.export', $lessonPlan)]);
                return redirect()->route('auth.google.export-authorize');
            }

            if (! $createResponse->successful()) {
                Log::error('Google Docs Create Error: ' . $createResponse->body());
                if (request()->ajax()) {
                    return response()->json(['error' => 'Error creating the document in Google Docs.'], 500);
                }
                return redirect()->route('lesson-plans.show', $lessonPlan)
                    ->with('error', 'Error creating the document in Google Docs.');
            }

            $docData = $createResponse->json();
            $documentId = $docData['documentId'];

            $requests = $this->buildGoogleDocsRequests($lessonPlan->content);

            if (! empty($requests)) {
                $updateResponse = Http::timeout(20)->withHeaders([
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                ])->withoutVerifying()->post("https://docs.googleapis.com/v1/documents/{$documentId}:batchUpdate", [
                    'requests' => $requests,
                ]);

                if (! $updateResponse->successful()) {
                    Log::error('Google Docs Update Error: ' . $updateResponse->body());
                }
            }

            $url = "https://docs.google.com/document/d/{$documentId}/edit";

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'url' => $url,
                ]);
            }

            return redirect($url);

        } catch (\Exception $e) {
            Log::error('Google Docs Export Exception: ' . $e->getMessage());
            if (request()->ajax()) {
                return response()->json(['error' => 'Connection exception with Google API: ' . $e->getMessage()], 500);
            }
            return redirect()->route('lesson-plans.show', $lessonPlan)
                ->with('error', 'Connection error with Google: ' . $e->getMessage());
        }
    }

    private function buildGoogleDocsRequests($markdown)
    {
        $lines = explode("\n", $markdown);
        $fullText = "";
        $styles = [];
        $currentIndex = 1;

        foreach ($lines as $line) {
            $cleanLine = $line;
            $lineStyle = 'normal';

            // Match headings: #, ##, ###, ####, #####, ######
            if (preg_match('/^(#{1,6})\s+(.*)$/', trim($line), $matches)) {
                $level = strlen($matches[1]);
                $cleanLine = $matches[2];
                $lineStyle = 'h' . $level;
            } elseif (str_starts_with(trim($line), '- ')) {
                $cleanLine = '• ' . substr(trim($line), 2);
                $lineStyle = 'list';
            } elseif (str_starts_with(trim($line), '* ')) {
                $cleanLine = '• ' . substr(trim($line), 2);
                $lineStyle = 'list';
            }

            // Remove bold/italic markdown signs
            $cleanLine = str_replace(['**', '*'], '', $cleanLine);
            $textToInsert = $cleanLine . "\n";

            try {
                $converted = iconv('UTF-8', 'UTF-16LE', $textToInsert);
                $length = $converted !== false ? strlen($converted) / 2 : mb_strlen($textToInsert);
            } catch (\Exception $e) {
                $length = mb_strlen($textToInsert);
            }

            if ($length > 0) {
                $styles[] = [
                    'start' => $currentIndex,
                    'end' => $currentIndex + $length - 1,
                    'style' => $lineStyle
                ];

                $fullText .= $textToInsert;
                $currentIndex += $length;
            }
        }

        if (empty($fullText)) {
            return [];
        }

        $requests = [
            [
                'insertText' => [
                    'location' => ['index' => 1],
                    'text' => $fullText
                ]
            ]
        ];

        foreach ($styles as $s) {
            if ($s['style'] === 'h1') {
                $requests[] = [
                    'updateTextStyle' => [
                        'range' => [
                            'startIndex' => $s['start'],
                            'endIndex' => $s['end']
                        ],
                        'textStyle' => [
                            'fontSize' => ['magnitude' => 20, 'unit' => 'PT'],
                            'bold' => true,
                            'foregroundColor' => [
                                'color' => [
                                    'rgbColor' => ['red' => 0.08, 'green' => 0.38, 'blue' => 0.31]
                                ]
                            ]
                        ],
                        'fields' => 'fontSize,bold,foregroundColor'
                    ]
                ];
            } elseif ($s['style'] === 'h2') {
                $requests[] = [
                    'updateTextStyle' => [
                        'range' => [
                            'startIndex' => $s['start'],
                            'endIndex' => $s['end']
                        ],
                        'textStyle' => [
                            'fontSize' => ['magnitude' => 15, 'unit' => 'PT'],
                            'bold' => true,
                            'foregroundColor' => [
                                'color' => [
                                    'rgbColor' => ['red' => 0.9, 'green' => 0.46, 'blue' => 0.0]
                                ]
                            ]
                        ],
                        'fields' => 'fontSize,bold,foregroundColor'
                    ]
                ];
            } elseif ($s['style'] === 'h3') {
                $requests[] = [
                    'updateTextStyle' => [
                        'range' => [
                            'startIndex' => $s['start'],
                            'endIndex' => $s['end']
                        ],
                        'textStyle' => [
                            'fontSize' => ['magnitude' => 12, 'unit' => 'PT'],
                            'bold' => true,
                            'foregroundColor' => [
                                'color' => [
                                    'rgbColor' => ['red' => 0.08, 'green' => 0.38, 'blue' => 0.31]
                                ]
                            ]
                        ],
                        'fields' => 'fontSize,bold,foregroundColor'
                    ]
                ];
            } elseif (in_array($s['style'], ['h4', 'h5', 'h6'])) {
                $requests[] = [
                    'updateTextStyle' => [
                        'range' => [
                            'startIndex' => $s['start'],
                            'endIndex' => $s['end']
                        ],
                        'textStyle' => [
                            'fontSize' => ['magnitude' => 11, 'unit' => 'PT'],
                            'bold' => true,
                            'foregroundColor' => [
                                'color' => [
                                    'rgbColor' => ['red' => 0.2, 'green' => 0.2, 'blue' => 0.2]
                                ]
                            ]
                        ],
                        'fields' => 'fontSize,bold,foregroundColor'
                    ]
                ];
            }
        }

        return $requests;
    }

    public function destroy(LessonPlan $lessonPlan)
    {
        if ($lessonPlan->user_id !== auth()->id()) {
            abort(403, 'Unauthorized.');
        }

        $lessonPlan->delete();

        return redirect()->route('lesson-plans.index')
            ->with('success', 'Lesson plan deleted successfully.');
    }

    public function refine(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'instructions' => 'required|string',
        ]);

        $apiKey = env('GEMINI_API_KEY');
        if (! $apiKey) {
            return response()->json([
                'error' => 'Gemini API Key is not configured in the .env file.',
            ], 500);
        }

        $prompt = "I have this lesson plan:\n\n".
                  "```markdown\n".
                  $request->input('content')."\n".
                  "```\n\n".
                  "I want you to refine and modify it following exactly these instructions:\n".
                  "\"{$request->instructions}\"\n\n".
                  "Guidelines:\n".
                  "1. Maintain the structure and format of the lesson plan (phases, Markdown, bold text, etc.).\n".
                  "2. Keep the references to the educational technology tools from the AINS catalog (e.g. **Canva**, **Kahoot**, etc.) unless explicitly asked to add or remove one.\n".
                  "3. Respond only with the new refined content in Markdown (do not add greetings, extra explanations, or delimiters like ```markdown ... ``` or similar, just the modified lesson plan in English).";

        try {
            $response = Http::timeout(30)->withoutVerifying()->post(
                'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key='.$apiKey,
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
                $markdown = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

                if ($markdown) {
                    if (str_starts_with($markdown, "```markdown")) {
                        $markdown = substr($markdown, 11);
                        if (str_ends_with($markdown, "```")) {
                            $markdown = substr($markdown, 0, -3);
                        }
                    } elseif (str_starts_with($markdown, "```")) {
                        $markdown = substr($markdown, 3);
                        if (str_ends_with($markdown, "```")) {
                            $markdown = substr($markdown, 0, -3);
                        }
                    }
                    $markdown = trim($markdown);

                    return response()->json([
                        'markdown' => $markdown,
                    ]);
                }
            }

            Log::error('Gemini Planner Refinement Error: Status '.$response->status().' - '.$response->body());

            return response()->json([
                'error' => 'Error refining the lesson plan from the AI server.',
            ], 500);

        } catch (\Exception $e) {
            Log::error('Gemini Planner Refinement Exception: '.$e->getMessage());

            return response()->json([
                'error' => 'Connection error with the AI assistant: '.$e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, LessonPlan $lessonPlan)
    {
        if ($lessonPlan->user_id !== auth()->id()) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'subject' => 'sometimes|required|string|max:255',
            'grade_level' => 'sometimes|required|string|max:255',
            'objectives' => 'sometimes|required|string',
            'duration' => 'sometimes|required|string|max:255',
            'content' => 'required|string',
            'selected_tools' => 'nullable|array',
        ]);

        $lessonPlan->update([
            'title' => $request->title ?? $lessonPlan->title,
            'subject' => $request->subject ?? $lessonPlan->subject,
            'grade_level' => $request->grade_level ?? $lessonPlan->grade_level,
            'objectives' => $request->objectives ?? $lessonPlan->objectives,
            'duration' => $request->duration ?? $lessonPlan->duration,
            'content' => $request->input('content'),
            'selected_tools' => $request->selected_tools ?? $lessonPlan->selected_tools ?? [],
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Lesson plan updated successfully.',
                'url' => route('lesson-plans.show', $lessonPlan),
            ]);
        }

        return redirect()->route('lesson-plans.show', $lessonPlan)
            ->with('success', 'Lesson plan updated successfully.');
    }
}
