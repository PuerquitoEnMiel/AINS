<?php

namespace App\Http\Controllers;

use App\Models\Tool;
use Illuminate\Support\Facades\Auth;

class ToolController extends Controller
{
    /**
     * Show tool detail page.
     */
    public function show(Tool $tool)
    {
        // Track click/view
        $tool->trackClick(Auth::id(), request()->ip());

        // Load relationships
        $tool->load(['categoryRelation', 'creator', 'reviews.user']);
        $tool->loadCount(['favoritedBy', 'views']);

        // Check if current user favorited this tool
        $isFavorited = Auth::check()
            ? Auth::user()->favorites()->where('tool_id', $tool->id)->exists()
            : false;

        // User's existing review (if any)
        $userReview = Auth::check()
            ? $tool->reviews()->where('user_id', Auth::id())->first()
            : null;

        // Related tools (same category, exclude current)
        $relatedTools = Tool::approved()
            ->where('category_id', $tool->category_id)
            ->where('id', '!=', $tool->id)
            ->take(4)
            ->get();

        return view('tools.show', compact('tool', 'isFavorited', 'userReview', 'relatedTools'));
    }
}
