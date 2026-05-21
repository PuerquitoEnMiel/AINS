<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\BadgeEvidence;
use App\Models\Quiz;
use App\Http\Controllers\QuizController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BadgeController extends Controller
{
    public function index()
    {
        $badges = Badge::with('quiz')
            ->withCount('evidences')
            ->orderBy('sort_order')
            ->get();

        $pendingCount = BadgeEvidence::where('status', 'pending')->count();

        return view('admin.badges.index', compact('badges', 'pendingCount'));
    }

    public function create()
    {
        return view('admin.badges.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'                   => 'required|string|max:255',
            'description'            => 'required|string',
            'icon'                   => 'nullable|string|max:255',
            'color'                  => 'required|string|size:7',
            'category'               => 'required|string',
            'difficulty'             => 'required|string',
            'criteria_type'          => 'required|string',
            'sort_order'             => 'required|integer',
            'requires_evidence'      => 'nullable|boolean',
            'certification_url'      => 'nullable|url|max:500',
            'evidence_instructions'  => 'nullable|string|max:1000',
            'expires_in_days'        => 'nullable|integer|min:1',
        ]);

        Badge::create([
            'name'                  => $request->name,
            'slug'                  => Str::slug($request->name),
            'description'           => $request->description,
            'icon'                  => $request->icon ?? '🏅',
            'color'                 => $request->color,
            'category'              => $request->category,
            'difficulty'            => $request->difficulty,
            'criteria_type'         => $request->criteria_type,
            'sort_order'            => $request->sort_order,
            'requires_evidence'     => $request->boolean('requires_evidence'),
            'certification_url'     => $request->certification_url,
            'evidence_instructions' => $request->evidence_instructions,
            'expires_in_days'       => $request->expires_in_days ?: null,
        ]);

        return redirect()->route('admin.badges.index')
            ->with('success', 'Insignia creada exitosamente.');
    }

    public function edit(Badge $badge)
    {
        return view('admin.badges.edit', compact('badge'));
    }

    public function update(Request $request, Badge $badge)
    {
        $request->validate([
            'name'                   => 'required|string|max:255',
            'description'            => 'required|string',
            'icon'                   => 'nullable|string|max:255',
            'color'                  => 'required|string|size:7',
            'category'               => 'required|string',
            'difficulty'             => 'required|string',
            'criteria_type'          => 'required|string',
            'sort_order'             => 'required|integer',
            'requires_evidence'      => 'nullable|boolean',
            'certification_url'      => 'nullable|url|max:500',
            'evidence_instructions'  => 'nullable|string|max:1000',
            'expires_in_days'        => 'nullable|integer|min:1',
        ]);

        $badge->update([
            'name'                  => $request->name,
            'slug'                  => Str::slug($request->name),
            'description'           => $request->description,
            'icon'                  => $request->icon ?? '🏅',
            'color'                 => $request->color,
            'category'              => $request->category,
            'difficulty'            => $request->difficulty,
            'criteria_type'         => $request->criteria_type,
            'sort_order'            => $request->sort_order,
            'requires_evidence'     => $request->boolean('requires_evidence'),
            'certification_url'     => $request->certification_url,
            'evidence_instructions' => $request->evidence_instructions,
            'expires_in_days'       => $request->expires_in_days ?: null,
        ]);

        return redirect()->route('admin.badges.index')
            ->with('success', 'Insignia actualizada exitosamente.');
    }

    public function destroy(Badge $badge)
    {
        $badge->delete();
        return redirect()->route('admin.badges.index')
            ->with('success', 'Insignia eliminada exitosamente.');
    }

    /**
     * Show evidence queue for admin review.
     */
    public function evidenceQueue()
    {
        $evidences = BadgeEvidence::with(['user', 'badge', 'reviewer'])
            ->orderByRaw("CASE status WHEN 'pending' THEN 0 WHEN 'approved' THEN 1 ELSE 2 END")
            ->latest()
            ->paginate(15);

        return view('admin.badge_evidence.index', compact('evidences'));
    }

    /**
     * Approve a badge evidence submission and grant the badge.
     */
    public function approveEvidence(BadgeEvidence $evidence)
    {
        $badge = $evidence->badge;
        $expiresAt = $badge->expires_in_days
            ? now()->addDays($badge->expires_in_days)
            : null;

        $evidence->update([
            'status'      => 'approved',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'expires_at'  => $expiresAt,
        ]);

        // Grant badge via pivot (update or create)
        $evidence->user->badges()->syncWithoutDetaching([
            $badge->id => [
                'earned_at' => now(),
                'score'     => 100,
            ],
        ]);

        return back()->with('success', "Evidencia aprobada. Insignia \"{$badge->name}\" otorgada a {$evidence->user->name}.");
    }

    /**
     * Reject a badge evidence submission.
     */
    public function rejectEvidence(Request $request, BadgeEvidence $evidence)
    {
        $evidence->update([
            'status'      => 'rejected',
            'admin_notes' => $request->admin_notes,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', "Evidencia rechazada. Se notificó al docente.");
    }

    public function generateQuizWithAI(Badge $badge)
    {
        $quizController = new QuizController();
        $response = $quizController->generateQuiz($badge);

        $data = json_decode($response->getContent(), true);

        if (isset($data['success']) && $data['success']) {
            return redirect()->route('admin.badges.index')
                ->with('success', "Cuestionario para '{$badge->name}' generado exitosamente por IA.");
        }

        return redirect()->route('admin.badges.index')
            ->with('error', 'Error al generar cuestionario con IA: ' . ($data['error'] ?? 'Desconocido'));
    }
}
