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
            'sort_order'             => 'required|integer',
            'requires_evidence'      => 'nullable|boolean',
            'certification_url'      => 'nullable|url|max:500',
            'evidence_instructions'  => 'nullable|string|max:1000',
            'validity_days'          => 'nullable|integer|min:1',
        ]);

        Badge::create([
            'name'                  => $request->name,
            'slug'                  => Str::slug($request->name),
            'description'           => $request->description,
            'icon'                  => $request->icon ?? '🏅',
            'color'                 => $request->color,
            'category'              => $request->category,
            'difficulty'            => $request->difficulty,
            'criteria_type'         => 'manual',
            'sort_order'            => $request->sort_order,
            'requires_evidence'     => true, // manual evidence is the only way
            'certification_url'     => $request->certification_url,
            'evidence_instructions' => $request->evidence_instructions,
            'validity_days'         => $request->validity_days,
        ]);

        return redirect()->route('admin.badges.index')
            ->with('success', 'Badge created successfully.');
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
            'sort_order'             => 'required|integer',
            'requires_evidence'      => 'nullable|boolean',
            'certification_url'      => 'nullable|url|max:500',
            'evidence_instructions'  => 'nullable|string|max:1000',
            'validity_days'          => 'nullable|integer|min:1',
        ]);

        $badge->update([
            'name'                  => $request->name,
            'slug'                  => Str::slug($request->name),
            'description'           => $request->description,
            'icon'                  => $request->icon ?? '🏅',
            'color'                 => $request->color,
            'category'              => $request->category,
            'difficulty'            => $request->difficulty,
            'criteria_type'         => 'manual',
            'sort_order'            => $request->sort_order,
            'requires_evidence'     => true,
            'certification_url'     => $request->certification_url,
            'evidence_instructions' => $request->evidence_instructions,
            'validity_days'         => $request->validity_days,
        ]);

        return redirect()->route('admin.badges.index')
            ->with('success', 'Badge updated successfully.');
    }

    public function destroy(Badge $badge)
    {
        $badge->delete();
        return redirect()->route('admin.badges.index')
            ->with('success', 'Badge deleted successfully.');
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
        $expiresAt = $badge->calculateExpiresAt(now());

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

        return back()->with('success', "Evidence approved. Badge \"{$badge->name}\" granted to {$evidence->user->name}.");
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

        return back()->with('success', "Evidence rejected. Teacher has been notified.");
    }

    public function generateQuizWithAI(Badge $badge)
    {
        $quizController = new QuizController();
        $response = $quizController->generateQuiz($badge);

        $data = json_decode($response->getContent(), true);

        if (isset($data['success']) && $data['success']) {
            return redirect()->route('admin.badges.index')
                ->with('success', "Quiz for '{$badge->name}' successfully generated by AI.");
        }

        return redirect()->route('admin.badges.index')
            ->with('error', 'Failed to generate quiz with AI: ' . ($data['error'] ?? 'Unknown'));
    }
}
