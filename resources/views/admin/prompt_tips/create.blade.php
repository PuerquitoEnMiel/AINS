@extends('layouts.app')

@section('header-title', 'New Prompt Tip')
@section('header-subtitle', 'Create a new AI prompt template and guide')

@section('content')

<div class="max-w-3xl">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
        <form method="POST" action="{{ route('admin.prompt-tips.store') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Prompt Title</label>
                <input type="text" name="title" value="{{ old('title') }}" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green outline-none transition-all" placeholder="e.g. Assessment Rubric Design">
                @error('title') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Target Audience</label>
                    <select name="target_role" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green outline-none transition-all bg-white">
                        <option value="docentes" {{ old('target_role') == 'docentes' ? 'selected' : '' }}>Teachers</option>
                        <option value="estudiantes" {{ old('target_role') == 'estudiantes' ? 'selected' : '' }}>Students</option>
                    </select>
                    @error('target_role') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Category</label>
                    <input type="text" name="category" value="{{ old('category') }}" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green outline-none transition-all" placeholder="e.g. Planning, Creativity">
                    @error('category') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Complexity</label>
                    <select name="complexity" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green outline-none transition-all bg-white">
                        <option value="Básico" {{ old('complexity') == 'Básico' ? 'selected' : '' }}>Basic</option>
                        <option value="Intermedio" {{ old('complexity') == 'Intermedio' ? 'selected' : '' }}>Intermediate</option>
                        <option value="Avanzado" {{ old('complexity') == 'Avanzado' ? 'selected' : '' }}>Advanced</option>
                    </select>
                    @error('complexity') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Description / Purpose</label>
                <textarea name="description" required rows="2" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green outline-none transition-all" placeholder="What is the purpose of this prompt and how does it help the user?">{{ old('description') }}</textarea>
                @error('description') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Prompt Text (Template)</label>
                <textarea name="prompt_text" required rows="6" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-mono focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green outline-none transition-all" placeholder="Write the prompt text. You can use [brackets] for variables the user should fill in...">{{ old('prompt_text') }}</textarea>
                @error('prompt_text') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Sort Order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green outline-none transition-all">
                @error('sort_order') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="px-6 py-2.5 bg-ans-dark-green text-white rounded-xl font-semibold hover:bg-ans-seal-green transition-all shadow-md text-sm">Save Prompt</button>
                <a href="{{ route('admin.prompt-tips.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-600 rounded-xl font-semibold hover:bg-gray-200 transition-all text-sm">Cancel</a>
            </div>
        </form>
    </div>
</div>

@endsection
