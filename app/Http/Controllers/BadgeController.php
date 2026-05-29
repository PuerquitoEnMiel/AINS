<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use Illuminate\Http\Request;

class BadgeController extends Controller
{
    public function index()
    {
        $badges = Badge::orderByDesc('is_mandatory')->orderBy('sort_order')->get();
        $user = auth()->user();
        
        // Progress metrics
        $totalBadgesCount = $badges->count();
        $earnedBadgesCount = $user ? $user->badges()->count() : 0;
        
        $progressPercent = $totalBadgesCount > 0 
            ? round(($earnedBadgesCount / $totalBadgesCount) * 100) 
            : 0;

        // Build earned badges map keyed by badge_id for expiry status lookup
        $earnedBadgesMap = collect();
        if ($user) {
            $earnedBadgesMap = $user->badges->keyBy('id');
        }

        return view('badges.index', compact(
            'badges', 'earnedBadgesCount', 'totalBadgesCount', 'progressPercent', 'earnedBadgesMap'
        ));
    }

    public function show($slug)
    {
        $badge = Badge::where('slug', $slug)->firstOrFail();
        $user = auth()->user();
        
        $isEarned = $user ? $user->hasBadge($badge->slug) : false;
        return view('badges.show', compact('badge', 'isEarned', 'earnedPivot'));
    }
}
