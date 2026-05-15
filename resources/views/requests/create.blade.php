@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-8">
        <a href="/" class="text-sm text-ans-dark-green hover:underline flex items-center gap-1">
            ← Volver al catálogo
        </a>
        <h2 class="text-3xl font-heading font-bold text-gray-900 mt-4">Sugerir Herramienta de IA</h2>
        <p class="text-gray-600 mt-2">¿Conoces una herramienta que debería estar en el catálogo? Envía tu solicitud al equipo administrador.</p>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4 mb-6">
        {{ session('success') }}
    </div>
    @endif

    <form method="POST" action="/solicitudes" class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tu Nombre *</label>
                <input type="text" name="requester_name" required value="{{ old('requester_name') }}"
                    class="w-full border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-ans-dark-green @error('requester_name') border-red-400 @enderror">
                @error('requester_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tu Email Institucional *</label>
                <input type="email" name="requester_email" required value="{{ old('requester_email') }}"
                    class="w-full border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-ans-dark-green @error('requester_email') border-red-400 @enderror">
                @error('requester_email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Nombre de la Herramienta *</label>
            <input type="text" name="tool_name" required value="{{ old('tool_name') }}"
                class="w-full border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-ans-dark-green">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">URL Oficial *</label>
            <input type="url" name="url" required value="{{ old('url') }}" placeholder="https://..."
                class="w-full border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-ans-dark-green">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Descripción *</label>
            <textarea name="description" required rows="4"
                class="w-full border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-ans-dark-green">{{ old('description') }}</textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Categoría *</label>
                <select name="category" required
                    class="w-full border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-ans-dark-green">
                    <option value="">Seleccionar...</option>
                    <option value="Video">Video</option>
                    <option value="Photos">Photos</option>
                    <option value="Presentations">Presentations</option>
                    <option value="Dashboard/Analysis">Dashboard/Analysis</option>
                    <option value="Music">Music</option>
                    <option value="Editor">Editor</option>
                    <option value="Others">Otros</option>
                </select>
            </div>
            <div class="flex items-end pb-1">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_google_workspace" value="1"
                        class="w-5 h-5 rounded accent-ans-dark-green">
                    <span class="text-sm font-medium text-gray-700">Es herramienta de Google Workspace</span>
                </label>
            </div>
        </div>

        <div class="pt-2">
            <button type="submit"
                class="w-full bg-ans-dark-green hover:bg-ans-seal-green text-white font-semibold py-3 rounded-lg shadow transition">
                Enviar Solicitud
            </button>
        </div>
    </form>
</div>
@endsection
