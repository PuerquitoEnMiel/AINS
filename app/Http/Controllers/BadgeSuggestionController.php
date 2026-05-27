<?php

namespace App\Http\Controllers;

use App\Models\BadgeSuggestion;
use Illuminate\Http\Request;

class BadgeSuggestionController extends Controller
{
    public function index()
    {
        $suggestions = auth()->user()->badgeSuggestions()->latest()->get();

        return view('badges.suggestions_index', compact('suggestions'));
    }

    public function create()
    {
        return view('badges.suggest');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'              => 'required|string|max:255',
            'description'       => 'required|string|max:1000',
            'certification_url' => 'nullable|url|max:500',
        ]);

        $suggestion = auth()->user()->badgeSuggestions()->create([
            'name'              => $request->name,
            'description'       => $request->description,
            'certification_url' => $request->certification_url,
            'status'            => 'pending',
        ]);

        // Notify admins
        \App\Models\AdminNotification::create([
            'user_id' => auth()->id(),
            'title' => 'Nueva Sugerencia de Insignia',
            'message' => 'El docente ' . auth()->user()->name . ' ha sugerido una nueva insignia: "' . $request->name . '".',
            'type' => 'suggestion',
            'data' => [
                'suggestion_id' => $suggestion->id,
                'suggestion_name' => $suggestion->name,
            ],
        ]);

        return redirect()->route('badge-suggestions.index')
            ->with('success', '¡Tu sugerencia de insignia ha sido enviada con éxito! La administración la revisará.');
    }
}
