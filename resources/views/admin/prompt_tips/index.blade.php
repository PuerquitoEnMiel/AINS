@extends('layouts.app')

@section('header-title', 'Prompt Tips Management')
@section('header-subtitle', 'Manage EdTech AI prompt templates and tips')

@section('content')

@if(session('success'))
<div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700 font-medium">✅ {{ session('success') }}</div>
@endif

<div class="flex items-center justify-between mb-6">
    <h3 class="text-lg font-heading font-bold text-gray-800">AI Prompts & Tips</h3>
    <a href="{{ route('admin.prompt-tips.create') }}" class="px-5 py-2.5 bg-ans-dark-green text-white rounded-xl font-semibold text-sm hover:bg-ans-seal-green transition-all shadow-md">+ New Prompt</a>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="text-left text-xs text-gray-500 uppercase tracking-wider bg-gray-50 border-b border-gray-100">
                <th class="px-6 py-4">Title</th>
                <th class="px-6 py-4">Audience</th>
                <th class="px-6 py-4">Category</th>
                <th class="px-6 py-4">Complexity</th>
                <th class="px-6 py-4">Sort Order</th>
                <th class="px-6 py-4 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @foreach($prompts as $prompt)
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 font-semibold text-gray-800">{{ $prompt->title }}</td>
                <td class="px-6 py-4">
                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold {{ $prompt->target_role === 'docentes' ? 'bg-purple-50 text-purple-700 border border-purple-100' : 'bg-blue-50 text-blue-700 border border-blue-100' }}">
                        {{ $prompt->target_role === 'docentes' ? 'Teachers' : ucfirst($prompt->target_role) }}
                    </span>
                </td>
                <td class="px-6 py-4 text-gray-600">{{ $prompt->category }}</td>
                <td class="px-6 py-4">
                    <span class="px-2 py-0.5 rounded text-xs font-medium {{ in_array($prompt->complexity, ['Avanzado', 'Advanced']) ? 'bg-red-50 text-red-700' : (in_array($prompt->complexity, ['Intermedio', 'Intermediate']) ? 'bg-yellow-50 text-yellow-700' : 'bg-green-50 text-green-700') }}">
                        {{ in_array($prompt->complexity, ['Avanzado', 'Advanced']) ? 'Advanced' : (in_array($prompt->complexity, ['Intermedio', 'Intermediate']) ? 'Intermediate' : 'Basic') }}
                    </span>
                </td>
                <td class="px-6 py-4 text-gray-500">{{ $prompt->sort_order }}</td>
                <td class="px-6 py-4 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <a href="{{ route('admin.prompt-tips.edit', $prompt) }}" class="px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg text-xs font-semibold hover:bg-blue-100 transition-colors">Edit</a>
                        <form method="POST" action="{{ route('admin.prompt-tips.destroy', $prompt) }}" onsubmit="return confirm('Are you sure you want to delete this prompt?')">
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
