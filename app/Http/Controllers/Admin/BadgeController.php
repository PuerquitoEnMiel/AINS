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
            ->orderByDesc('is_mandatory')
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
            'icon_image'             => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'color'                  => 'required|string|size:7',
            'category'               => 'required|string',
            'difficulty'             => 'required|string',
            'sort_order'             => 'required|integer',
            'requires_evidence'      => 'nullable|boolean',
            'certification_url'      => 'nullable|url|max:500',
            'evidence_instructions'  => 'nullable|string|max:1000',
            'validity_days'          => 'nullable|integer|min:1',
            'is_mandatory'           => 'nullable|boolean',
        ]);

        $imagePath = null;
        if ($request->hasFile('icon_image')) {
            $imagePath = $request->file('icon_image')->store('badge_icons', 'public');
        }

        Badge::create([
            'name'                  => $request->name,
            'slug'                  => Str::slug($request->name),
            'description'           => $request->description,
            'icon'                  => $request->icon ?? '🏅',
            'image_path'            => $imagePath,
            'color'                 => $request->color,
            'category'              => $request->category,
            'difficulty'            => $request->difficulty,
            'criteria_type'         => 'manual',
            'sort_order'            => $request->sort_order,
            'requires_evidence'     => true, // manual evidence is the only way
            'certification_url'     => $request->certification_url,
            'evidence_instructions' => $request->evidence_instructions,
            'validity_days'         => $request->validity_days,
            'is_mandatory'          => $request->boolean('is_mandatory'),
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
            'icon_image'             => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'color'                  => 'required|string|size:7',
            'category'               => 'required|string',
            'difficulty'             => 'required|string',
            'sort_order'             => 'required|integer',
            'requires_evidence'      => 'nullable|boolean',
            'certification_url'      => 'nullable|url|max:500',
            'evidence_instructions'  => 'nullable|string|max:1000',
            'validity_days'          => 'nullable|integer|min:1',
            'is_mandatory'           => 'nullable|boolean',
        ]);

        $imagePath = $badge->image_path;
        if ($request->hasFile('icon_image')) {
            $imagePath = $request->file('icon_image')->store('badge_icons', 'public');
        }

        $badge->update([
            'name'                  => $request->name,
            'slug'                  => Str::slug($request->name),
            'description'           => $request->description,
            'icon'                  => $request->icon ?? '🏅',
            'image_path'            => $imagePath,
            'color'                 => $request->color,
            'category'              => $request->category,
            'difficulty'            => $request->difficulty,
            'criteria_type'         => 'manual',
            'sort_order'            => $request->sort_order,
            'requires_evidence'     => true,
            'certification_url'     => $request->certification_url,
            'evidence_instructions' => $request->evidence_instructions,
            'validity_days'         => $request->validity_days,
            'is_mandatory'          => $request->boolean('is_mandatory'),
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
    public function approveEvidence(Request $request, BadgeEvidence $evidence)
    {
        $badge = $evidence->badge;
        
        $certifiedAt = now();
        if ($request->filled('certified_at')) {
            try {
                $certifiedAt = \Carbon\Carbon::parse($request->certified_at);
            } catch (\Exception $e) {
                // Fallback to now if invalid date
            }
        }
        
        $expiresAt = $badge->calculateExpiresAt($certifiedAt);

        $evidence->update([
            'status'      => 'approved',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'expires_at'  => $expiresAt,
        ]);

        // Grant badge via pivot (update or create)
        $evidence->user->badges()->syncWithoutDetaching([
            $badge->id => [
                'earned_at' => $certifiedAt,
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

    /**
     * Show badge suggestions queue.
     */
    public function suggestionsQueue()
    {
        $suggestions = \App\Models\BadgeSuggestion::with('user')
            ->orderByRaw("CASE status WHEN 'pending' THEN 0 WHEN 'approved' THEN 1 ELSE 2 END")
            ->latest()
            ->paginate(15);

        return view('admin.badge_suggestions.index', compact('suggestions'));
    }

    /**
     * Approve a badge suggestion.
     */
    public function approveSuggestion(Request $request, \App\Models\BadgeSuggestion $suggestion)
    {
        $suggestion->update([
            'status' => 'approved',
            'admin_notes' => $request->admin_notes,
        ]);

        // Redirect to create badge pre-filled with suggestion data
        return redirect()->route('admin.badges.create', [
            'name' => $suggestion->name,
            'description' => $suggestion->description,
            'certification_url' => $suggestion->certification_url,
        ])->with('success', 'Suggestion approved. Complete badge creation here.');
    }

    /**
     * Reject a badge suggestion.
     */
    public function rejectSuggestion(Request $request, \App\Models\BadgeSuggestion $suggestion)
    {
        $suggestion->update([
            'status' => 'rejected',
            'admin_notes' => $request->admin_notes,
        ]);

        return back()->with('success', 'Suggestion rejected.');
    }
}
