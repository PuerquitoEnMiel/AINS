<?php

namespace App\Http\Controllers;

use App\Models\Tool;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * List user's favorite tools (excluding ai-detection for students).
     */
    public function index()
    {
        $user = Auth::user();
        $favorites = $user->favorites()->approved()->with('categoryRelation')->get();

        // Students cannot see AI Detection tools even in favorites
        if ($user->isStudent()) {
            $favorites = $favorites->filter(function ($tool) {
                return $tool->categoryRelation?->slug !== 'ai-detection';
            })->values();
        }

        return view('favorites.index', compact('favorites'));
    }

    /**
     * Toggle favorite status (AJAX).
     */
    public function toggle(Tool $tool)
    {
        $user = Auth::user();

        // Block students from favoriting AI Detection tools
        if ($user->isStudent() && $tool->categoryRelation?->slug === 'ai-detection') {
            return response()->json(['error' => 'You do not have permission to save this tool.'], 403);
        }

        $exists = $user->favorites()->where('tool_id', $tool->id)->exists();

        if ($exists) {
            $user->favorites()->detach($tool->id);
            return response()->json(['favorited' => false, 'message' => 'Removed from favorites.']);
        }

        $user->favorites()->attach($tool->id);
        return response()->json(['favorited' => true, 'message' => 'Added to favorites.']);
    }
}
