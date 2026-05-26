<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('approvedTools')->orderBy('sort_order')->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'icon' => 'nullable|string|max:10',
            'color' => 'required|string|max:7',
            'sort_order' => 'nullable|integer',
        ]);

        $data['slug'] = Str::slug($data['name']);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        Category::create($data);

        Cache::forget('welcome_categories');

        return redirect()->route('admin.categories.index')
            ->with('success', "Category \"{$data['name']}\" created.");
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'icon' => 'nullable|string|max:10',
            'color' => 'required|string|max:7',
            'sort_order' => 'nullable|integer',
        ]);

        $data['slug'] = Str::slug($data['name']);
        $category->update($data);

        Cache::forget('welcome_categories');

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated.');
    }

    public function destroy(Category $category)
    {
        $name = $category->name;
        $category->delete();

        Cache::forget('welcome_categories');

        return redirect()->route('admin.categories.index')
            ->with('success', "Category \"{$name}\" deleted.");
    }
}
