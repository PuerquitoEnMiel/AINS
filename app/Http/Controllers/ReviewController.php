<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Tool;
use App\Http\Requests\StoreReviewRequest;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Store or update a review for a tool.
     */
    public function store(StoreReviewRequest $request, Tool $tool)
    {
        // Students and guests cannot review AI Detection tools
        $user = Auth::user();
        if ($tool->categoryRelation?->slug === 'ai-detection') {
            if ($user->isStudent()) {
                return back()->with('error', 'You do not have permission to review this tool.');
            }
        }

        $data = $request->validated();

        Review::updateOrCreate(
            ['user_id' => Auth::id(), 'tool_id' => $tool->id],
            $data
        );

        // Recalculate cached average
        $tool->recalculateRating();

        return back()->with('success', 'Review saved!');
    }
}
