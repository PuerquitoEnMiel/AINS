<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ToolRequest;
use Illuminate\Http\Request;

class ToolRequestController extends Controller
{
    public function create()
    {
        $categories = Category::orderBy('sort_order')->get();

        return view('requests.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tool_name' => 'required|string|max:255',
            'description' => 'required|string',
            'url' => 'required|url',
            'category' => 'required|string|exists:categories,name',
            'requester_name' => 'required|string|max:255',
            'requester_email' => 'required|email',
        ]);

        $data['is_google_workspace'] = $request->boolean('is_google_workspace');

        ToolRequest::create($data);

        return redirect('/')->with('success', '¡Solicitud enviada! El equipo administrador la revisará pronto.');
    }
}
