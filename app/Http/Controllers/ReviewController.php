<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Tool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Store or update a review for a tool.
     */
    public function store(Request $request, Tool $tool)
    {
        // Students and guests cannot review AI Detection tools
        $user = Auth::user();
        if ($tool->categoryRelation?->slug === 'ai-detection') {
            if ($user->isStudent()) {
                return back()->with('error', 'No tienes permiso para reseñar esta herramienta.');
            }
        }

        $data = $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        Review::updateOrCreate(
            ['user_id' => Auth::id(), 'tool_id' => $tool->id],
            $data
        );

        // Recalculate cached average
        $tool->recalculateRating();

        return back()->with('success', '¡Reseña guardada!');
    }
}
