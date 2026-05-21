<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaskForceMember;
use Illuminate\Http\Request;

class TaskForceMemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $members = TaskForceMember::orderBy('sort_order')->get();
        return view('admin.task_force.index', compact('members'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.task_force.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'description' => 'required|string',
            'initials' => 'required|string|max:3',
            'avatar_color' => 'required|string|max:7', // e.g. #007934
            'image_url' => 'nullable|url|max:255',
            'sort_order' => 'required|integer',
        ]);

        TaskForceMember::create($validated);

        return redirect()->route('admin.task-force.index')
            ->with('success', 'Miembro agregado exitosamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TaskForceMember $taskForce)
    {
        // Laravel's implicit route model binding might use $taskForce based on resource name,
        // but we'll bind it as $taskForce in routes, let's keep name consistent.
        $member = $taskForce;
        return view('admin.task_force.edit', compact('member'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TaskForceMember $taskForce)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'description' => 'required|string',
            'initials' => 'required|string|max:3',
            'avatar_color' => 'required|string|max:7',
            'image_url' => 'nullable|url|max:255',
            'sort_order' => 'required|integer',
        ]);

        $taskForce->update($validated);

        return redirect()->route('admin.task-force.index')
            ->with('success', 'Miembro actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TaskForceMember $taskForce)
    {
        $taskForce->delete();

        return redirect()->route('admin.task-force.index')
            ->with('success', 'Miembro eliminado exitosamente.');
    }
}
