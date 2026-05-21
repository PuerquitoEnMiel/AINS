<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Tool;
use App\Models\ToolRequest;
use Illuminate\Http\Request;

class ToolRequestController extends Controller
{
    public function index()
    {
        $requests = ToolRequest::latest()->get();

        return view('admin.requests.index', compact('requests'));
    }

    public function approve(ToolRequest $toolRequest)
    {
        $category = Category::where('name', $toolRequest->category)->first();

        // Create tool from request
        $tool = Tool::create([
            'name' => $toolRequest->tool_name,
            'description' => $toolRequest->description,
            'url' => $toolRequest->url,
            'category' => $toolRequest->category,
            'category_id' => $category ? $category->id : null,
            'is_google_workspace' => $toolRequest->is_google_workspace,
            'approval_status' => 'approved',
        ]);

        $toolRequest->update([
            'status' => 'approved',
            'tool_id' => $tool->id,
        ]);

        return back()->with('success', "Herramienta \"{$tool->name}\" aprobada y publicada.");
    }

    public function reject(Request $request, ToolRequest $toolRequest)
    {
        $toolRequest->update([
            'status' => 'rejected',
            'admin_notes' => $request->input('admin_notes'),
        ]);

        return back()->with('info', 'Solicitud rechazada.');
    }
}
