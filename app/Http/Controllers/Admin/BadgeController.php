<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\Quiz;
use App\Http\Controllers\QuizController;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BadgeController extends Controller
{
    public function index()
    {
        $badges = Badge::with('quiz')->orderBy('sort_order')->get();
        return view('admin.badges.index', compact('badges'));
    }

    public function create()
    {
        return view('admin.badges.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'icon' => 'nullable|string|max:255',
            'color' => 'required|string|size:7',
            'category' => 'required|string',
            'difficulty' => 'required|string',
            'criteria_type' => 'required|string',
            'sort_order' => 'required|integer',
        ]);

        $badge = Badge::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'icon' => $request->icon ?? '🏅',
            'color' => $request->color,
            'category' => $request->category,
            'difficulty' => $request->difficulty,
            'criteria_type' => $request->criteria_type,
            'sort_order' => $request->sort_order,
        ]);

        return redirect()->route('admin.badges.index')
            ->with('success', 'Insignia creada exitosamente.');
    }

    public function edit(Badge $badge)
    {
        return view('admin.badges.edit', compact('badge'));
    }

    public function update(Request $request, Badge $badge)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'icon' => 'nullable|string|max:255',
            'color' => 'required|string|size:7',
            'category' => 'required|string',
            'difficulty' => 'required|string',
            'criteria_type' => 'required|string',
            'sort_order' => 'required|integer',
        ]);

        $badge->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'icon' => $request->icon ?? '🏅',
            'color' => $request->color,
            'category' => $request->category,
            'difficulty' => $request->difficulty,
            'criteria_type' => $request->criteria_type,
            'sort_order' => $request->sort_order,
        ]);

        return redirect()->route('admin.badges.index')
            ->with('success', 'Insignia actualizada exitosamente.');
    }

    public function destroy(Badge $badge)
    {
        $badge->delete();
        return redirect()->route('admin.badges.index')
            ->with('success', 'Insignia eliminada exitosamente.');
    }

    public function generateQuizWithAI(Badge $badge)
    {
        $quizController = new QuizController();
        $response = $quizController->generateQuiz($badge);
        
        $data = json_decode($response->getContent(), true);
        
        if (isset($data['success']) && $data['success']) {
            return redirect()->route('admin.badges.index')
                ->with('success', "Cuestionario para '{$badge->name}' generado exitosamente por IA.");
        }
        
        return redirect()->route('admin.badges.index')
            ->with('error', 'Error al generar cuestionario con IA: ' . ($data['error'] ?? 'Desconocido'));
    }
}
