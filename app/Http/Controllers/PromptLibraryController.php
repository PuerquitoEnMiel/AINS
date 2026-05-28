<?php

namespace App\Http\Controllers;

use App\Models\PromptTip;
use App\Models\PromptVote;
use App\Models\PromptComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PromptLibraryController extends Controller
{
    /**
     * Show the community prompt library.
     * (Moved from route closure in web.php)
     */
    public function index(\Illuminate\Http\Request $request)
    {
        $userId  = auth()->id();
        $isAdmin = auth()->check() && auth()->user()->isAdmin();
        $tab     = $request->input('tab', 'docentes');

        if (!in_array($tab, ['docentes', 'estudiantes', 'comunidad'])) {
            $tab = 'docentes';
        }

        $query = PromptTip::with(['author', 'comments.user', 'votes'])
            ->when(! $isAdmin, function ($q) use ($userId) {
                $q->where(function ($sub) use ($userId) {
                    $sub->where('is_approved', true);
                    if ($userId) {
                        $sub->orWhere('user_id', $userId);
                    }
                });
            });

        if ($tab === 'comunidad') {
            $query->where('is_community', true);
        } else {
            $query->where('target_role', $tab)->where('is_community', false);
        }

        $prompts = $query->orderByDesc('is_approved')  // pending at top for admin
            ->orderBy('sort_order')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('tips', compact('prompts', 'tab'));
    }

    /**
     * Cast or update a vote on a prompt.
     */
    public function vote(Request $request, PromptTip $prompt)
    {
        $request->validate([
            'type' => 'required|in:upvote,downvote',
        ]);

        $userId = Auth::id();
        $type = $request->type;

        $existing = PromptVote::where('user_id', $userId)
            ->where('prompt_tip_id', $prompt->id)
            ->first();

        if ($existing) {
            if ($existing->type === $type) {
                // Clicking same vote removes it
                $existing->delete();
                $voted = null;
            } else {
                // Changing vote type
                $existing->update(['type' => $type]);
                $voted = $type;
            }
        } else {
            // New vote
            PromptVote::create([
                'user_id' => $userId,
                'prompt_tip_id' => $prompt->id,
                'type' => $type,
            ]);
            $voted = $type;
        }

        return response()->json([
            'success' => true,
            'voted' => $voted,
            'score' => $prompt->voteCount(),
        ]);
    }

    /**
     * Add a comment to a prompt.
     */
    public function comment(Request $request, PromptTip $prompt)
    {
        $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        $comment = PromptComment::create([
            'user_id' => Auth::id(),
            'prompt_tip_id' => $prompt->id,
            'body' => $request->body,
        ]);

        return response()->json([
            'success' => true,
            'comment' => [
                'id' => $comment->id,
                'user_name' => Auth::user()->name,
                'user_avatar' => Auth::user()->avatar ?: null,
                'body' => e($comment->body),
                'created_at' => $comment->created_at->diffForHumans(),
            ]
        ]);
    }

    /**
     * Submit a prompt to the community.
     */
    public function submit(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'target_role' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'complexity' => 'required|in:Básico,Intermedio,Avanzado,Basic,Intermediate,Advanced',
            'description' => 'required|string|max:1000',
            'prompt_text' => 'required|string',
        ]);

        PromptTip::create([
            'title' => $request->title,
            'target_role' => $request->target_role,
            'category' => $request->category,
            'complexity' => $request->complexity,
            'description' => $request->description,
            'prompt_text' => $request->prompt_text,
            'user_id' => Auth::id(),
            'is_community' => true,
            'is_approved' => false, // Community prompts require approval
            'sort_order' => 99,
        ]);

        return redirect()->back()->with('success', 'Prompt submitted successfully. It will be visible after administrator approval.');
    }

    /**
     * Increment usage/copy count of a prompt.
     */
    public function trackCopy(PromptTip $prompt)
    {
        $prompt->increment('usage_count');
        return response()->json(['success' => true, 'usage_count' => $prompt->usage_count]);
    }

    /**
     * Admin: approve or reject a community prompt.
     */
    public function toggleApproval(PromptTip $prompt)
    {
        $this->authorizeAdmin();

        $prompt->update(['is_approved' => !$prompt->is_approved]);

        $status = $prompt->is_approved ? 'approved' : 'disapproved';
        return redirect()->back()->with('success', "Prompt {$status} successfully.");
    }

    private function authorizeAdmin()
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }
    }
}
