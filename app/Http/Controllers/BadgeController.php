<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use Illuminate\Http\Request;

class BadgeController extends Controller
{
    public function index()
    {
        $badges = Badge::orderBy('sort_order')->get();
        $user = auth()->user();
        
        // Progress metrics
        $totalBadgesCount = $badges->count();
        $earnedBadgesCount = $user ? $user->badges()->count() : 0;
        
        $progressPercent = $totalBadgesCount > 0 
            ? round(($earnedBadgesCount / $totalBadgesCount) * 100) 
            : 0;

        return view('badges.index', compact('badges', 'earnedBadgesCount', 'totalBadgesCount', 'progressPercent'));
    }

    public function show($slug)
    {
        $badge = Badge::where('slug', $slug)->firstOrFail();
        $user = auth()->user();
        
        $isEarned = $user ? $user->hasBadge($badge->slug) : false;
        $earnedPivot = $isEarned ? $user->badges()->where('slug', $badge->slug)->first()->pivot : null;
        
        $quiz = $badge->quiz;
        
        return view('badges.show', compact('badge', 'isEarned', 'earnedPivot', 'quiz'));
    }
}
