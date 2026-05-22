@extends('layouts.app')

@section('header-title', 'AI Detection Tools')
@section('header-subtitle', 'Tools to detect AI-generated content in student work')

@section('content')

<!-- Hero Banner -->
<div class="relative overflow-hidden rounded-3xl mb-8"
     style="background: linear-gradient(135deg, #1a0505 0%, #3b0f0f 50%, #7f1d1d 100%);">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 right-0 w-96 h-96 rounded-full" style="background: radial-gradient(circle, #ef4444 0%, transparent 70%);"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 rounded-full" style="background: radial-gradient(circle, #f97316 0%, transparent 70%);"></div>
    </div>
    <div class="relative p-8 md:p-12">
        <div class="flex items-start gap-5">
            <div class="w-16 h-16 rounded-2xl bg-red-500/20 border border-red-500/30 flex items-center justify-center text-red-200 flex-shrink-0">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-white mb-2">AI Detection Center</h1>
                <p class="text-red-100/80 text-sm md:text-base max-w-2xl">
                    Specialized tools to verify academic integrity. Identify AI-generated content in essays, research papers, and student assignments.
                </p>
                <div class="flex flex-wrap gap-3 mt-4">
                    <span class="px-3 py-1 bg-red-500/20 border border-red-500/30 rounded-full text-xs text-red-200 font-medium flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        Only for Teachers and Administrators
                    </span>
                    <span class="px-3 py-1 bg-white/10 border border-white/20 rounded-full text-xs text-white/70 font-medium">{{ $tools->count() }} tools available</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Info Card -->
<div class="mb-6 p-5 bg-amber-50 border border-amber-200 rounded-2xl flex gap-4">
    <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
    <div class="text-sm text-amber-800">
        <p class="font-semibold mb-1">Responsible Use of Detection Tools</p>
        <p class="text-amber-700/80">These tools are an <strong>aid</strong>, not a definitive verdict. No tool is 100% accurate. We recommend combining these results with pedagogical evaluation and direct conversation with the student before making academic decisions.</p>
    </div>
</div>

@if($tools->isEmpty())
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-16 text-center">
    <div class="flex flex-col items-center gap-4">
        <div class="w-16 h-16 rounded-2xl bg-gray-50 flex items-center justify-center text-gray-400">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>
        <h3 class="font-semibold text-gray-700">No detection tools yet</h3>
        <p class="text-sm text-gray-400 max-w-xs">The administrator can add AI detection tools from the admin panel.</p>
        @if(Auth::user()->isAdmin())
        <a href="{{ route('admin.tools.create') }}" class="mt-2 px-5 py-2.5 bg-ans-dark-green text-white rounded-xl text-sm font-semibold hover:bg-ans-seal-green transition-all">
            + Add Tool
        </a>
        @endif
    </div>
</div>
@else
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
    @foreach($tools as $tool)
    <a href="{{ route('tools.show', $tool) }}" class="group bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col">
        <!-- Card Header -->
        <div class="p-5 border-b border-gray-50">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3">
                    @if($tool->image)
                    <img src="{{ asset('storage/' . $tool->image) }}" alt="{{ $tool->name }}" class="w-10 h-10 rounded-xl object-cover border border-gray-100">
                    @else
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-red-50 border border-red-100 text-red-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    @endif
                    <div>
                        <h3 class="font-bold text-gray-900 group-hover:text-ans-dark-green transition-colors">{{ $tool->name }}</h3>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                            <span class="text-xs text-red-600 font-medium">AI Detection</span>
                        </div>
                    </div>
                </div>
                @if($tool->is_official)
                <span class="px-2 py-0.5 bg-blue-50 text-blue-700 text-xs font-semibold rounded-full flex-shrink-0">Official</span>
                @endif
            </div>
            <p class="text-sm text-gray-500 mt-3 leading-relaxed line-clamp-2">{{ $tool->description }}</p>
        </div>

        <!-- Card Footer -->
        <div class="p-4 mt-auto">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3 text-xs text-gray-400">
                    @if($tool->views_count)
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        {{ number_format($tool->views_count) }}
                    </span>
                    @endif
                    @if($tool->avg_rating)
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-yellow-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"></path></svg>
                        {{ number_format($tool->avg_rating, 1) }}
                    </span>
                    @endif
                </div>
                <span class="text-xs font-medium text-ans-dark-green group-hover:text-ans-seal-green flex items-center gap-1">
                    View Tool
                    <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </span>
            </div>
        </div>
    </a>
    @endforeach
</div>
@endif

@endsection
