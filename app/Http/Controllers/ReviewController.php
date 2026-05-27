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

        $review = Review::updateOrCreate(
            ['user_id' => Auth::id(), 'tool_id' => $tool->id],
            $data
        );

        // Recalculate cached average
        $tool->recalculateRating();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Review saved!',
                'review' => $review->load('user'),
                'avg_rating' => $tool->avg_rating,
                'reviews_count' => $tool->reviews()->count(),
            ]);
        }

        return back()->with('success', 'Review saved!');
    }
}
