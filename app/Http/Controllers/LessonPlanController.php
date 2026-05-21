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
        $request->validate([
            'title' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'grade_level' => 'required|string|max:255',
            'objectives' => 'required|string',
            'duration' => 'required|string|max:255',
        ]);

        $apiKey = env('GEMINI_API_KEY');
        if (! $apiKey) {
            return response()->json([
                'error' => 'API Key de Gemini no configurada en el archivo .env.',
            ], 500);
        }

        // Get approved tools context
        $tools = Tool::approved()->get(['id', 'name', 'description', 'category']);
        $toolsContext = $tools->map(function ($t) {
            return "- {$t->name} (Categoría: {$t->category}): {$t->description}";
        })->implode("\n");

        $prompt = "Diseña una planeación de clase detallada y profesional para el colegio American Nicaraguan School (ANS).\n\n".
                  "Detalles de la clase:\n".
                  "- Título/Tema: {$request->title}\n".
                  "- Asignatura: {$request->subject}\n".
                  "- Nivel/Grado: {$request->grade_level}\n".
                  "- Objetivos de Aprendizaje: {$request->objectives}\n".
                  "- Duración: {$request->duration}\n\n".
                  "Directrices pedagógicas:\n".
                  "1. Estructura el plan en fases claras: Inicio/Warm-up, Desarrollo/Activities, Cierre/Wrap-up y Evaluación/Assessment.\n".
                  "2. Integra explícitamente una o más de las siguientes herramientas del catálogo de tecnología educativa aprobado en AINS en el desarrollo de la clase. Menciona el nombre exacto de la herramienta en negrita (ej. **Canva**, **Kahoot**):\n".
                  $toolsContext."\n\n".
                  "3. Agrega sugerencias de prompt o plantillas de uso de IA aplicables para esta lección específica.\n".
                  '4. Responde en español (o inglés si el tema lo amerita, por defecto español). Usa formato Markdown limpio y profesional con subtítulos, listas con viñetas y bloques de código para plantillas.';

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

            Log::error('Gemini Planner Error: Status '.$response->status().' - '.$response->body());

            return response()->json([
                'error' => 'Error al generar la planeación de clases desde el servidor de IA.',
            ], 500);

        } catch (\Exception $e) {
            Log::error('Gemini Planner Exception: '.$e->getMessage());

            return response()->json([
                'error' => 'Error de conexión con el asistente de IA: '.$e->getMessage(),
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
            'content' => $request->content,
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
            abort(403, 'No autorizado.');
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
            abort(403, 'No autorizado.');
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
                    return response()->json(['error' => 'Error al crear el documento en Google Docs.'], 500);
                }
                return redirect()->route('lesson-plans.show', $lessonPlan)
                    ->with('error', 'Error al crear el documento en Google Docs.');
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
                return response()->json(['error' => 'Excepción de conexión con Google API: ' . $e->getMessage()], 500);
            }
            return redirect()->route('lesson-plans.show', $lessonPlan)
                ->with('error', 'Error de conexión con Google: ' . $e->getMessage());
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

            if (str_starts_with($line, '# ')) {
                $cleanLine = substr($line, 2);
                $lineStyle = 'h1';
            } elseif (str_starts_with($line, '## ')) {
                $cleanLine = substr($line, 3);
                $lineStyle = 'h2';
            } elseif (str_starts_with($line, '### ')) {
                $cleanLine = substr($line, 4);
                $lineStyle = 'h3';
            } elseif (str_starts_with($line, '- ')) {
                $cleanLine = '• ' . substr($line, 2);
                $lineStyle = 'list';
            } elseif (str_starts_with($line, '* ')) {
                $cleanLine = '• ' . substr($line, 2);
                $lineStyle = 'list';
            }

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
            }
        }

        return $requests;
    }

    public function destroy(LessonPlan $lessonPlan)
    {
        if ($lessonPlan->user_id !== auth()->id()) {
            abort(403, 'No autorizado.');
        }

        $lessonPlan->delete();

        return redirect()->route('lesson-plans.index')
            ->with('success', 'Planificación eliminada exitosamente.');
    }
}
