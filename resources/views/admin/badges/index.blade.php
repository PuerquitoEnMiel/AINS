@extends('layouts.app')

@section('header-title', 'EdTech Badges Management')
@section('header-subtitle', 'Manage micro-credentials and AI generated quizzes')

@section('content')

@if(session('success'))
<div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700 font-medium">✅ {{ session('success') }}</div>
@endif

@if(session('error'))
<div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700 font-medium">❌ {{ session('error') }}</div>
@endif

<div class="flex items-center justify-between mb-6">
    <h3 class="text-lg font-heading font-bold text-gray-800">All Badges</h3>
    <a href="{{ route('admin.badges.create') }}" class="px-5 py-2.5 bg-ans-dark-green text-white rounded-xl font-semibold text-sm hover:bg-ans-seal-green transition-all shadow-md">+ New Badge</a>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs text-gray-500 uppercase tracking-wider bg-gray-50 border-b border-gray-100">
                    <th class="px-6 py-4">Icon & Badge Name</th>
                    <th class="px-6 py-4">Category</th>
                    <th class="px-6 py-4">Difficulty</th>
                    <th class="px-6 py-4">Sort Order</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($badges as $badge)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl p-2 rounded-xl flex-shrink-0 w-11 h-11 flex items-center justify-center border border-gray-100 shadow-sm" style="background-color: {{ $badge->color }}20; border-color: {{ $badge->color }}40;">
                                {{ $badge->icon }}
                            </span>
                            <div>
                                <div class="font-semibold text-gray-800">{{ $badge->name }}</div>
                                <div class="text-xs text-gray-400 font-mono">{{ $badge->slug }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold capitalize" style="background-color: {{ $badge->color }}10; color: {{ $badge->color }};">
                            {{ str_replace('_', ' ', $badge->category) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $diffColors = match($badge->difficulty) {
                                'bronze' => 'bg-amber-100 text-amber-800 border-amber-200',
                                'silver' => 'bg-slate-100 text-slate-800 border-slate-200',
                                'gold' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                default => 'bg-gray-100 text-gray-800 border-gray-200'
                            };
                        @endphp
                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold capitalize border {{ $diffColors }}">
                            {{ $badge->difficulty }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-500 font-medium">{{ $badge->sort_order }}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.badges.edit', $badge) }}" class="px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg text-xs font-semibold hover:bg-blue-100 transition-colors">Edit</a>
                            <form method="POST" action="{{ route('admin.badges.destroy', $badge) }}" onsubmit="return confirm('Are you sure you want to delete this badge?')">
                                @csrf @method('DELETE')
                                <button class="px-3 py-1.5 bg-red-50 text-red-600 rounded-lg text-xs font-semibold hover:bg-red-100 transition-colors">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-10 text-center text-gray-500 italic bg-gray-50">
                        No badges registered yet. Click "+ New Badge" to get started.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
