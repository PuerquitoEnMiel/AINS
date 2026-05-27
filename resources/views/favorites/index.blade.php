@extends('layouts.app')

@section('header-title', 'My Favorites')
@section('header-subtitle', 'Your bookmarked AI tools')

@section('content')

@if($favorites->isEmpty())
<div class="flex flex-col items-center justify-center py-24 text-center bg-white border border-gray-100 rounded-3xl p-8 shadow-sm">
    <div class="w-24 h-24 bg-red-50 rounded-full flex items-center justify-center mb-6 animate-pulse">
        <svg class="w-12 h-12 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
    </div>
    <h3 class="text-2xl font-heading font-extrabold text-gray-800 mb-3">You do not have any favorite tools yet</h3>
    <p class="text-gray-500 mb-8 max-w-md text-sm leading-relaxed">Explore our curated catalog and click the heart icon on any AI tool to save it here.</p>
    <a href="/" class="inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-ans-dark-green to-ans-light-green text-white rounded-2xl font-bold hover:shadow-lg hover:shadow-ans-dark-green/20 transition-all hover:-translate-y-0.5 text-base">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        Explore Tools
    </a>
</div>
@else
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @include('partials.tool_grid', ['tools' => $favorites])
</div>

@section('modals')
    @include('partials._tool_detail_modal')
@endsection
@endif

@endsection
