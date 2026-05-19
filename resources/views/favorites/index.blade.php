@extends('layouts.app')

@section('header-title', 'My Favorites')
@section('header-subtitle', 'Your bookmarked AI tools')

@section('content')

@if($favorites->isEmpty())
<div class="flex flex-col items-center justify-center py-20 text-center">
    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-6">
        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
    </div>
    <h3 class="text-xl font-heading font-bold text-gray-800 mb-2">No favorites yet</h3>
    <p class="text-gray-500 mb-6 max-w-sm">Browse the catalog and click the heart icon on any tool to save it here.</p>
    <a href="/" class="px-6 py-2.5 bg-ans-dark-green text-white rounded-xl font-semibold hover:bg-ans-seal-green transition-all shadow-md">Browse Catalog</a>
</div>
@else
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($favorites as $tool)
    <a href="{{ route('tools.show', $tool) }}" class="group bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-lg transition-all overflow-hidden">
        <div class="p-6">
            <div class="flex items-start gap-4 mb-3">
                @if($tool->logo_url)
                    <img src="{{ $tool->logo_url }}" alt="" class="w-12 h-12 rounded-xl object-cover border border-gray-200">
                @else
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-ans-dark-green to-ans-light-green flex items-center justify-center text-white font-bold">{{ substr($tool->name, 0, 2) }}</div>
                @endif
                <div class="flex-1 min-w-0">
                    <h3 class="font-heading font-bold text-gray-800 group-hover:text-ans-dark-green transition-colors truncate">{{ $tool->name }}</h3>
                    @if($tool->categoryRelation)
                        <span class="text-xs text-gray-500">{{ $tool->categoryRelation->icon }} {{ $tool->categoryRelation->name }}</span>
                    @endif
                </div>
            </div>
            <p class="text-sm text-gray-600 line-clamp-2">{{ $tool->description }}</p>
            <div class="flex items-center gap-3 mt-4 text-xs text-gray-400">
                <span>⭐ {{ number_format($tool->avg_rating, 1) }}</span>
                <span>•</span>
                <span>{{ $tool->click_count }} clicks</span>
            </div>
        </div>
    </a>
    @endforeach
</div>
@endif

@endsection
