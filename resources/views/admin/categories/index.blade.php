@extends('layouts.app')

@section('header-title', 'Categories Management')
@section('header-subtitle', 'Manage tool categories')

@section('content')

@if(session('success'))
<div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700 font-medium">✅ {{ session('success') }}</div>
@endif

<div class="flex items-center justify-between mb-6">
    <h3 class="text-lg font-heading font-bold text-gray-800">All Categories</h3>
    <a href="{{ route('admin.categories.create') }}" class="px-5 py-2.5 bg-ans-dark-green text-white rounded-xl font-semibold text-sm hover:bg-ans-seal-green transition-all shadow-md">+ New Category</a>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="text-left text-xs text-gray-500 uppercase tracking-wider bg-gray-50 border-b border-gray-100">
                <th class="px-6 py-4">Icon</th>
                <th class="px-6 py-4">Name</th>
                <th class="px-6 py-4">Slug</th>
                <th class="px-6 py-4">Color</th>
                <th class="px-6 py-4">Tools</th>
                <th class="px-6 py-4">Order</th>
                <th class="px-6 py-4 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @foreach($categories as $cat)
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 text-xl">{{ $cat->icon }}</td>
                <td class="px-6 py-4 font-semibold text-gray-800">{{ $cat->name }}</td>
                <td class="px-6 py-4 text-gray-500 font-mono text-xs">{{ $cat->slug }}</td>
                <td class="px-6 py-4"><div class="w-6 h-6 rounded-lg border border-gray-200" style="background: {{ $cat->color }}"></div></td>
                <td class="px-6 py-4"><span class="px-2.5 py-1 bg-gray-100 rounded-lg text-xs font-bold">{{ $cat->approved_tools_count }}</span></td>
                <td class="px-6 py-4 text-gray-500">{{ $cat->sort_order }}</td>
                <td class="px-6 py-4 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <a href="{{ route('admin.categories.edit', $cat) }}" class="px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg text-xs font-semibold hover:bg-blue-100 transition-colors">Edit</a>
                        <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}" onsubmit="return confirm('Delete this category?')">
                            @csrf @method('DELETE')
                            <button class="px-3 py-1.5 bg-red-50 text-red-600 rounded-lg text-xs font-semibold hover:bg-red-100 transition-colors">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection
