<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tool;
use Illuminate\Http\Request;

class ToolController extends Controller
{
    public function index()
    {
        $tools = Tool::latest()->paginate(20);
        return view('admin.tools.index', compact('tools'));
    }

    public function create()
    {
        return view('admin.tools.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'                => 'required|string|max:100',
            'description'         => 'required|string|max:500',
            'url'                 => 'required|url',
            'category'            => 'required|string',
            'is_google_workspace' => 'nullable|boolean',
            'approval_status'     => 'required|in:approved,pending',
            'logo'                => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $dir = public_path('tool-logos');
            if (!is_dir($dir)) mkdir($dir, 0775, true);
            $filename = uniqid('tool_') . '.' . $request->file('logo')->getClientOriginalExtension();
            $request->file('logo')->move($dir, $filename);
            $data['logo_url'] = 'tool-logos/' . $filename;
        }

        $data['is_google_workspace'] = $request->boolean('is_google_workspace');
        Tool::create($data);

        return redirect()->route('admin.tools.index')
            ->with('success', "Tool \"{$data['name']}\" created successfully.");
    }

    public function edit(Tool $tool)
    {
        return view('admin.tools.edit', compact('tool'));
    }

    public function update(Request $request, Tool $tool)
    {
        $data = $request->validate([
            'name'                => 'required|string|max:100',
            'description'         => 'required|string|max:500',
            'url'                 => 'required|url',
            'category'            => 'required|string',
            'is_google_workspace' => 'nullable|boolean',
            'approval_status'     => 'required|in:approved,pending',
            'logo'                => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            // Delete old logo if stored locally
            if ($tool->logo_url && !str_starts_with($tool->logo_url, 'http')) {
                $old = public_path($tool->logo_url);
                if (file_exists($old)) unlink($old);
            }
            $dir = public_path('tool-logos');
            if (!is_dir($dir)) mkdir($dir, 0775, true);
            $filename = uniqid('tool_') . '.' . $request->file('logo')->getClientOriginalExtension();
            $request->file('logo')->move($dir, $filename);
            $data['logo_url'] = 'tool-logos/' . $filename;
        }

        $data['is_google_workspace'] = $request->boolean('is_google_workspace');
        $tool->update($data);

        return redirect()->route('admin.tools.index')
            ->with('success', "Tool \"{$tool->name}\" updated successfully.");
    }

    public function destroy(Tool $tool)
    {
        if ($tool->logo_url && !str_starts_with($tool->logo_url, 'http')) {
            $path = public_path($tool->logo_url);
            if (file_exists($path)) unlink($path);
        }
        $name = $tool->name;
        $tool->delete();

        return redirect()->route('admin.tools.index')
            ->with('success', "Tool \"{$name}\" deleted.");
    }

    public function toggleStatus(Tool $tool)
    {
        $tool->approval_status = $tool->approval_status === 'approved' ? 'pending' : 'approved';
        $tool->save();

        return response()->json([
            'success' => true,
            'status'  => $tool->approval_status,
            'message' => "Tool status updated to {$tool->approval_status}."
        ]);
    }
}
