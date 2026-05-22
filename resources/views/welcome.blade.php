@extends('layouts.app')

@section('content')

@php
    $allTools = is_iterable($tools) ? $tools : collect();
    $officialCount = $allTools->where('is_official', true)->count();
    $totalCount = $allTools->count();
    $categoriesCount = isset($categories) && is_iterable($categories) ? $categories->count() : 5;
    
    // Banner configuration (recently approved/added in last 15 days)
    $newestTool = $allTools->sortByDesc('created_at')->first();
    $showNewestBanner = false;
    if ($newestTool && $newestTool->created_at) {
        $showNewestBanner = \Carbon\Carbon::parse($newestTool->created_at)->gt(now()->subDays(15));
    }
    
    // Trending tools (top 4 by clicks)
    $trendingTools = $allTools->sortByDesc('click_count')->take(4);

    // Vibrant Tailwind Gradients for placeholders
    $vibrantGradients = [
        'from-purple-500 to-indigo-600',
        'from-pink-500 to-rose-600',
        'from-emerald-500 to-teal-600',
        'from-blue-500 to-indigo-600',
        'from-amber-500 to-orange-600',
        'from-cyan-500 to-blue-600',
        'from-violet-500 to-fuchsia-600',
        'from-red-500 to-ans-orange'
    ];

    $getGradientForName = function($name) use ($vibrantGradients) {
        $hash = 0;
        foreach (str_split($name) as $char) {
            $hash += ord($char);
        }
        return $vibrantGradients[$hash % count($vibrantGradients)];
    };
@endphp

<style>
    /* ─── Premium Catalog Card Border Mask Glow ─── */
    .premium-card {
        position: relative;
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        border: 1px solid rgba(229, 231, 235, 0.5) !important;
    }
    .premium-card::after {
        content: '';
        position: absolute;
        inset: -1px;
        border-radius: 1rem;
        padding: 1.5px;
        background: linear-gradient(135deg, transparent 40%, rgba(0, 121, 52, 0.25) 75%, rgba(255, 131, 0, 0.3) 100%);
        -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        opacity: 0;
        transition: opacity 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        pointer-events: none;
        z-index: 1;
    }
    .premium-card:hover {
        transform: translateY(-5px);
        border-color: transparent !important;
        box-shadow: 0 20px 35px rgba(0, 105, 55, 0.05), 0 4px 12px rgba(255, 131, 0, 0.03);
    }
    .premium-card:hover::after {
        opacity: 1;
    }
    
    /* ─── Staggered Card Animation Adjustments ─── */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(12px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .animate-fade-in-up {
        animation: fadeInUp 0.45s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
</style>

<!-- ═══════════════════════════════════════════════════════════ -->
<!-- HERO SECTION — Impactful gradient banner with stats        -->
<!-- ═══════════════════════════════════════════════════════════ -->
<div class="relative -mx-8 -mt-8 mb-12 overflow-hidden">
    <div class="bg-gradient-to-br from-ans-dark-green via-ans-seal-green to-ans-dark-green px-10 py-14 md:py-16 relative">
        <!-- Decorative floating shapes -->
        <div class="absolute top-6 right-16 w-32 h-32 bg-white/5 rounded-full blur-2xl animate-float"></div>
        <div class="absolute bottom-4 left-24 w-24 h-24 bg-ans-orange/10 rounded-full blur-xl animate-float" style="animation-delay: 1s;"></div>
        <div class="absolute top-1/2 right-1/3 w-16 h-16 bg-ans-light-green/10 rounded-full blur-lg animate-pulse-soft"></div>

        <div class="max-w-7xl mx-auto relative z-10">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-8">
                <!-- Left: Title -->
                <div class="flex-1 animate-fade-in-up" style="animation-duration: 0.5s;">
                    <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-full px-4 py-1.5 mb-5 border border-white/10">
                        <span class="w-2 h-2 bg-ans-light-green rounded-full animate-pulse-soft"></span>
                        <span class="text-xs font-semibold text-white/90 tracking-wide uppercase">Curated & Approved</span>
                    </div>
                    <h1 class="text-4xl md:text-5xl font-heading font-extrabold text-white leading-tight tracking-tight">
                        AI Tools<br>
                        <span class="text-ans-orange">Directory</span>
                    </h1>
                    <p class="text-white/70 mt-4 max-w-lg text-base leading-relaxed">
                        Explore curated AI-powered tools approved for teachers and students at the American Nicaraguan School.
                    </p>
                </div>

                <!-- Right: Quick Stats Cards -->
                <div class="flex gap-4 animate-fade-in-up" style="animation-duration: 0.7s;">
                    <div class="bg-white/10 backdrop-blur-sm border border-white/10 rounded-2xl p-5 text-center min-w-[110px]">
                        <div class="text-3xl font-heading font-extrabold text-white">{{ $totalCount }}</div>
                        <p class="text-xs text-white/60 mt-1 font-medium">Total Tools</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm border border-white/10 rounded-2xl p-5 text-center min-w-[110px]">
                        <div class="text-3xl font-heading font-extrabold text-ans-orange">{{ $officialCount }}</div>
                        <p class="text-xs text-white/60 mt-1 font-medium">Official</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm border border-white/10 rounded-2xl p-5 text-center min-w-[110px]">
                        <div class="text-3xl font-heading font-extrabold text-ans-light-green">{{ $categoriesCount }}</div>
                        <p class="text-xs text-white/60 mt-1 font-medium">Categories</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Curved bottom edge -->
    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 40" fill="none" class="w-full">
            <path d="M0 40V0C240 30 480 40 720 40C960 40 1200 30 1440 0V40H0Z" fill="rgb(249 250 251 / 0.5)"/>
        </svg>
    </div>
</div>


@if($newestTool && $showNewestBanner)
<div id="new-app-banner" class="mb-8 animate-fade-in-up" data-tool-id="{{ $newestTool->id }}">
    <div class="relative bg-gradient-to-r from-ans-dark-green/95 via-ans-seal-green/90 to-ans-dark-green/95 backdrop-blur-xl border border-white/15 rounded-2xl p-4 md:p-5 shadow-lg overflow-hidden flex flex-col md:flex-row items-center justify-between gap-4">
        <!-- Close Button -->
        <button onclick="dismissNewAppBanner({{ $newestTool->id }})" class="absolute top-3 right-3 text-white/60 hover:text-white transition-colors" title="Close Novedades Banner">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
        <!-- Content -->
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-ans-orange rounded-xl flex items-center justify-center shadow-md shadow-ans-orange/20 text-white font-bold text-lg flex-shrink-0 animate-pulse-soft">
                @if($newestTool->logo_url)
                    <img src="{{ asset($newestTool->logo_url) }}" alt="{{ $newestTool->name }}" class="w-full h-full object-cover rounded-xl">
                @else
                    {{ substr($newestTool->name, 0, 1) }}
                @endif
            </div>
            <div>
                <span class="inline-flex items-center gap-1 bg-ans-orange/20 text-ans-orange text-[9px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider mb-1">
                    New
                </span>
                <h4 class="text-sm font-bold text-white">New tool available: {{ $newestTool->name }}!</h4>
                <p class="text-xs text-white/70 mt-0.5 line-clamp-1 md:line-clamp-none">{{ $newestTool->description }}</p>
            </div>
        </div>
        <!-- Action Button -->
        <div class="flex-shrink-0 pr-8">
            <button onclick="openToolModal(JSON.parse(this.dataset.tool))" 
                    data-tool="{{ json_encode(['id'=>$newestTool->id,'name'=>$newestTool->name,'desc'=>$newestTool->description,'url'=>$newestTool->url,'cat'=>$newestTool->category,'type'=>$newestTool->is_google_workspace?'Google Workspace':'3rd Party','logo'=>$newestTool->logo_url?asset($newestTool->logo_url):null]) }}"
                    class="bg-ans-orange hover:bg-ans-orange/90 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition-all shadow-md shadow-ans-orange/20 hover:scale-105">
                Explore Now
            </button>
        </div>
    </div>
</div>
@endif

<!-- ═══════════════════════════════════════════════════════════ -->
<!-- OFFICIAL TOOLS — Dynamic featured section                  -->
<!-- ═══════════════════════════════════════════════════════════ -->
@php
    $officialTools = $allTools->where('is_official', true);
@endphp

@if($officialTools->count() > 0)
<div id="official-section" class="mb-14">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <div class="w-1.5 h-8 bg-gradient-to-b from-ans-orange to-ans-yellow rounded-full"></div>
            <div>
                <h3 class="text-xl font-heading font-bold text-gray-900">Official Tools</h3>
                <p class="text-xs text-gray-500 mt-0.5">MINED & Google approved platforms</p>
            </div>
        </div>
        <span class="text-xs font-semibold bg-ans-orange/10 text-ans-orange px-3 py-1.5 rounded-full">★ Featured</span>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        @foreach($officialTools as $index => $oTool)
            @php
                $palettes = [
                    0 => ['from' => 'from-ans-orange to-[#e67600]', 'bg' => 'bg-gradient-to-bl from-ans-orange/5 to-transparent', 'shadow' => 'shadow-ans-orange/20'],
                    1 => ['from' => 'from-ans-light-green to-ans-2nd-green', 'bg' => 'bg-gradient-to-bl from-ans-light-green/5 to-transparent', 'shadow' => 'shadow-ans-light-green/20'],
                    2 => ['from' => 'from-ans-purple to-[#5a2570]', 'bg' => 'bg-gradient-to-bl from-ans-purple/5 to-transparent', 'shadow' => 'shadow-ans-purple/20'],
                    3 => ['from' => 'from-ans-blue to-ans-light-blue', 'bg' => 'bg-gradient-to-bl from-ans-blue/5 to-transparent', 'shadow' => 'shadow-ans-blue/20'],
                ];
                $palette = $palettes[$index % 4];
            @endphp
            <div class="group premium-card bg-white rounded-2xl border border-gray-100 p-6 hover:shadow-xl hover:shadow-ans-orange/5 hover:-translate-y-1 transition-all duration-300 relative overflow-hidden cursor-pointer animate-fade-in-up"
                 style="animation-delay: {{ 0.05 * ($index + 1) }}s;"
                 data-tool="{{ json_encode([
                     'id' => $oTool->id,
                     'name' => $oTool->name,
                     'desc' => $oTool->description,
                     'url' => $oTool->url,
                     'cat' => $oTool->category,
                     'type' => $oTool->is_google_workspace ? 'Google Workspace' : '3rd Party',
                     'logo' => $oTool->logo_url ? asset($oTool->logo_url) : null
                 ]) }}"
                 onclick="openToolModal(JSON.parse(this.dataset.tool))">
                <div class="absolute top-0 right-0 w-24 h-24 {{ $palette['bg'] }} rounded-bl-full"></div>
                
                <!-- Heart Button -->
                <button onclick="toggleCardFavorite(event, this, '{{ $oTool->name }}', {{ $oTool->id }})" class="card-fav-btn absolute top-4 right-4 z-10 w-8 h-8 rounded-full bg-white/80 hover:bg-white text-gray-400 hover:text-red-500 flex items-center justify-center transition-all shadow-sm border border-gray-100/50 hover:scale-105 active:scale-95" data-tool-name="{{ $oTool->name }}" title="Favorite">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                </button>

                @php
                    $grad = $oTool->logo_url ? '' : $getGradientForName($oTool->name);
                @endphp
                <div class="w-14 h-14 @if($oTool->logo_url) bg-gradient-to-br {{ $palette['from'] }} @else bg-gradient-to-br {{ $grad }} shadow-md shadow-black/10 @endif rounded-2xl flex items-center justify-center mb-5 shadow-lg {{ $palette['shadow'] }} group-hover:scale-110 transition-transform duration-300">
                    @if($oTool->logo_url)
                        <img src="{{ asset($oTool->logo_url) }}" alt="{{ $oTool->name }}" class="w-full h-full object-cover rounded-2xl">
                    @else
                        <span class="text-white font-bold text-xl">{{ substr($oTool->name, 0, 1) }}</span>
                    @endif
                </div>
                <h4 class="font-heading font-bold text-gray-900 text-lg">{{ $oTool->name }}</h4>
                <p class="text-sm text-gray-500 mt-2 leading-relaxed line-clamp-2">{{ $oTool->description }}</p>
                <div class="mt-5 flex items-center justify-between">
                    <span class="text-[10px] font-bold bg-ans-dark-green/10 text-ans-dark-green px-2.5 py-1 rounded-full uppercase tracking-wider">Official</span>
                    <svg class="w-5 h-5 text-gray-300 group-hover:text-ans-orange group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

<!-- ═══════════════════════════════════════════════════════════ -->
<!-- TRENDING TOOLS — Animated visitor counters                 -->
<!-- ═══════════════════════════════════════════════════════════ -->
@if($trendingTools->count() > 0)
<div id="trending-section" class="mb-14">
    <style>
        .hide-scroll::-webkit-scrollbar {
            display: none;
        }
    </style>
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <div class="w-1.5 h-8 bg-gradient-to-b from-red-500 to-ans-orange rounded-full"></div>
            <div>
                <h3 class="text-xl font-heading font-bold text-gray-900">Trending Tools</h3>
                <p class="text-xs text-gray-500 mt-0.5">The most popular platforms in the ANS community</p>
            </div>
        </div>
        <span class="inline-flex items-center gap-1 text-xs font-semibold bg-red-500/10 text-red-600 px-3 py-1.5 rounded-full">
            <span class="w-1.5 h-1.5 bg-red-500 rounded-full animate-ping"></span>
            Trending
        </span>
    </div>
    
    <div class="flex overflow-x-auto gap-5 pb-6 pt-3 px-2 snap-x snap-mandatory hide-scroll" style="scrollbar-width: none; -ms-overflow-style: none;">
        @foreach($trendingTools as $index => $tTool)
            <div class="flex-shrink-0 w-80 snap-center group premium-card bg-white rounded-2xl border border-gray-100 p-5 hover:shadow-xl hover:shadow-red-500/5 hover:-translate-y-1 transition-all duration-300 relative overflow-visible cursor-pointer animate-fade-in-up"
                 style="animation-delay: {{ 0.05 * ($index + 1) }}s;"
                 data-tool="{{ json_encode([
                     'id' => $tTool->id,
                     'name' => $tTool->name,
                     'desc' => $tTool->description,
                     'url' => $tTool->url,
                     'cat' => $tTool->category,
                     'type' => $tTool->is_google_workspace ? 'Google Workspace' : '3rd Party',
                     'logo' => $tTool->logo_url ? asset($tTool->logo_url) : null
                 ]) }}"
                 onclick="openToolModal(JSON.parse(this.dataset.tool))">
                 
                <!-- Heart Button -->
                <button onclick="toggleCardFavorite(event, this, '{{ $tTool->name }}', {{ $tTool->id }})" class="card-fav-btn absolute top-4 right-4 z-10 w-8 h-8 rounded-full bg-white/80 hover:bg-white text-gray-400 hover:text-red-500 flex items-center justify-center transition-all shadow-sm border border-gray-100/50 hover:scale-105 active:scale-95" data-tool-name="{{ $tTool->name }}" title="Favorite">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                </button>

                <!-- App Store Rank Badge -->
                <div class="absolute -top-3 -left-3 w-10 h-10 bg-gradient-to-br from-red-500 to-ans-orange rounded-full flex items-center justify-center text-white font-black text-lg shadow-lg border-4 border-white z-10 shadow-red-500/30">
                    #{{ $index + 1 }}
                </div>

                <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-red-500/5 to-transparent rounded-bl-full overflow-hidden"></div>
                @php
                    $grad = $tTool->logo_url ? '' : $getGradientForName($tTool->name);
                @endphp
                <div class="flex items-start justify-between mb-4 relative z-0">
                    <div class="w-14 h-14 @if($tTool->logo_url) bg-gradient-to-br from-gray-100 to-gray-50 @else bg-gradient-to-br {{ $grad }} shadow-md shadow-black/10 @endif rounded-xl flex items-center justify-center @if(!$tTool->logo_url) text-white @else text-gray-400 @endif font-bold text-2xl group-hover:scale-110 transition-all duration-300 shadow-sm border border-gray-100/50">
                        @if($tTool->logo_url)
                            <img src="{{ asset($tTool->logo_url) }}" alt="{{ $tTool->name }}" class="w-full h-full object-cover rounded-xl">
                        @else
                            {{ substr($tTool->name, 0, 1) }}
                        @endif
                    </div>
                    <!-- Click Count Badge -->
                    <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-red-50 text-red-600 px-2.5 py-1 rounded-lg border border-red-100 group-hover:scale-105 transition-transform duration-300 shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        {{ $tTool->click_count }} clicks
                    </span>
                </div>
                <h4 class="font-heading font-bold text-gray-900 text-base leading-tight group-hover:text-red-600 transition-colors">{{ $tTool->name }}</h4>
                <p class="text-xs text-gray-500 mt-2 leading-relaxed line-clamp-2">{{ $tTool->description }}</p>
                <div class="mt-4 flex items-center gap-2">
                    @if($tTool->is_official)
                        <span class="text-[9px] font-bold bg-ans-orange/10 text-ans-orange px-2 py-0.5 rounded-full uppercase tracking-wider">★ Official</span>
                    @endif
                    @if($tTool->is_google_workspace)
                        <span class="text-[9px] font-bold bg-ans-blue/10 text-ans-blue px-2 py-0.5 rounded-full uppercase tracking-wider">Workspace</span>
                    @else
                        <span class="text-[9px] font-bold bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full uppercase tracking-wider">3rd Party</span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif


<!-- ═══════════════════════════════════════════════════════════ -->
<!-- GENERAL CATALOG — Filterable tool grid                     -->
<!-- ═══════════════════════════════════════════════════════════ -->
<div class="mb-12">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <div class="flex items-center gap-3">
            <div class="w-1.5 h-8 bg-gradient-to-b from-ans-dark-green to-ans-light-green rounded-full"></div>
            <div>
                <h3 class="text-xl font-heading font-bold text-gray-900">EdTech Catalog</h3>
                <p class="text-xs text-gray-500 mt-0.5">All approved tools available for use</p>
            </div>
        </div>
        <!-- Filter Pills -->
        <div class="flex gap-2 flex-wrap items-center">
            <button class="text-xs font-semibold px-4 py-2 bg-ans-dark-green text-white rounded-full shadow-sm transition-all hover:shadow-md" id="filter-all">All</button>
            <button class="text-xs font-semibold px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-full hover:border-ans-dark-green hover:text-ans-dark-green transition-all" id="filter-workspace">Google Workspace</button>
            <button class="text-xs font-semibold px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-full hover:border-ans-dark-green hover:text-ans-dark-green transition-all" id="filter-3rdparty">3rd Party</button>
            
            <div class="h-4 w-[1px] bg-gray-200 mx-1"></div>
            
            @foreach($categories as $category)
                <button class="text-xs font-semibold px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-full hover:border-ans-dark-green hover:text-ans-dark-green transition-all filter-category-btn" 
                        data-category="{{ $category->name }}" 
                        id="filter-cat-{{ $category->id }}">
                    {{ $category->icon }} {{ $category->name }}
                </button>
            @endforeach
            
            <div class="h-4 w-[1px] bg-gray-200 mx-1"></div>
            
            <button class="text-xs font-semibold px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-full hover:border-ans-dark-green hover:text-ans-dark-green transition-all flex items-center gap-1.5" id="filter-favs">
                <svg class="w-3.5 h-3.5 text-yellow-500 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                Favorites
            </button>
        </div>
    </div>
    
    <div id="catalog-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        @foreach((is_iterable($tools) ? $tools : []) as $index => $tool)
        @if($tool->is_official)
            @continue
        @endif
        <div class="group premium-card bg-white rounded-2xl border border-gray-100 p-6 hover:shadow-xl hover:shadow-gray-100 hover:-translate-y-1 transition-all duration-300 cursor-pointer relative overflow-visible animate-fade-in-up"
             style="animation-delay: {{ 0.05 * ($index + 1) }}s;"
             data-tool="{{ json_encode(['id'=>$tool->id,'name'=>$tool->name,'desc'=>$tool->description,'url'=>$tool->url,'cat'=>$tool->category,'type'=>$tool->is_google_workspace?'Google Workspace':'3rd Party','logo'=>$tool->logo_url?asset($tool->logo_url):null]) }}"
             onclick="openToolModal(JSON.parse(this.dataset.tool))">
            
            <!-- Heart Button -->
            <button onclick="toggleCardFavorite(event, this, '{{ $tool->name }}', {{ $tool->id }})" class="card-fav-btn absolute top-4 right-4 z-10 w-8 h-8 rounded-full bg-white/80 hover:bg-white text-gray-400 hover:text-red-500 flex items-center justify-center transition-all shadow-sm border border-gray-100/50 hover:scale-105 active:scale-95" data-tool-name="{{ $tool->name }}" title="Favorite">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
            </button>

            <div class="flex items-start justify-between mb-4">
                @if($tool->logo_url)
                    <img src="{{ asset($tool->logo_url) }}" alt="{{ $tool->name }}" class="w-12 h-12 rounded-xl object-cover border border-gray-100 group-hover:scale-110 transition-transform duration-300">
                @else
                    @php
                        $grad = $getGradientForName($tool->name);
                    @endphp
                    <div class="w-12 h-12 bg-gradient-to-br {{ $grad }} rounded-xl flex items-center justify-center text-white font-bold text-xl group-hover:scale-110 transition-all duration-300 shadow-md shadow-black/10">
                        {{ substr($tool->name, 0, 1) }}
                    </div>
                @endif
                <div class="w-5 h-5"></div>
            </div>
            <h4 class="font-heading font-bold text-gray-900 text-lg leading-tight">{{ $tool->name }}</h4>
            <p class="text-sm text-gray-500 mt-2 leading-relaxed line-clamp-2">{{ $tool->description }}</p>
            <div class="mt-5 flex items-center gap-2">
                @if($tool->is_google_workspace)
                    <span class="text-[10px] font-bold bg-ans-blue/10 text-ans-blue px-2.5 py-1 rounded-full uppercase tracking-wider">Workspace</span>
                @else
                    <span class="text-[10px] font-bold bg-gray-100 text-gray-500 px-2.5 py-1 rounded-full uppercase tracking-wider">3rd Party</span>
                @endif
                <span class="text-[10px] text-gray-300 ml-auto">Click for details</span>
            </div>
        </div>
        @endforeach

        @if(!is_iterable($tools) || count($tools) == 0)
        <!-- Demo Cards when DB is empty -->
        @php
            $demoTools = [
                ['name' => 'ChatGPT', 'desc' => 'Advanced language model for general-purpose assistance and content creation.', 'color' => 'from-emerald-500 to-emerald-700', 'shadow' => 'shadow-emerald-500/20', 'type' => '3rd Party', 'cat' => 'Text & Writing'],
                ['name' => 'Gemini', 'desc' => 'Google\'s multimodal AI for research, analysis and creative tasks.', 'color' => 'from-ans-blue to-ans-light-blue', 'shadow' => 'shadow-ans-blue/20', 'type' => 'Workspace', 'cat' => 'Text & Writing'],
                ['name' => 'Canva AI', 'desc' => 'AI-powered design tool for presentations, posters and visual content.', 'color' => 'from-ans-purple to-pink-600', 'shadow' => 'shadow-ans-purple/20', 'type' => '3rd Party', 'cat' => 'Image & Design'],
                ['name' => 'Gamma', 'desc' => 'AI-powered presentation and document builder for educators.', 'color' => 'from-ans-orange to-ans-yellow', 'shadow' => 'shadow-ans-orange/20', 'type' => '3rd Party', 'cat' => 'Image & Design'],
                ['name' => 'NotebookLM', 'desc' => 'Google\'s AI research assistant for summarizing and exploring documents.', 'color' => 'from-ans-dark-green to-ans-2nd-green', 'shadow' => 'shadow-ans-dark-green/20', 'type' => 'Workspace', 'cat' => 'Data & Analysis'],
                ['name' => 'Claude', 'desc' => 'AI assistant optimized for analysis, writing, and safe conversations.', 'color' => 'from-amber-500 to-orange-600', 'shadow' => 'shadow-amber-500/20', 'type' => '3rd Party', 'cat' => 'Text & Writing'],
                ['name' => 'Perplexity', 'desc' => 'AI-powered search engine with cited sources for academic research.', 'color' => 'from-cyan-500 to-blue-600', 'shadow' => 'shadow-cyan-500/20', 'type' => '3rd Party', 'cat' => 'Data & Analysis'],
                ['name' => 'Suno AI', 'desc' => 'AI music generation tool for creative audio projects.', 'color' => 'from-pink-500 to-rose-600', 'shadow' => 'shadow-pink-500/20', 'type' => '3rd Party', 'cat' => 'Video & Animation'],
            ];
        @endphp
        @php
            $demoUrls = ['ChatGPT'=>'https://chatgpt.com','Gemini'=>'https://gemini.google.com','Canva AI'=>'https://canva.com','Gamma'=>'https://gamma.app','NotebookLM'=>'https://notebooklm.google.com','Claude'=>'https://claude.ai','Perplexity'=>'https://perplexity.ai','Suno AI'=>'https://suno.ai'];
        @endphp
        @foreach($demoTools as $index => $demo)
        <div class="group premium-card bg-white rounded-2xl border border-gray-100 p-6 hover:shadow-xl hover:shadow-gray-100 hover:-translate-y-1 transition-all duration-300 cursor-pointer relative overflow-visible animate-fade-in-up"
             style="animation-delay: {{ 0.05 * ($index + 1) }}s;"
             data-tool="{{ json_encode(['name'=>$demo['name'],'desc'=>$demo['desc'],'url'=>$demoUrls[$demo['name']]??'#','cat'=>$demo['cat'],'type'=>$demo['type'],'logo'=>null]) }}"
             onclick="openToolModal(JSON.parse(this.dataset.tool))">
            
            <!-- Heart Button -->
            <button onclick="toggleCardFavorite(event, this, '{{ $demo['name'] }}', 0)" class="card-fav-btn absolute top-4 right-4 z-10 w-8 h-8 rounded-full bg-white/80 hover:bg-white text-gray-400 hover:text-red-500 flex items-center justify-center transition-all shadow-sm border border-gray-100/50 hover:scale-105 active:scale-95" data-tool-name="{{ $demo['name'] }}" title="Favorite">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
            </button>

            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 bg-gradient-to-br {{ $demo['color'] }} rounded-xl flex items-center justify-center {{ $demo['shadow'] }} shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <span class="text-white font-bold text-xl">{{ substr($demo['name'], 0, 1) }}</span>
                </div>
                <div class="w-5 h-5"></div>
            </div>
            <h4 class="font-heading font-bold text-gray-900 text-lg leading-tight">{{ $demo['name'] }}</h4>
            <p class="text-sm text-gray-500 mt-2 leading-relaxed line-clamp-2">{{ $demo['desc'] }}</p>
            <div class="mt-5 flex items-center gap-2">
                @if($demo['type'] === 'Workspace')
                    <span class="text-[10px] font-bold bg-ans-blue/10 text-ans-blue px-2.5 py-1 rounded-full uppercase tracking-wider">Workspace</span>
                @else
                    <span class="text-[10px] font-bold bg-gray-100 text-gray-500 px-2.5 py-1 rounded-full uppercase tracking-wider">3rd Party</span>
                @endif
                <span class="text-[10px] text-gray-300 ml-auto">Click for details</span>
            </div>
        </div>
        @endforeach
        @endif
    </div>

    <!-- Dynamic Empty State -->
    <div id="catalog-empty-state" class="hidden flex flex-col items-center justify-center text-center py-16 px-6 bg-white rounded-3xl border border-gray-100 shadow-sm animate-fade-in-up mt-6 max-w-xl mx-auto">
        <div class="w-20 h-20 bg-ans-dark-green/5 rounded-full flex items-center justify-center text-ans-dark-green mb-6 animate-pulse-soft">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>
        <h4 class="text-xl font-heading font-bold text-gray-800 tracking-tight">No tools match your query</h4>
        <p class="text-sm text-gray-500 mt-2 max-w-md leading-relaxed">
            Try adjusting your search terms, clearing keywords, or switching categories to find approved educational platforms.
        </p>
        <button onclick="resetSearchFilters()" class="mt-6 inline-flex items-center gap-2 bg-ans-dark-green hover:bg-ans-seal-green text-white text-xs font-bold px-5 py-2.5 rounded-xl transition-all shadow-md shadow-ans-dark-green/10 hover:-translate-y-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89"></path>
            </svg>
            Reset Filters
        </button>
    </div>
</div>


<!-- ═══════════════════════════════════════════════════════════ -->
<!-- CTA BANNER — Encourage tool suggestions                    -->
<!-- ═══════════════════════════════════════════════════════════ -->
@auth
<div class="relative bg-gradient-to-r from-ans-orange to-[#e67600] rounded-2xl p-8 md:p-10 overflow-hidden mb-8 animate-fade-in-up" style="animation-delay: 0.6s;">
    <div class="absolute top-0 right-0 w-48 h-48 bg-white/10 rounded-full blur-2xl translate-x-16 -translate-y-16"></div>
    <div class="absolute bottom-0 left-0 w-32 h-32 bg-black/5 rounded-full blur-xl -translate-x-8 translate-y-8"></div>
    <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
        <div>
            <h3 class="text-2xl font-heading font-extrabold text-white">Know a great AI tool?</h3>
            <p class="text-white/80 mt-2 max-w-md">Help us grow this directory. Suggest tools you've found useful for teaching or learning.</p>
        </div>
        <a href="{{ route('requests.create') }}" class="flex-shrink-0 bg-white text-ans-orange font-bold px-8 py-3.5 rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all text-sm">
            Suggest a Tool →
        </a>
    </div>
</div>
@endauth

@section('modals')
<!-- ════════════════════════════════════════════ -->
<!-- TOOL DETAIL MODAL                            -->
<!-- ════════════════════════════════════════════ -->
<div id="tool-modal" class="fixed inset-0 z-50 hidden" onclick="if(event.target===this)closeToolModal()">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity" id="modal-backdrop"></div>
    <!-- Panel -->
    <div class="absolute right-0 top-0 bottom-0 w-full max-w-md bg-white shadow-2xl flex flex-col transform translate-x-full transition-transform duration-300" id="modal-panel">
        <!-- Header -->
        <div class="relative bg-gradient-to-br from-ans-dark-green to-ans-seal-green p-8 flex-shrink-0">
            <div class="absolute top-4 right-16 w-20 h-20 bg-white/5 rounded-full blur-xl"></div>
            <button onclick="toggleFavCurrent()" id="modal-fav-btn" class="absolute top-4 right-16 w-9 h-9 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center text-white/80 hover:text-white transition-all" title="Favorite">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.381-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
            </button>
            <button onclick="closeToolModal()" class="absolute top-4 right-4 w-9 h-9 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center text-white/80 hover:text-white transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            <div class="flex items-center gap-4">
                <div id="modal-logo" class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center border-2 border-white/20 flex-shrink-0 overflow-hidden">
                    <span id="modal-logo-letter" class="text-white font-bold text-2xl"></span>
                </div>
                <div>
                    <p class="text-white/60 text-xs font-semibold uppercase tracking-wider mb-1" id="modal-category"></p>
                    <h2 class="text-2xl font-heading font-extrabold text-white" id="modal-name"></h2>
                    <span id="modal-type-badge" class="text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider mt-2 inline-block"></span>
                </div>
            </div>
        </div>
        <!-- Body -->
        <div class="flex-1 overflow-y-auto p-8">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">About this tool</h3>
            <p class="text-gray-700 leading-relaxed" id="modal-desc"></p>
            <div class="mt-6 p-4 bg-gray-50 rounded-xl border border-gray-100">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Official URL</p>
                <a id="modal-url" href="#" target="_blank" class="text-ans-dark-green text-sm font-medium hover:underline break-all"></a>
            </div>
            <div id="modal-clicks-badge" class="mt-5 inline-flex items-center gap-1.5 px-3 py-1.5 bg-ans-dark-green/5 text-ans-dark-green rounded-xl text-xs font-semibold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                <span>Used <span id="modal-clicks-count">0</span> times by students/teachers</span>
            </div>
        </div>
        <!-- Footer -->
        <div class="p-6 border-t border-gray-100 flex-shrink-0 space-y-3">
            <a id="modal-open-btn" href="#" target="_blank"
               class="flex items-center justify-center gap-2 w-full bg-gradient-to-r from-ans-dark-green to-ans-seal-green hover:from-ans-seal-green hover:to-ans-dark-green text-white font-bold py-4 rounded-xl shadow-lg shadow-ans-dark-green/20 hover:shadow-xl transition-all text-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                Open Tool
            </a>
            <a id="modal-reviews-btn" href="#"
               class="flex items-center justify-center gap-2 w-full border-2 border-ans-dark-green text-ans-dark-green hover:bg-ans-dark-green/5 font-bold py-3 rounded-xl transition-all text-sm">
                ⭐ View Reviews &amp; Ratings
            </a>
        </div>
    </div>
</div>
@endsection

<script>
// State
let currentCategory = 'all';
let currentSearchQuery = '';
let activeTool = null;

// Parse Query URL Category & Search
(function() {
    const params = new URLSearchParams(window.location.search);
    const cat = params.get('category');
    const search = params.get('search');
    
    if (search) {
        currentSearchQuery = search.toLowerCase().trim();
        const searchInput = document.getElementById('global-search');
        if (searchInput) {
            searchInput.value = search;
            const kbdBadge = document.getElementById('search-kbd');
            if (kbdBadge) kbdBadge.classList.add('opacity-0');
        }
    }

    if (cat) {
        currentCategory = cat;
        setTimeout(() => {
            let matchedBtn = null;
            document.querySelectorAll('.filter-category-btn').forEach(btn => {
                if (btn.dataset.category.toLowerCase() === cat.toLowerCase()) {
                    matchedBtn = btn;
                }
            });
            
            if (matchedBtn) {
                setActiveFilter(matchedBtn.id);
            } else {
                const staticPills = {
                    'workspace': 'filter-workspace',
                    'google workspace': 'filter-workspace',
                    '3rdparty': 'filter-3rdparty',
                    '3rd party': 'filter-3rdparty'
                };
                const activeId = staticPills[cat.toLowerCase()];
                if (activeId) {
                    setActiveFilter(activeId);
                } else {
                    applyFilters();
                }
            }
        }, 100);
    } else if (search) {
        applyFilters();
    }
})();

// Real-time Search input binding
const searchInput = document.getElementById('global-search');
if (searchInput) {
    searchInput.addEventListener('input', e => {
        currentSearchQuery = e.target.value.toLowerCase().trim();
        applyFilters();
    });
}

// Filter listeners
document.getElementById('filter-all').addEventListener('click', () => { currentCategory = 'all'; setActiveFilter('filter-all'); });
document.getElementById('filter-workspace').addEventListener('click', () => { currentCategory = 'workspace'; setActiveFilter('filter-workspace'); });
document.getElementById('filter-3rdparty').addEventListener('click', () => { currentCategory = '3rdparty'; setActiveFilter('filter-3rdparty'); });
document.getElementById('filter-favs').addEventListener('click', () => { currentCategory = 'favs'; setActiveFilter('filter-favs'); });

document.querySelectorAll('.filter-category-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        currentCategory = btn.dataset.category;
        setActiveFilter(btn.id);
    });
});

function setActiveFilter(id) {
    const staticFilters = ['filter-all', 'filter-workspace', 'filter-3rdparty', 'filter-favs'];
    staticFilters.forEach(btnId => {
        const btn = document.getElementById(btnId);
        if (!btn) return;
        if (btnId === id) {
            btn.className = 'text-xs font-semibold px-4 py-2 bg-ans-dark-green text-white rounded-full shadow-sm transition-all hover:shadow-md';
        } else {
            btn.className = 'text-xs font-semibold px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-full hover:border-ans-dark-green hover:text-ans-dark-green transition-all flex items-center gap-1.5';
        }
    });
    
    document.querySelectorAll('.filter-category-btn').forEach(btn => {
        if (btn.id === id) {
            btn.className = 'text-xs font-semibold px-4 py-2 bg-ans-dark-green text-white rounded-full shadow-sm transition-all hover:shadow-md filter-category-btn';
        } else {
            btn.className = 'text-xs font-semibold px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-full hover:border-ans-dark-green hover:text-ans-dark-green transition-all filter-category-btn';
        }
    });
    
    applyFilters();
}

function applyFilters() {
    const favs = getFavs();
    let officialVisibleCount = 0;
    let catalogVisibleCount = 0;
    let trendingVisibleCount = 0;
    
    // Filter Official Tools
    document.querySelectorAll('#official-section .group[data-tool]').forEach(card => {
        try {
            const tool = JSON.parse(card.dataset.tool);
            const name = tool.name.toLowerCase();
            const desc = tool.desc.toLowerCase();
            const cat = (tool.cat || 'General').toLowerCase();
            const type = (tool.type || '').toLowerCase();
            
            const matchesSearch = name.includes(currentSearchQuery) || 
                                  desc.includes(currentSearchQuery) || 
                                  cat.includes(currentSearchQuery);
            
            let matchesCategory = false;
            if (currentCategory === 'all') {
                matchesCategory = true;
            } else if (currentCategory === 'workspace') {
                matchesCategory = true; // All official tools fall under workspace/featured
            } else if (currentCategory === '3rdparty') {
                matchesCategory = false; // Official tools are institutional platforms
            } else if (currentCategory === 'favs') {
                matchesCategory = favs.includes(tool.name);
            } else {
                matchesCategory = (cat === currentCategory.toLowerCase());
            }
            
            if (matchesSearch && matchesCategory) {
                card.style.display = '';
                card.classList.add('animate-fade-in-up');
                officialVisibleCount++;
            } else {
                card.style.display = 'none';
                card.classList.remove('animate-fade-in-up');
            }
        } catch (err) {}
    });
    
    // Filter Trending Tools
    document.querySelectorAll('#trending-section .group[data-tool]').forEach(card => {
        try {
            const tool = JSON.parse(card.dataset.tool);
            const name = tool.name.toLowerCase();
            const desc = tool.desc.toLowerCase();
            const cat = (tool.cat || 'General').toLowerCase();
            const type = (tool.type || '').toLowerCase();
            
            const matchesSearch = name.includes(currentSearchQuery) || 
                                  desc.includes(currentSearchQuery) || 
                                  cat.includes(currentSearchQuery);
            
            let matchesCategory = false;
            if (currentCategory === 'all') {
                matchesCategory = true;
            } else if (currentCategory === 'workspace') {
                matchesCategory = (type === 'google workspace' || type === 'workspace');
            } else if (currentCategory === '3rdparty') {
                matchesCategory = (type === '3rd party');
            } else if (currentCategory === 'favs') {
                matchesCategory = favs.includes(tool.name);
            } else {
                matchesCategory = (cat === currentCategory.toLowerCase());
            }
            
            if (matchesSearch && matchesCategory) {
                card.style.display = '';
                card.classList.add('animate-fade-in-up');
                trendingVisibleCount++;
            } else {
                card.style.display = 'none';
                card.classList.remove('animate-fade-in-up');
            }
        } catch (err) {}
    });
    
    // Filter Catalog Tools
    document.querySelectorAll('#catalog-grid .group[data-tool]').forEach(card => {
        try {
            const tool = JSON.parse(card.dataset.tool);
            const name = tool.name.toLowerCase();
            const desc = tool.desc.toLowerCase();
            const cat = (tool.cat || 'General').toLowerCase();
            const type = (tool.type || '').toLowerCase();
            
            const matchesSearch = name.includes(currentSearchQuery) || 
                                  desc.includes(currentSearchQuery) || 
                                  cat.includes(currentSearchQuery);
            
            let matchesCategory = false;
            if (currentCategory === 'all') {
                matchesCategory = true;
            } else if (currentCategory === 'workspace') {
                matchesCategory = (type === 'google workspace' || type === 'workspace');
            } else if (currentCategory === '3rdparty') {
                matchesCategory = (type === '3rd party');
            } else if (currentCategory === 'favs') {
                matchesCategory = favs.includes(tool.name);
            } else {
                matchesCategory = (cat === currentCategory.toLowerCase());
            }
            
            if (matchesSearch && matchesCategory) {
                card.style.display = '';
                card.classList.add('animate-fade-in-up');
                catalogVisibleCount++;
            } else {
                card.style.display = 'none';
                card.classList.remove('animate-fade-in-up');
            }
        } catch (err) {}
    });

    // Dynamic header visibility based on filter visibility
    const officialSection = document.getElementById('official-section');
    if (officialSection) {
        if (officialVisibleCount === 0) {
            officialSection.style.display = 'none';
        } else {
            officialSection.style.display = '';
        }
    }

    const trendingSection = document.getElementById('trending-section');
    if (trendingSection) {
        if (trendingVisibleCount === 0) {
            trendingSection.style.display = 'none';
        } else {
            trendingSection.style.display = '';
        }
    }

    // Dynamic global empty state toggle
    const emptyState = document.getElementById('catalog-empty-state');
    if (emptyState) {
        if (officialVisibleCount === 0 && catalogVisibleCount === 0 && trendingVisibleCount === 0) {
            emptyState.classList.remove('hidden');
        } else {
            emptyState.classList.add('hidden');
        }
    }
}

// Banner Novedades functions
document.addEventListener('DOMContentLoaded', () => {
    const banner = document.getElementById('new-app-banner');
    if (banner) {
        const toolId = banner.dataset.toolId;
        if (localStorage.getItem('dismissed_new_tool_banner_' + toolId)) {
            banner.classList.add('hidden');
        }
    }
    if (typeof initHearts === 'function') {
        initHearts();
    }
});

function dismissNewAppBanner(toolId) {
    localStorage.setItem('dismissed_new_tool_banner_' + toolId, 'true');
    const banner = document.getElementById('new-app-banner');
    if (banner) {
        banner.classList.add('hidden');
    }
}

// Reset filters back to default
function resetSearchFilters() {
    currentSearchQuery = '';
    currentCategory = 'all';
    
    const globSearch = document.getElementById('global-search');
    if (globSearch) {
        globSearch.value = '';
        globSearch.blur();
    }
    
    const kbdBadge = document.getElementById('search-kbd');
    if (kbdBadge) kbdBadge.classList.remove('opacity-0');
    
    setActiveFilter('filter-all');
}

// Favorites Storage helpers
function getFavs() {
    try {
        return JSON.parse(localStorage.getItem('ains-favs')) || [];
    } catch(e) {
        return [];
    }
}

function toggleFav(name) {
    let favs = getFavs();
    if (favs.includes(name)) {
        favs = favs.filter(n => n !== name);
    } else {
        favs.push(name);
    }
    localStorage.setItem('ains-favs', JSON.stringify(favs));
    return favs.includes(name);
}

function updateCardHeartUI(btn, isFav) {
    const svg = btn.querySelector('svg');
    if (!svg) return;
    if (isFav) {
        btn.classList.remove('text-gray-400');
        btn.classList.add('text-red-500');
        svg.setAttribute('fill', 'currentColor');
    } else {
        btn.classList.remove('text-red-500');
        btn.classList.add('text-gray-400');
        svg.setAttribute('fill', 'none');
    }
}

function syncHeartsForTool(toolName, isFav) {
    document.querySelectorAll(`.card-fav-btn[data-tool-name="${toolName}"]`).forEach(btn => {
        updateCardHeartUI(btn, isFav);
    });
}

function toggleCardFavorite(event, btn, toolName, toolId) {
    if (event) event.stopPropagation();
    const isFav = toggleFav(toolName);
    syncHeartsForTool(toolName, isFav);
    applyFilters();
    
    const csrfTokenEl = document.querySelector('meta[name="csrf-token"]');
    if (csrfTokenEl && toolId) {
        const csrfToken = csrfTokenEl.getAttribute('content');
        fetch(`/tools/${toolId}/favorite`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                if (response.status === 401 || response.status === 403) {
                    toggleFav(toolName);
                    syncHeartsForTool(toolName, !isFav);
                    applyFilters();
                }
                return response.json().then(data => { throw new Error(data.error || 'Sync error') });
            }
            return response.json();
        })
        .then(data => {
            if (data.favorited !== isFav) {
                localStorage.setItem('ains-favs', JSON.stringify(
                    data.favorited 
                        ? [...new Set([...getFavs(), toolName])]
                        : getFavs().filter(n => n !== toolName)
                ));
                syncHeartsForTool(toolName, data.favorited);
                applyFilters();
            }
        })
        .catch(err => console.error('Error syncing favorite:', err));
    }
}

function toggleFavCurrent() {
    if (!activeTool) return;
    const isFav = toggleFav(activeTool.name);
    updateFavBtnState(isFav);
    syncHeartsForTool(activeTool.name, isFav);
    applyFilters();
    
    const csrfTokenEl = document.querySelector('meta[name="csrf-token"]');
    if (csrfTokenEl && activeTool.id) {
        const csrfToken = csrfTokenEl.getAttribute('content');
        fetch(`/tools/${activeTool.id}/favorite`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        }).catch(err => console.error('Error syncing favorite:', err));
    }
}

function initHearts() {
    const favs = getFavs();
    document.querySelectorAll('.card-fav-btn').forEach(btn => {
        const name = btn.dataset.toolName;
        updateCardHeartUI(btn, favs.includes(name));
    });
}

function updateFavBtnState(isFav) {
    const btn = document.getElementById('modal-fav-btn');
    if (isFav) {
        btn.innerHTML = `<svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>`;
        btn.title = "Remove from Favorites";
    } else {
        btn.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.381-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>`;
        btn.title = "Add to Favorites";
    }
}

// Click metrics storage
function getClicks(name) {
    try {
        const clicks = JSON.parse(localStorage.getItem('ains-clicks')) || {};
        return clicks[name] || 0;
    } catch(e) {
        return 0;
    }
}

function incrementClicks(name) {
    try {
        const clicks = JSON.parse(localStorage.getItem('ains-clicks')) || {};
        clicks[name] = (clicks[name] || 0) + 1;
        localStorage.setItem('ains-clicks', JSON.stringify(clicks));
        return clicks[name];
    } catch(e) {
        return 1;
    }
}

function openToolModal(tool) {
    activeTool = tool;
    
    // Populate details
    document.getElementById('modal-name').textContent = tool.name;
    document.getElementById('modal-category').textContent = tool.cat || 'General';
    document.getElementById('modal-desc').textContent = tool.desc;
    document.getElementById('modal-url').textContent = tool.url;
    document.getElementById('modal-url').href = tool.url;
    document.getElementById('modal-open-btn').href = tool.url;

    // Logo
    const logoEl = document.getElementById('modal-logo');
    logoEl.className = "w-16 h-16 rounded-2xl flex items-center justify-center border-2 border-white/20 flex-shrink-0 overflow-hidden";
    if (tool.logo) {
        logoEl.classList.add("bg-white/10");
        logoEl.innerHTML = `<img src="${tool.logo}" class="w-full h-full object-cover">`;
    } else {
        const vibrantGradients = [
            'from-purple-500 to-indigo-600',
            'from-pink-500 to-rose-600',
            'from-emerald-500 to-teal-600',
            'from-blue-500 to-indigo-600',
            'from-amber-500 to-orange-600',
            'from-cyan-500 to-blue-600',
            'from-violet-500 to-fuchsia-600',
            'from-red-500 to-ans-orange'
        ];
        let hash = 0;
        for (let i = 0; i < tool.name.length; i++) {
            hash += tool.name.charCodeAt(i);
        }
        const grad = vibrantGradients[hash % vibrantGradients.length];
        grad.split(' ').forEach(cls => logoEl.classList.add(cls));
        logoEl.classList.add('bg-gradient-to-br');
        logoEl.innerHTML = `<span class="text-white font-bold text-2xl shadow-sm">${tool.name.charAt(0)}</span>`;
    }

    // Type badge
    const badge = document.getElementById('modal-type-badge');
    if (tool.type === 'Google Workspace' || tool.type === 'Workspace') {
        badge.textContent = 'Google Workspace';
        badge.className = 'text-[10px] font-bold bg-white/20 text-white px-2.5 py-1 rounded-full uppercase tracking-wider mt-2 inline-block';
    } else {
        badge.textContent = tool.type;
        badge.className = 'text-[10px] font-bold bg-ans-orange/20 text-ans-orange px-2.5 py-1 rounded-full uppercase tracking-wider mt-2 inline-block';
    }

    // Favorites button state
    updateFavBtnState(getFavs().includes(tool.name));

    // Clicks metrics state
    const clickCount = getClicks(tool.name);
    document.getElementById('modal-clicks-count').textContent = clickCount;

    // View reviews button link mapping
    const reviewsBtn = document.getElementById('modal-reviews-btn');
    if (reviewsBtn) {
        if (tool.id) {
            reviewsBtn.href = `/tools/${tool.id}`;
            reviewsBtn.classList.remove('hidden');
        } else {
            reviewsBtn.classList.add('hidden'); // hidden for front-end fallback/demo items
        }
    }

    // Show modal
    const modal = document.getElementById('tool-modal');
    const panel = document.getElementById('modal-panel');
    
    modal.classList.remove('hidden');
    panel.offsetHeight; // Force browser layout recalculation
    panel.classList.remove('translate-x-full');
    document.body.style.overflow = 'hidden';
}

function closeToolModal() {
    const modal = document.getElementById('tool-modal');
    const panel = document.getElementById('modal-panel');
    
    panel.classList.add('translate-x-full');
    
    setTimeout(() => {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
        activeTool = null;
    }, 300);
}

// Bind clicks on open button to track institutional usage
document.getElementById('modal-open-btn').addEventListener('click', () => {
    if (activeTool) {
        const newCount = incrementClicks(activeTool.name);
        document.getElementById('modal-clicks-count').textContent = newCount;
    }
});

document.addEventListener('keydown', e => { if(e.key === 'Escape') closeToolModal(); });
</script>

@endsection
