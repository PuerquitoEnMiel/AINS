<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Show user profile page.
     */
    public function show()
    {
        $user = Auth::user()->loadCount(['favorites', 'reviews', 'conversations']);
        $favorites = $user->favorites()->approved()->with('categoryRelation')->latest('tool_user.created_at')->take(6)->get();
        $reviews = $user->reviews()->with('tool')->latest()->take(5)->get();
        $badges = $user->badges()->latest('badge_user.earned_at')->get();

        return view('profile.show', compact('user', 'favorites', 'reviews', 'badges'));
    }

    /**
     * Update profile (bio, department).
     */
    public function update(Request $request)
    {
        $data = $request->validate([
            'department' => 'nullable|string|max:100',
            'bio' => 'nullable|string|max:500',
        ]);

        Auth::user()->update($data);

        return back()->with('success', 'Perfil actualizado.');
    }
}
