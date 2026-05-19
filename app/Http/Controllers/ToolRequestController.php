<?php

namespace App\Http\Controllers;

use App\Models\ToolRequest;
use Illuminate\Http\Request;

class ToolRequestController extends Controller
{
    public function create()
    {
        return view('requests.create');
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'tool_name'           => 'required|string|max:255',
            'description'         => 'required|string',
            'url'                 => 'required|url',
            'category'            => 'required|string',
            'requester_name'      => 'required|string|max:255',
            'requester_email'     => 'required|email',
        ]);

        $data['is_google_workspace'] = $request->boolean('is_google_workspace');

        ToolRequest::create($data);

        return redirect('/')->with('success', '¡Solicitud enviada! El equipo administrador la revisará pronto.');
    }
}
