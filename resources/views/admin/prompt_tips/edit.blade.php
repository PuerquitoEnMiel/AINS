@extends('layouts.app')

@section('header-title', 'Edit Prompt Tip')
@section('header-subtitle', 'Edit details for ' . $prompt->title)

@section('content')

<div class="max-w-3xl">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
        <form method="POST" action="{{ route('admin.prompt-tips.update', $prompt) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Título del Prompt</label>
                <input type="text" name="title" value="{{ old('title', $prompt->title) }}" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green outline-none transition-all">
                @error('title') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Audiencia Objetivo</label>
                    <select name="target_role" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green outline-none transition-all bg-white">
                        <option value="docentes" {{ old('target_role', $prompt->target_role) == 'docentes' ? 'selected' : '' }}>Docentes</option>
                        <option value="estudiantes" {{ old('target_role', $prompt->target_role) == 'estudiantes' ? 'selected' : '' }}>Estudiantes</option>
                    </select>
                    @error('target_role') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Categoría</label>
                    <input type="text" name="category" value="{{ old('category', $prompt->category) }}" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green outline-none transition-all">
                    @error('category') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Complejidad</label>
                    <select name="complexity" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green outline-none transition-all bg-white">
                        <option value="Básico" {{ old('complexity', $prompt->complexity) == 'Básico' ? 'selected' : '' }}>Básico</option>
                        <option value="Intermedio" {{ old('complexity', $prompt->complexity) == 'Intermedio' ? 'selected' : '' }}>Intermedio</option>
                        <option value="Avanzado" {{ old('complexity', $prompt->complexity) == 'Avanzado' ? 'selected' : '' }}>Avanzado</option>
                    </select>
                    @error('complexity') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Descripción / Propósito</label>
                <textarea name="description" required rows="2" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green outline-none transition-all">{{ old('description', $prompt->description) }}</textarea>
                @error('description') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Texto del Prompt (Template)</label>
                <textarea name="prompt_text" required rows="6" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-mono focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green outline-none transition-all">{{ old('prompt_text', $prompt->prompt_text) }}</textarea>
                @error('prompt_text') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Orden de visualización</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $prompt->sort_order) }}" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green outline-none transition-all">
                @error('sort_order') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="px-6 py-2.5 bg-ans-dark-green text-white rounded-xl font-semibold hover:bg-ans-seal-green transition-all shadow-md text-sm">Actualizar Prompt</button>
                <a href="{{ route('admin.prompt-tips.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-600 rounded-xl font-semibold hover:bg-gray-200 transition-all text-sm">Cancelar</a>
            </div>
        </form>
    </div>
</div>

@endsection
