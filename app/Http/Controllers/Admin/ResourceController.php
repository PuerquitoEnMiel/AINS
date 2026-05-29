<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResourceController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status', 'all'); // all | pending | published | trashed

        $query = Resource::withTrashed()->with('creator')->latest();

        match ($status) {
            'pending'   => $query->whereNull('deleted_at')->where('is_published', false),
            'published' => $query->whereNull('deleted_at')->where('is_published', true),
            'trashed'   => $query->onlyTrashed(),
            default     => $query->whereNull('deleted_at'),
        };

        $resources    = $query->paginate(20)->withQueryString();
        $pendingCount = Resource::where('is_published', false)->count();

        return view('admin.resources.index', compact('resources', 'status', 'pendingCount'));
    }

    public function create()
    {
        return view('admin.resources.form', ['resource' => new Resource()]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateResource($request);
        $validated['created_by']   = Auth::id();
        $validated['is_published'] = $request->boolean('is_published');

        Resource::create($validated);

        return redirect()->route('admin.resources.index')->with('success', 'Resource created successfully.');
    }

    public function edit(Resource $resource)
    {
        return view('admin.resources.form', compact('resource'));
    }

    public function update(Request $request, Resource $resource)
    {
        $validated = $this->validateResource($request);
        $validated['is_published'] = $request->boolean('is_published');

        $resource->update($validated);

        return redirect()->route('admin.resources.index')->with('success', 'Resource updated.');
    }

    public function destroy(Resource $resource)
    {
        $resource->delete();
        return back()->with('success', 'Resource deleted.');
    }

    public function approve(Resource $resource)
    {
        $resource->update(['is_published' => true]);
        return back()->with('success', "Resource \"{$resource->title}\" approved and published.");
    }

    // ── Private ───────────────────────────────────────────────────

    private function validateResource(Request $request): array
    {
        return $request->validate([
            'title'          => 'required|string|max:200',
            'description'    => 'nullable|string|max:1000',
            'url'            => 'nullable|url|max:500',
            'type'           => 'required|in:link,book,video,article,tool,course',
            'area'           => 'required|in:stem,innovation,ai,robotics,design,programming,math,science,general',
            'target_roles'   => 'required|array',
            'target_roles.*' => 'in:student,teacher,all',
            'thumbnail_url'  => 'nullable|url|max:500',
            'author'         => 'nullable|string|max:150',
            'source'         => 'nullable|string|max:150',
        ]);
    }
}
