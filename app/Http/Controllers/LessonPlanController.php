<?php

namespace App\Http\Controllers;

use App\Models\LessonPlan;
use App\Models\Tool;
use App\Services\GeminiService;
use App\Services\GoogleDocsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LessonPlanController extends Controller
{
    public function __construct(
        protected GeminiService    $gemini,
        protected GoogleDocsService $docs,
    ) {}

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
            'title'      => 'required|string|max:255',
            'subject'    => 'required|string|max:255',
            'grade_level'=> 'required|string|max:255',
            'objectives' => 'required|string',
            'duration'   => 'required|string|max:255',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,jpg,jpeg,png,webp,mp3,wav,mp4,mov|max:51200',
        ]);

        if (! $this->gemini->isConfigured()) {
            return response()->json(['error' => 'Gemini API Key is not configured.'], 500);
        }

        // Build tools context
        $tools       = Tool::approved()->with('categoryRelation')->get();
        $toolsContext = $tools->map(function ($t) {
            $catName = $t->categoryRelation?->name ?? 'Other';
            return "- {$t->name} (Category: {$catName}): {$t->description}";
        })->implode("\n");

        $prompt = "Design a detailed and professional lesson plan for the American Nicaraguan School (ANS).\n\n" .
                  "Class details:\n" .
                  "- Title/Topic: {$request->title}\n" .
                  "- Subject: {$request->subject}\n" .
                  "- Grade Level: {$request->grade_level}\n" .
                  "- Learning Objectives: {$request->objectives}\n" .
                  "- Duration: {$request->duration}\n\n";

        if ($request->hasFile('attachment')) {
            $prompt .= "I have attached a reference document/image. Please analyze the attached content and incorporate details/materials from it into this lesson plan.\n\n";
        }

        $prompt .= "Pedagogical guidelines:\n" .
                   "1. Structure the plan into clear phases: Warm-up, Activities, Wrap-up, and Assessment.\n" .
                   "2. Explicitly integrate one or more of the following educational technology tools from the approved AINS catalog into the class development. Mention the exact name of the tool in bold (e.g. **Canva**, **Kahoot**):\n" .
                   $toolsContext . "\n\n" .
                   "3. Add suggestions for AI prompts or templates applicable for this specific lesson.\n" .
                   "4. Respond in English. Use clean, professional Markdown formatting with subheadings, bulleted lists, and code blocks for templates.";

        $parts = [['text' => $prompt]];

        if ($request->hasFile('attachment')) {
            try {
                $file      = $request->file('attachment');
                $mimeType  = $file->getMimeType();
                $base64Data = base64_encode(file_get_contents($file->getRealPath()));
                $parts[]   = [
                    'inlineData' => [
                        'mimeType' => $mimeType,
                        'data'     => $base64Data,
                    ],
                ];
            } catch (\Exception $e) {
                Log::error('Lesson plan attachment read error: ' . $e->getMessage());
            }
        }

        $markdown = $this->gemini->generate($parts);

        if (! $markdown) {
            return response()->json(['error' => 'Error generating the lesson plan from the AI server.'], 500);
        }

        // Detect which approved tools were mentioned
        $detectedToolIds = $tools
            ->filter(fn ($tool) => stripos($markdown, $tool->name) !== false)
            ->pluck('id')
            ->values()
            ->toArray();

        return response()->json([
            'markdown' => $markdown,
            'tool_ids' => $detectedToolIds,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'          => 'required|string|max:255',
            'subject'        => 'required|string|max:255',
            'grade_level'    => 'required|string|max:255',
            'objectives'     => 'required|string',
            'duration'       => 'required|string|max:255',
            'content'        => 'required|string',
            'selected_tools' => 'nullable|array',
        ]);

        $plan = LessonPlan::create([
            'user_id'        => auth()->id(),
            'title'          => $request->title,
            'subject'        => $request->subject,
            'grade_level'    => $request->grade_level,
            'objectives'     => $request->objectives,
            'duration'       => $request->duration,
            'content'        => $request->input('content'),
            'selected_tools' => $request->selected_tools ?? [],
        ]);

        return response()->json([
            'success' => true,
            'id'      => $plan->id,
            'url'     => route('lesson-plans.show', $plan),
        ]);
    }

    public function show(LessonPlan $lessonPlan)
    {
        $this->authorize('view', $lessonPlan);

        $tools = collect();
        if (! empty($lessonPlan->selected_tools)) {
            $tools = Tool::approved()
                ->whereIn('id', $lessonPlan->selected_tools)
                ->get();
        }

        return view('lesson_plans.show', compact('lessonPlan', 'tools'));
    }

    public function export(LessonPlan $lessonPlan)
    {
        $this->authorize('view', $lessonPlan);

        $token = session('google_access_token');
        if (! $token) {
            return $this->redirectToReauth($lessonPlan);
        }

        try {
            $result = $this->docs->createFromMarkdown($token, $lessonPlan->title, $lessonPlan->content);

            if (isset($result['reauth'])) {
                session()->forget('google_access_token');
                return $this->redirectToReauth($lessonPlan);
            }

            if (! $result['success']) {
                return $this->exportError($lessonPlan, $result['error'] ?? 'Error exporting to Google Docs.');
            }

            if (request()->ajax()) {
                return response()->json(['success' => true, 'url' => $result['url']]);
            }

            return redirect($result['url']);

        } catch (\Exception $e) {
            Log::error('Google Docs Export Exception: ' . $e->getMessage());
            return $this->exportError($lessonPlan, 'Connection error with Google: ' . $e->getMessage());
        }
    }

    public function destroy(LessonPlan $lessonPlan)
    {
        $this->authorize('delete', $lessonPlan);

        $lessonPlan->delete();

        return redirect()->route('lesson-plans.index')
            ->with('success', 'Lesson plan deleted successfully.');
    }

    public function refine(Request $request)
    {
        $request->validate([
            'content'      => 'required|string',
            'instructions' => 'required|string',
        ]);

        if (! $this->gemini->isConfigured()) {
            return response()->json(['error' => 'Gemini API Key is not configured.'], 500);
        }

        $prompt = "I have this lesson plan:\n\n" .
                  "```markdown\n" .
                  $request->input('content') . "\n" .
                  "```\n\n" .
                  "I want you to refine and modify it following exactly these instructions:\n" .
                  "\"{$request->instructions}\"\n\n" .
                  "Guidelines:\n" .
                  "1. Maintain the structure and format of the lesson plan (phases, Markdown, bold text, etc.).\n" .
                  "2. Keep the references to the educational technology tools from the AINS catalog (e.g. **Canva**, **Kahoot**, etc.) unless explicitly asked to add or remove one.\n" .
                  "3. Respond only with the new refined content in Markdown (do not add greetings, extra explanations, or delimiters like ```markdown ... ``` or similar, just the modified lesson plan in English).";

        $markdown = $this->gemini->generate([['text' => $prompt]]);

        if (! $markdown) {
            return response()->json(['error' => 'Error refining the lesson plan from the AI server.'], 500);
        }

        // Strip accidental markdown fences
        $markdown = preg_replace('/^```(?:markdown)?\n?/', '', $markdown);
        $markdown = preg_replace('/\n?```$/', '', $markdown);
        $markdown = trim($markdown);

        return response()->json(['markdown' => $markdown]);
    }

    public function update(Request $request, LessonPlan $lessonPlan)
    {
        $this->authorize('update', $lessonPlan);

        $request->validate([
            'title'          => 'sometimes|required|string|max:255',
            'subject'        => 'sometimes|required|string|max:255',
            'grade_level'    => 'sometimes|required|string|max:255',
            'objectives'     => 'sometimes|required|string',
            'duration'       => 'sometimes|required|string|max:255',
            'content'        => 'required|string',
            'selected_tools' => 'nullable|array',
        ]);

        $lessonPlan->update([
            'title'          => $request->title          ?? $lessonPlan->title,
            'subject'        => $request->subject        ?? $lessonPlan->subject,
            'grade_level'    => $request->grade_level    ?? $lessonPlan->grade_level,
            'objectives'     => $request->objectives     ?? $lessonPlan->objectives,
            'duration'       => $request->duration       ?? $lessonPlan->duration,
            'content'        => $request->input('content'),
            'selected_tools' => $request->selected_tools ?? $lessonPlan->selected_tools ?? [],
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Lesson plan updated successfully.',
                'url'     => route('lesson-plans.show', $lessonPlan),
            ]);
        }

        return redirect()->route('lesson-plans.show', $lessonPlan)
            ->with('success', 'Lesson plan updated successfully.');
    }

    // ── Private Helpers ──────────────────────────────────────────

    protected function redirectToReauth(LessonPlan $lessonPlan)
    {
        session(['google_export_redirect' => route('lesson-plans.export', $lessonPlan)]);

        if (request()->ajax()) {
            return response()->json(['redirect' => route('auth.google.export-authorize')]);
        }

        return redirect()->route('auth.google.export-authorize');
    }

    protected function exportError(LessonPlan $lessonPlan, string $message)
    {
        if (request()->ajax()) {
            return response()->json(['error' => $message], 500);
        }

        return redirect()->route('lesson-plans.show', $lessonPlan)->with('error', $message);
    }
}
