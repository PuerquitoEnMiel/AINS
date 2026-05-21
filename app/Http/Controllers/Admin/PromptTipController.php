<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromptTip;
use Illuminate\Http\Request;

class PromptTipController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $prompts = PromptTip::orderBy('sort_order')->get();
        return view('admin.prompt_tips.index', compact('prompts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.prompt_tips.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'target_role' => 'required|string|in:docentes,estudiantes',
            'category' => 'required|string|max:255',
            'complexity' => 'required|string|max:50',
            'description' => 'required|string',
            'prompt_text' => 'required|string',
            'sort_order' => 'required|integer',
        ]);

        PromptTip::create($validated);

        return redirect()->route('admin.prompt-tips.index')
            ->with('success', 'Prompt EdTech creado exitosamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PromptTip $promptTip)
    {
        $prompt = $promptTip;
        return view('admin.prompt_tips.edit', compact('prompt'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PromptTip $promptTip)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'target_role' => 'required|string|in:docentes,estudiantes',
            'category' => 'required|string|max:255',
            'complexity' => 'required|string|max:50',
            'description' => 'required|string',
            'prompt_text' => 'required|string',
            'sort_order' => 'required|integer',
        ]);

        $promptTip->update($validated);

        return redirect()->route('admin.prompt-tips.index')
            ->with('success', 'Prompt EdTech actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PromptTip $promptTip)
    {
        $promptTip->delete();

        return redirect()->route('admin.prompt-tips.index')
            ->with('success', 'Prompt EdTech eliminado exitosamente.');
    }
}
