@extends('layouts.app')

@section('header-title', isset($category) ? 'Edit Category' : 'New Category')
@section('header-subtitle', 'Category management')

@section('content')

<div class="max-w-xl">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
        <form method="POST" action="{{ isset($category) ? route('admin.categories.update', $category) : route('admin.categories.store') }}" class="space-y-5">
            @csrf
            @if(isset($category)) @method('PUT') @endif

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Name</label>
                <input type="text" name="name" value="{{ old('name', $category->name ?? '') }}" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green outline-none transition-all" placeholder="e.g. AI Assistants">
                @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Icon (emoji)</label>
                    <input type="text" name="icon" value="{{ old('icon', $category->icon ?? '') }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green outline-none transition-all" placeholder="🤖">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Color</label>
                    <input type="color" name="color" value="{{ old('color', $category->color ?? '#007934') }}" class="w-full h-11 px-2 py-1 border border-gray-200 rounded-xl cursor-pointer">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Sort Order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green outline-none transition-all">
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="px-6 py-2.5 bg-ans-dark-green text-white rounded-xl font-semibold hover:bg-ans-seal-green transition-all shadow-md text-sm">{{ isset($category) ? 'Update' : 'Create' }} Category</button>
                <a href="{{ route('admin.categories.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-600 rounded-xl font-semibold hover:bg-gray-200 transition-all text-sm">Cancel</a>
            </div>
        </form>
    </div>
</div>

@endsection
