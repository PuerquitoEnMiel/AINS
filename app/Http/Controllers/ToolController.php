<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Tool;
use Illuminate\Support\Facades\Auth;

class ToolController extends Controller
{
    /**
     * Show tool detail page.
     */
    public function show(Tool $tool)
    {
        // Guard: AI Detection tools are teacher/admin only
        if ($tool->categoryRelation && $tool->categoryRelation->slug === 'ai-detection') {
            if (! Auth::check() || (Auth::user()->isStudent())) {
                abort(403, 'Acceso restringido. Esta herramienta es exclusiva para docentes y administradores.');
            }
        }

        // Track click/view once per session to prevent metrics inflation
        $viewed = session()->get("viewed_tool_{$tool->id}");
        if (!$viewed) {
            $tool->trackClick(Auth::id(), request()->ip());
            session()->put("viewed_tool_{$tool->id}", true);
        }

        // Load relationships
        $tool->load(['categoryRelation', 'creator', 'reviews.user', 'insight']);
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

    /**
     * Dedicated AI Detection hub — teacher/admin only.
     * (Route also protected by is_teacher_or_admin middleware.)
     */
    public function aiDetection()
    {
        $category = Category::where('slug', 'ai-detection')->first();

        $tools = $category
            ? Tool::approved()->where('category_id', $category->id)->get()
            : collect();

        return view('tools.ai_detection', compact('tools', 'category'));
    }
}
