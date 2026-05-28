<?php

namespace App\Http\Controllers;

use App\Models\TaskForceMember;

class TaskForceController extends Controller
{
    public function index()
    {
        $members = TaskForceMember::orderBy('sort_order')->get();

        return view('task_force', compact('members'));
    }
}
