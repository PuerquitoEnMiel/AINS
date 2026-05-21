<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Tool;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ToolController extends Controller
{
    public function index()
    {
        $tools = Tool::latest()->paginate(20);

        return view('admin.tools.index', compact('tools'));
    }

    public function create()
    {
        $categories = Category::orderBy('sort_order')->get();

        return view('admin.tools.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'required|string|max:500',
            'url' => 'required|url',
            'category_id' => 'required|exists:categories,id',
            'is_google_workspace' => 'nullable|boolean',
            'is_official' => 'nullable|boolean',
            'approval_status' => 'required|in:approved,pending',
            'logo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $disk = config('filesystems.default', 'public');
            if ($disk === 'local') {
                $disk = 'public';
            }
            $path = $request->file('logo')->store('tool-logos', $disk);
            /** @var FilesystemAdapter $storageDisk */
            $storageDisk = Storage::disk($disk);
            $data['logo_url'] = $storageDisk->url($path);
        }

        $data['is_google_workspace'] = $request->boolean('is_google_workspace');
        $data['is_official'] = $request->boolean('is_official');
        $category = Category::find($request->category_id);
        $data['category'] = $category->name;
        $data['category_id'] = $category->id;

        Tool::create($data);

        return redirect()->route('admin.tools.index')
            ->with('success', "Tool \"{$data['name']}\" created successfully.");
    }

    public function edit(Tool $tool)
    {
        $categories = Category::orderBy('sort_order')->get();

        return view('admin.tools.edit', compact('tool', 'categories'));
    }

    public function update(Request $request, Tool $tool)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'required|string|max:500',
            'url' => 'required|url',
            'category_id' => 'required|exists:categories,id',
            'is_google_workspace' => 'nullable|boolean',
            'is_official' => 'nullable|boolean',
            'approval_status' => 'required|in:approved,pending',
            'logo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($tool->logo_url && ! str_starts_with($tool->logo_url, 'http')) {
                // If local path, try to parse the path after /storage/
                $oldPath = str_replace('/storage/', '', $tool->logo_url);
                Storage::disk('public')->delete($oldPath);
            } elseif ($tool->logo_url && str_contains($tool->logo_url, 'storage.googleapis.com')) {
                // If GCS path, extract path
                $oldPath = parse_url($tool->logo_url, PHP_URL_PATH);
                $oldPath = ltrim($oldPath, '/'.config('filesystems.disks.gcs.bucket').'/');
                Storage::disk('gcs')->delete($oldPath);
            }

            $disk = config('filesystems.default', 'public');
            if ($disk === 'local') {
                $disk = 'public';
            }
            $path = $request->file('logo')->store('tool-logos', $disk);
            /** @var FilesystemAdapter $storageDisk */
            $storageDisk = Storage::disk($disk);
            $data['logo_url'] = $storageDisk->url($path);
        }

        $data['is_google_workspace'] = $request->boolean('is_google_workspace');
        $data['is_official'] = $request->boolean('is_official');
        $category = Category::find($request->category_id);
        $data['category'] = $category->name;
        $data['category_id'] = $category->id;

        $tool->update($data);

        return redirect()->route('admin.tools.index')
            ->with('success', "Tool \"{$tool->name}\" updated successfully.");
    }

    public function destroy(Tool $tool)
    {
        if ($tool->logo_url && ! str_starts_with($tool->logo_url, 'http')) {
            $oldPath = str_replace('/storage/', '', $tool->logo_url);
            Storage::disk('public')->delete($oldPath);
        } elseif ($tool->logo_url && str_contains($tool->logo_url, 'storage.googleapis.com')) {
            $oldPath = parse_url($tool->logo_url, PHP_URL_PATH);
            $oldPath = ltrim($oldPath, '/'.config('filesystems.disks.gcs.bucket').'/');
            Storage::disk('gcs')->delete($oldPath);
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
            'status' => $tool->approval_status,
            'message' => "Tool status updated to {$tool->approval_status}.",
        ]);
    }
}
