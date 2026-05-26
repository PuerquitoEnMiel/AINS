<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ToolRequest;
use App\Http\Requests\StoreToolRequestRequest;

class ToolRequestController extends Controller
{
    public function create()
    {
        $categories = Category::orderBy('sort_order')->get();

        return view('requests.create', compact('categories'));
    }

    public function store(StoreToolRequestRequest $request)
    {
        $data = $request->validated();

        $data['is_google_workspace'] = $request->boolean('is_google_workspace');

        ToolRequest::create($data);

        return redirect('/')->with('success', 'Request submitted! The administration team will review it soon.');
    }
}
