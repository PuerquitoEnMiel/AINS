<?php

namespace App\Http\Controllers;

use App\Models\Tool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * List user's favorite tools.
     */
    public function index()
    {
        $favorites = Auth::user()->favorites()->approved()->with('categoryRelation')->get();
        return view('favorites.index', compact('favorites'));
    }

    /**
     * Toggle favorite status (AJAX).
     */
    public function toggle(Tool $tool)
    {
        $user = Auth::user();
        $exists = $user->favorites()->where('tool_id', $tool->id)->exists();

        if ($exists) {
            $user->favorites()->detach($tool->id);
            return response()->json(['favorited' => false, 'message' => 'Eliminado de favoritos.']);
        }

        $user->favorites()->attach($tool->id);
        return response()->json(['favorited' => true, 'message' => 'Agregado a favoritos.']);
    }
}
