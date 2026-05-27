@extends('layouts.app')

@section('content')

@php
    $allTools = is_iterable($tools) ? $tools : collect();
    $officialCount = isset($officialTools) ? $officialTools->count() : 0;
    $totalCount = (is_object($tools) && method_exists($tools, 'total') ? $tools->total() : $allTools->count()) + $officialCount;
    $categoriesCount = isset($categories) && is_iterable($categories) ? $categories->count() : 5;
    
    // Banner configuration (recently approved/added in last 15 days)
    $newestTool = $allTools->sortByDesc('created_at')->first();
    $showNewestBanner = false;
    if ($newestTool && $newestTool->created_at) {
        $showNewestBanner = \Carbon\Carbon::parse($newestTool->created_at)->gt(now()->subDays(15));
    }
    
    // Trending tools (top 3 by clicks)
    $trendingTools = $allTools->sortByDesc('click_count')->take(3);

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
<!-- HERO SECTION — Dynamic role-based landing views            -->
<!-- ═══════════════════════════════════════════════════════════ -->
@auth
    @if(Auth::user()->isStudent())
        <!-- STUDENT HERO -->
        <div class="relative -mx-8 -mt-8 mb-12 overflow-hidden">
            <div class="bg-gradient-to-br from-indigo-600 via-purple-600 to-ans-dark-green px-10 py-14 md:py-16 relative">
                <!-- Decorative floating shapes -->
                <div class="absolute top-6 right-16 w-32 h-32 bg-white/5 rounded-full blur-2xl animate-float"></div>
                <div class="absolute bottom-4 left-24 w-24 h-24 bg-ans-orange/10 rounded-full blur-xl animate-float" style="animation-delay: 1s;"></div>
                <div class="absolute top-1/2 right-1/3 w-16 h-16 bg-ans-light-green/10 rounded-full blur-lg animate-pulse-soft"></div>

                <div class="max-w-7xl mx-auto relative z-10">
                    <div class="flex flex-col gap-6 md:gap-8">
                        <!-- Upper part: Title & Ask AI Button -->
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                            <div class="animate-fade-in-up" style="animation-duration: 0.5s;">
                                <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-full px-4 py-1.5 mb-4 border border-white/10">
                                    <span class="w-2 h-2 bg-ans-orange rounded-full animate-pulse-soft"></span>
                                    <span class="text-xs font-semibold text-white/90 tracking-wide uppercase">Student Hub</span>
                                </div>
                                <h1 class="text-3xl md:text-4xl font-heading font-extrabold text-white leading-tight">
                                    Discover Your <span class="text-ans-orange">AI Tools</span>
                                </h1>
                                <p class="text-white/80 mt-2 max-w-lg text-sm">
                                    Explore approved apps, get answers, and build projects with smart AI assistants.
                                </p>
                            </div>
                            <div class="flex-shrink-0 animate-fade-in-up" style="animation-duration: 0.6s;">
                                <button onclick="toggleChatbot()" class="flex items-center gap-2 bg-white text-indigo-700 hover:bg-white/95 px-6 py-3.5 rounded-2xl text-sm font-bold shadow-lg transition-all hover:scale-105">
                                    <svg class="w-5 h-5 text-ans-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                                    Ask AI Companion
                                </button>
                            </div>
                        </div>

                        <!-- Mid part: Premium AI Search Box -->
                        <div class="w-full max-w-2xl mx-auto animate-fade-in-up" style="animation-duration: 0.65s;">
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                                    <svg class="h-6 w-6 text-white/50 group-focus-within:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input type="search" id="hero-ai-search" placeholder="Search AI tools..." class="block w-full pl-14 pr-6 py-4 bg-white/20 hover:bg-white/25 focus:bg-white/30 backdrop-blur-xl border border-white/20 focus:border-white/40 rounded-2xl text-base text-white placeholder-white/60 focus:outline-none focus:ring-4 focus:ring-white/10 transition-all shadow-xl">
                            </div>
                        </div>

                        <!-- Bottom part: Scrollable Favorites -->
                        <div class="animate-fade-in-up mt-2" style="animation-duration: 0.75s;">
                            @if($favoriteTools && $favoriteTools->count() > 0)
                                <h3 class="text-xs font-semibold text-white/80 uppercase tracking-wider mb-3">Your Favorites</h3>
                                <div class="flex gap-4 overflow-x-auto pb-2 scrollbar-thin scrollbar-thumb-white/20 scrollbar-track-transparent">
                                    @foreach($favoriteTools as $favTool)
                                        <div data-tool='@include("partials._tool_data", ["tool" => $favTool])' onclick="openToolModal(JSON.parse(this.dataset.tool))" class="flex-shrink-0 bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/10 hover:border-white/20 rounded-2xl p-4 flex items-center gap-3 cursor-pointer transition-all duration-300 w-64 shadow-md hover:-translate-y-0.5">
                                            <div class="w-10 h-10 bg-ans-orange text-white rounded-xl flex items-center justify-center font-bold text-sm shadow-md flex-shrink-0">
                                                @if($favTool->logo_url)
                                                    <img src="{{ asset($favTool->logo_url) }}" alt="{{ $favTool->name }}" class="w-full h-full object-cover rounded-xl">
                                                @else
                                                    {{ substr($favTool->name, 0, 1) }}
                                                @endif
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <h4 class="text-sm font-bold text-white truncate">{{ $favTool->name }}</h4>
                                                <p class="text-[11px] text-white/60 truncate">{{ $favTool->categoryRelation?->name }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-xs text-white/60 italic">You don't have any favorited tools yet. Tap the heart icons below to add them!</p>
                            @endif
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

        <!-- STUDENT DASHBOARD ADDITIONAL SECTIONS -->
        <div class="max-w-7xl mx-auto px-4 md:px-8 mt-12 mb-14 animate-fade-in-up">
            @if(isset($studentPrompts) && $studentPrompts->count() > 0)
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-1.5 h-8 bg-gradient-to-b from-indigo-500 to-purple-600 rounded-full"></div>
                    <div>
                        <h3 class="text-xl font-heading font-bold text-gray-900">AI Prompt Blueprints</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Copy and use these structured prompts with our AI Companion</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                    @foreach($studentPrompts as $prompt)
                        <div class="bg-white rounded-2xl border border-gray-100 p-5 hover:shadow-lg transition-all duration-300 flex flex-col justify-between group shadow-sm">
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    @php
                                        $catMap = [
                                            'Evaluación' => 'Evaluation',
                                            'Planificación' => 'Planning',
                                            'Administración' => 'Administration',
                                            'Retroalimentación' => 'Feedback',
                                            'Investigación' => 'Research',
                                            'Diseño de Actividades' => 'Activity Design',
                                            'Desarrollo Profesional' => 'Professional Development'
                                        ];
                                        $displayCategory = $catMap[$prompt->category] ?? $prompt->category;

                                        $compMap = [
                                            'Baja' => 'Low',
                                            'Media' => 'Medium',
                                            'Alta' => 'High',
                                            'Básico' => 'Basic',
                                            'Intermedio' => 'Intermediate',
                                            'Avanzado' => 'Advanced'
                                        ];
                                        $displayComplexity = $compMap[$prompt->complexity] ?? $prompt->complexity;

                                        $compColor = match($prompt->complexity) {
                                            'Baja', 'Básico' => 'bg-green-50 text-green-600',
                                            'Media', 'Intermedio' => 'bg-yellow-50 text-yellow-600',
                                            'Alta', 'Avanzado' => 'bg-red-50 text-red-600',
                                            default => 'bg-gray-50 text-gray-600'
                                        };
                                    @endphp
                                    <span class="text-[10px] font-bold bg-indigo-50 text-indigo-600 px-2.5 py-0.5 rounded-full uppercase tracking-wider">{{ $displayCategory }}</span>
                                    <span class="text-[10px] font-bold {{ $compColor }} px-2.5 py-0.5 rounded-full uppercase tracking-wider">{{ $displayComplexity }}</span>
                                </div>
                                <h4 class="font-heading font-bold text-gray-900 text-base leading-tight mb-2 group-hover:text-indigo-600 transition-colors">{{ $prompt->title }}</h4>
                                <p class="text-xs text-gray-500 leading-relaxed mb-4 line-clamp-3">{{ $prompt->description }}</p>
                            </div>
                            <div class="mt-auto">
                                <div class="bg-gray-50 rounded-xl p-3 border border-gray-100 text-[11px] font-mono text-gray-600 line-clamp-3 mb-3 select-all relative group-hover:bg-gray-100 transition-colors">
                                    {{ $prompt->prompt_text }}
                                </div>
                                <button onclick="copyPrompt(this, {!! json_encode($prompt->prompt_text) !!})" class="w-full flex items-center justify-center gap-1.5 px-3 py-2.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 hover:text-indigo-700 border border-indigo-100 hover:border-indigo-200 rounded-xl text-xs font-bold transition-all shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m-5 4h5m-5 4h5m-5 4h5"></path></svg>
                                    Copy Template
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @elseif(Auth::user()->isTeacher())
        <!-- TEACHER HERO -->
        <div class="relative -mx-8 -mt-8 mb-12 overflow-hidden">
            <div class="bg-gradient-to-br from-ans-dark-green via-ans-seal-green to-emerald-900 px-10 py-14 md:py-16 relative">
                <!-- Decorative floating shapes -->
                <div class="absolute top-6 right-16 w-32 h-32 bg-white/5 rounded-full blur-2xl animate-float"></div>
                <div class="absolute bottom-4 left-24 w-24 h-24 bg-ans-orange/10 rounded-full blur-xl animate-float" style="animation-delay: 1s;"></div>
                <div class="absolute top-1/2 right-1/3 w-16 h-16 bg-ans-light-green/10 rounded-full blur-lg animate-pulse-soft"></div>

                <div class="max-w-7xl mx-auto relative z-10">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-8">
                        <!-- Left: Title -->
                        <div class="flex-1 animate-fade-in-up" style="animation-duration: 0.5s;">
                            <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-full px-4 py-1.5 mb-5 border border-white/10">
                                <span class="w-2 h-2 bg-ans-orange rounded-full animate-pulse-soft"></span>
                                <span class="text-xs font-semibold text-white/90 tracking-wide uppercase">Teacher Workspace</span>
                            </div>
                            <h1 class="text-4xl md:text-5xl font-heading font-extrabold text-white leading-tight tracking-tight">
                                Innovation<br>
                                <span class="text-ans-orange">Dashboard</span>
                            </h1>
                            <div class="flex flex-wrap gap-4 mt-6">
                                <a href="{{ route('lesson-plans.create') }}" class="inline-flex items-center gap-2 bg-ans-orange hover:bg-[#e67600] text-white px-6 py-3.5 rounded-2xl text-sm font-bold shadow-lg shadow-ans-orange/20 transition-all hover:scale-105 hover:-translate-y-0.5">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                                    Create Lesson Plan
                                </a>
                            </div>
                            
                            <!-- Recent badges earned -->
                            @if($latestBadges && $latestBadges->count() > 0)
                                <div class="mt-8 flex flex-wrap items-center gap-3">
                                    <span class="text-xs text-white/60 font-semibold uppercase tracking-wider">Recent Badges:</span>
                                    @foreach($latestBadges as $badge)
                                        <a href="{{ route('badges.show', $badge->slug) }}" class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 border border-white/10 rounded-full px-3 py-1.5 text-xs text-white transition-all hover:scale-105">
                                            @if($badge->image_path)
                                                <img src="{{ asset($badge->image_path) }}" alt="{{ $badge->name }}" class="w-5 h-5 object-cover rounded-full">
                                            @else
                                                <span class="w-2 h-2 rounded-full bg-ans-orange"></span>
                                            @endif
                                            {{ $badge->name }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Right: Interactive Metrics -->
                        <div class="grid grid-cols-3 gap-4 md:gap-5 animate-fade-in-up" style="animation-duration: 0.7s;">
                            <!-- Lesson Plans Card -->
                            <a href="{{ route('lesson-plans.index') }}" class="bg-white/10 hover:bg-white/15 border border-white/10 rounded-2xl p-5 text-center min-w-[115px] cursor-pointer transition-all hover:scale-105 shadow-md flex flex-col justify-between">
                                <div class="text-3xl md:text-4xl font-heading font-extrabold text-white" id="count-plans">0</div>
                                <div>
                                    <p class="text-xs font-bold text-white/90 mt-2">Lesson Plans</p>
                                    <span class="text-[9px] text-white/50 block mt-1">View list ➔</span>
                                </div>
                            </a>
                            <!-- Innovation Badges Card -->
                            <a href="{{ route('badges.index') }}" class="bg-white/10 hover:bg-white/15 border border-white/10 rounded-2xl p-5 text-center min-w-[115px] cursor-pointer transition-all hover:scale-105 shadow-md flex flex-col justify-between">
                                <div class="text-3xl md:text-4xl font-heading font-extrabold text-ans-orange" id="count-badges">0</div>
                                <div>
                                    <p class="text-xs font-bold text-white/90 mt-2">Badges Earned</p>
                                    <span class="text-[9px] text-white/50 block mt-1">Badge hub ➔</span>
                                </div>
                            </a>
                            <!-- Favorite Tools Card -->
                            <div onclick="document.getElementById('catalog-section').scrollIntoView({behavior: 'smooth'}); setTimeout(() => document.getElementById('filter-favs').click(), 400);" class="bg-white/10 hover:bg-white/15 border border-white/10 rounded-2xl p-5 text-center min-w-[115px] cursor-pointer transition-all hover:scale-105 shadow-md flex flex-col justify-between">
                                <div class="text-3xl md:text-4xl font-heading font-extrabold text-ans-light-green" id="count-favorites">0</div>
                                <div>
                                    <p class="text-xs font-bold text-white/90 mt-2">Favorite Tools</p>
                                    <span class="text-[9px] text-white/50 block mt-1">Filter below ➔</span>
                                </div>
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

        <!-- TEACHER DASHBOARD ADDITIONAL SECTIONS -->
        <div class="max-w-7xl mx-auto px-4 md:px-8 mt-12 mb-14 animate-fade-in-up">
            <!-- Recent Lesson Plans and Insignias side-by-side -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-12">
                <!-- Recent Lesson Plans -->
                <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 p-6 flex flex-col justify-between shadow-sm">
                    <div>
                        <div class="flex items-center justify-between mb-5">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-ans-dark-green" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m-6-8h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <h3 class="font-heading font-bold text-gray-900 text-lg">Your Recent Lesson Plans</h3>
                            </div>
                            <a href="{{ route('lesson-plans.index') }}" class="text-xs font-bold text-ans-dark-green hover:underline">View All ➔</a>
                        </div>
                        
                        @if($recentLessonPlans->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs border-collapse">
                                    <thead>
                                        <tr class="border-b border-gray-100 text-gray-400 font-bold uppercase tracking-wider">
                                            <th class="py-2.5">Title</th>
                                            <th class="py-2.5">Subject</th>
                                            <th class="py-2.5">Grade</th>
                                            <th class="py-2.5">Created</th>
                                            <th class="py-2.5 text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50 text-gray-700">
                                        @foreach($recentLessonPlans as $plan)
                                            <tr class="hover:bg-gray-50/50 transition-colors">
                                                <td class="py-3 font-bold text-gray-900">{{ $plan->title }}</td>
                                                <td class="py-3">{{ $plan->subject }}</td>
                                                <td class="py-3">{{ $plan->grade_level }}</td>
                                                <td class="py-3 text-gray-400">{{ $plan->created_at->format('M d, Y') }}</td>
                                                <td class="py-3 text-right space-x-1.5 whitespace-nowrap">
                                                    <a href="{{ route('lesson-plans.show', $plan->id) }}" class="inline-flex items-center justify-center w-7 h-7 bg-gray-50 hover:bg-gray-100 border border-gray-200 text-gray-600 rounded-lg transition-colors" title="View">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                    </a>
                                                    <a href="{{ route('lesson-plans.edit', $plan->id) }}" class="inline-flex items-center justify-center w-7 h-7 bg-gray-50 hover:bg-gray-100 border border-gray-200 text-gray-600 rounded-lg transition-colors" title="Edit">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                    </a>
                                                    <a href="{{ route('lesson-plans.export', $plan->id) }}" class="inline-flex items-center justify-center w-7 h-7 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-600 rounded-lg transition-colors" title="Export PDF">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-8 bg-gray-50/50 rounded-2xl border border-dashed border-gray-200 mt-2">
                                <p class="text-xs text-gray-400">You haven't generated any lesson plans yet.</p>
                                <a href="{{ route('lesson-plans.create') }}" class="text-xs font-bold text-ans-dark-green hover:underline mt-1.5 inline-block">Generate your first plan now ➔</a>
                            </div>
                        @endif
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-50 flex justify-between items-center text-[10px] text-gray-400">
                        <span>Pedagogical Workspace</span>
                        <a href="{{ route('lesson-plans.create') }}" class="text-ans-orange hover:text-[#e67600] font-bold flex items-center gap-1">Create New Plan <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg></a>
                    </div>
                </div>

                <!-- Badge Progress -->
                @php
                    $totalBadgesCount = \App\Models\Badge::count();
                    $earnedBadgesCount = Auth::user()->badges()->count();
                    $progressPercent = $totalBadgesCount > 0 ? round(($earnedBadgesCount / $totalBadgesCount) * 100) : 0;
                @endphp
                <div class="bg-white rounded-2xl border border-gray-100 p-6 flex flex-col justify-between shadow-sm">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-ans-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                <h3 class="font-heading font-bold text-gray-900 text-lg">EdTech Badges</h3>
                            </div>
                            <a href="{{ route('badges.index') }}" class="text-xs font-bold text-ans-orange hover:underline">Badges ➔</a>
                        </div>
                        
                        <div class="my-4 text-center">
                            <div class="relative inline-flex items-center justify-center">
                                <div class="w-20 h-20 bg-ans-orange/5 rounded-full flex items-center justify-center font-black text-2xl text-ans-orange border border-ans-orange/10">
                                    {{ $progressPercent }}%
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-2 font-medium">You have completed {{ $earnedBadgesCount }} of {{ $totalBadgesCount }} certifications</p>
                        </div>

                        <!-- Progress Bar -->
                        <div class="w-full bg-gray-50 h-2.5 rounded-full overflow-hidden mb-5 border border-gray-100">
                            <div class="bg-gradient-to-r from-ans-orange to-ans-yellow h-full rounded-full transition-all duration-1000" style="width: {{ $progressPercent }}%"></div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <a href="{{ route('badge-suggestions.create') }}" class="block text-center text-xs font-bold text-gray-600 hover:text-gray-900 bg-gray-50 hover:bg-gray-100 border border-gray-200 py-2.5 rounded-xl transition-all">
                            💡 Suggest New Badge
                        </a>
                    </div>
                </div>
            </div>

            <!-- Teacher Prompt Tips -->
            @if(isset($teacherPrompts) && $teacherPrompts->count() > 0)
                <div class="flex items-center gap-3 mb-6 mt-12">
                    <div class="w-1.5 h-8 bg-gradient-to-b from-ans-light-green to-ans-dark-green rounded-full"></div>
                    <div>
                        <h3 class="text-xl font-heading font-bold text-gray-900">Pedagogical Prompts</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Quickly copy these prompts for rubrics, lesson structure and student feedback</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                    @foreach($teacherPrompts as $prompt)
                        <div class="bg-white rounded-2xl border border-gray-100 p-5 hover:shadow-lg transition-all duration-300 flex flex-col justify-between group shadow-sm">
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    @php
                                        $catMap = [
                                            'Evaluación' => 'Evaluation',
                                            'Planificación' => 'Planning',
                                            'Administración' => 'Administration',
                                            'Retroalimentación' => 'Feedback',
                                            'Investigación' => 'Research',
                                            'Diseño de Actividades' => 'Activity Design',
                                            'Desarrollo Profesional' => 'Professional Development'
                                        ];
                                        $displayCategory = $catMap[$prompt->category] ?? $prompt->category;

                                        $compMap = [
                                            'Baja' => 'Low',
                                            'Media' => 'Medium',
                                            'Alta' => 'High',
                                            'Básico' => 'Basic',
                                            'Intermedio' => 'Intermediate',
                                            'Avanzado' => 'Advanced'
                                        ];
                                        $displayComplexity = $compMap[$prompt->complexity] ?? $prompt->complexity;

                                        $compColor = match($prompt->complexity) {
                                            'Baja', 'Básico' => 'bg-green-50 text-green-600',
                                            'Media', 'Intermedio' => 'bg-yellow-50 text-yellow-600',
                                            'Alta', 'Avanzado' => 'bg-red-50 text-red-600',
                                            default => 'bg-gray-50 text-gray-600'
                                        };
                                    @endphp
                                    <span class="text-[10px] font-bold bg-ans-dark-green/10 text-ans-dark-green px-2.5 py-0.5 rounded-full uppercase tracking-wider">{{ $displayCategory }}</span>
                                    <span class="text-[10px] font-bold {{ $compColor }} px-2.5 py-0.5 rounded-full uppercase tracking-wider">{{ $displayComplexity }}</span>
                                </div>
                                <h4 class="font-heading font-bold text-gray-900 text-base leading-tight mb-2 group-hover:text-ans-dark-green transition-colors">{{ $prompt->title }}</h4>
                                <p class="text-xs text-gray-500 leading-relaxed mb-4 line-clamp-3">{{ $prompt->description }}</p>
                            </div>
                            <div class="mt-auto">
                                <div class="bg-gray-50 rounded-xl p-3 border border-gray-100 text-[11px] font-mono text-gray-600 line-clamp-3 mb-3 select-all relative group-hover:bg-gray-100 transition-colors">
                                    {{ $prompt->prompt_text }}
                                </div>
                                <button onclick="copyPrompt(this, {!! json_encode($prompt->prompt_text) !!})" class="w-full flex items-center justify-center gap-1.5 px-3 py-2.5 bg-ans-dark-green/10 hover:bg-ans-dark-green/20 text-ans-dark-green hover:text-emerald-800 border border-ans-dark-green/20 rounded-xl text-xs font-bold transition-all shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m-5 4h5m-5 4h5m-5 4h5"></path></svg>
                                    Copy Template
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @else
        <!-- DEFAULT ADMIN HERO -->
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
            <!-- Curved bottom edge -->
            <div class="absolute bottom-0 left-0 right-0">
                <svg viewBox="0 0 1440 40" fill="none" class="w-full">
                    <path d="M0 40V0C240 30 480 40 720 40C960 40 1200 30 1440 0V40H0Z" fill="rgb(249 250 251 / 0.5)"/>
                </svg>
            </div>
        </div>

        <!-- ADMIN DASHBOARD ADDITIONAL SECTIONS -->
        <div class="max-w-7xl mx-auto px-4 md:px-8 mt-12 mb-14 animate-fade-in-up">
            <!-- Grid of Admin Quick Links -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
                <a href="{{ route('admin.tools.index') }}" class="bg-white border border-gray-100 hover:border-ans-orange/30 p-4 rounded-2xl flex flex-col items-center justify-center text-center hover:shadow-md transition-all shadow-sm group">
                    <span class="text-2xl mb-1 group-hover:scale-110 transition-transform">🛠️</span>
                    <span class="text-xs font-bold text-gray-800">Manage Tools</span>
                </a>
                <a href="{{ route('admin.users.index') }}" class="bg-white border border-gray-100 hover:border-ans-orange/30 p-4 rounded-2xl flex flex-col items-center justify-center text-center hover:shadow-md transition-all shadow-sm group">
                    <span class="text-2xl mb-1 group-hover:scale-110 transition-transform">👥</span>
                    <span class="text-xs font-bold text-gray-800">Manage Users</span>
                </a>
                <a href="{{ route('admin.chatbot-settings') }}" class="bg-white border border-gray-100 hover:border-ans-orange/30 p-4 rounded-2xl flex flex-col items-center justify-center text-center hover:shadow-md transition-all shadow-sm group">
                    <span class="text-2xl mb-1 group-hover:scale-110 transition-transform">🤖</span>
                    <span class="text-xs font-bold text-gray-800">Chatbot Config</span>
                </a>
                <a href="{{ route('admin.categories.index') }}" class="bg-white border border-gray-100 hover:border-ans-orange/30 p-4 rounded-2xl flex flex-col items-center justify-center text-center hover:shadow-md transition-all shadow-sm group">
                    <span class="text-2xl mb-1 group-hover:scale-110 transition-transform">🏷️</span>
                    <span class="text-xs font-bold text-gray-800">Categories</span>
                </a>
                <a href="{{ route('admin.badges.index') }}" class="bg-white border border-gray-100 hover:border-ans-orange/30 p-4 rounded-2xl flex flex-col items-center justify-center text-center hover:shadow-md transition-all shadow-sm group col-span-2 md:col-span-1">
                    <span class="text-2xl mb-1 group-hover:scale-110 transition-transform">🎖️</span>
                    <span class="text-xs font-bold text-gray-800">Manage Badges</span>
                </a>
            </div>

            <!-- Admin Approval Inbox Layout -->
            <div class="flex items-center gap-3 mb-6">
                <div class="w-1.5 h-8 bg-gradient-to-b from-ans-orange to-[#e67600] rounded-full"></div>
                <div>
                    <h3 class="text-xl font-heading font-bold text-gray-900">Admin Approval Inbox</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Approve or reject tool proposals, earned certifications evidence, and suggested badges</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Tab 1: Tool Proposals -->
                <div class="bg-white rounded-2xl border border-gray-100 p-5 flex flex-col justify-between shadow-sm">
                    <div>
                        <div class="flex items-center justify-between mb-4 border-b border-gray-50 pb-3">
                            <h4 class="font-bold text-sm text-gray-800 flex items-center gap-2">
                                <span class="w-2.5 h-2.5 bg-ans-orange rounded-full"></span>
                                Tool Requests ({{ $pendingRequestsCount }})
                            </h4>
                            <a href="{{ route('admin.requests.index') }}" class="text-[11px] font-bold text-ans-orange hover:underline">Queue ➔</a>
                        </div>
                        
                        @if($pendingRequests->count() > 0)
                            <div class="space-y-4 max-h-[350px] overflow-y-auto pr-1">
                                @foreach($pendingRequests as $req)
                                    <div class="bg-gray-50/50 hover:bg-gray-55 border border-gray-100 rounded-xl p-3.5 transition-colors">
                                        <h5 class="font-bold text-xs text-gray-900">{{ $req->name }}</h5>
                                        <p class="text-[10px] text-gray-400 mt-0.5 font-medium">{{ $req->category_name ?? 'General' }}</p>
                                        <p class="text-[11px] text-gray-500 mt-2 line-clamp-2 leading-relaxed">{{ $req->description }}</p>
                                        @if($req->url)
                                            <a href="{{ $req->url }}" target="_blank" class="text-[10px] text-blue-500 hover:underline mt-1.5 inline-block font-semibold">Visit Site ➔</a>
                                        @endif
                                        
                                        <div class="mt-3.5 flex items-center justify-end gap-2 border-t border-gray-100 pt-3">
                                            <form action="{{ route('admin.requests.approve', $req->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white text-[10px] font-bold px-3 py-1.5 rounded-lg shadow-sm transition-colors">Approve</button>
                                            </form>
                                            <form action="{{ route('admin.requests.reject', $req->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white text-[10px] font-bold px-3 py-1.5 rounded-lg shadow-sm transition-colors">Reject</button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-10 bg-gray-50/30 rounded-xl border border-dashed border-gray-200">
                                <span class="text-xl">✅</span>
                                <p class="text-[11px] text-gray-400 mt-1">No pending tool requests</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Tab 2: Badge Evidences -->
                <div class="bg-white rounded-2xl border border-gray-100 p-5 flex flex-col justify-between shadow-sm">
                    <div>
                        <div class="flex items-center justify-between mb-4 border-b border-gray-50 pb-3">
                            <h4 class="font-bold text-sm text-gray-800 flex items-center gap-2">
                                <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full"></span>
                                Badge Evidence ({{ $pendingEvidenceCount }})
                            </h4>
                            <a href="{{ route('admin.badge-evidence.index') }}" class="text-[11px] font-bold text-ans-dark-green hover:underline">Queue ➔</a>
                        </div>
                        
                        @if($pendingEvidence->count() > 0)
                            <div class="space-y-4 max-h-[350px] overflow-y-auto pr-1">
                                @foreach($pendingEvidence as $ev)
                                    <div class="bg-gray-50/50 hover:bg-gray-55 border border-gray-100 rounded-xl p-3.5 transition-colors">
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded-full bg-ans-orange text-white flex items-center justify-center font-bold text-[10px]">
                                                {{ substr($ev->user?->name ?? 'U', 0, 1) }}
                                            </div>
                                            <div>
                                                <h5 class="font-bold text-xs text-gray-900 leading-none">{{ $ev->user?->name ?? 'User' }}</h5>
                                                <span class="text-[9px] text-gray-400 font-medium mt-0.5 block">Applied for: {{ $ev->badge?->name ?? 'Badge' }}</span>
                                            </div>
                                        </div>
                                        <p class="text-[11px] text-gray-500 mt-2 line-clamp-2 leading-relaxed">{{ $ev->notes }}</p>
                                        
                                        <div class="mt-2.5 space-x-2">
                                            @if($ev->certificate_url)
                                                <a href="{{ $ev->certificate_url }}" target="_blank" class="text-[10px] text-blue-500 hover:underline inline-block font-semibold">View Cert URL ➔</a>
                                            @endif
                                            @if($ev->file_path)
                                                <a href="{{ asset('storage/' . $ev->file_path) }}" target="_blank" class="text-[10px] text-emerald-600 hover:underline inline-block font-semibold">View File ➔</a>
                                            @endif
                                        </div>
                                        
                                        <div class="mt-3.5 flex items-center justify-end gap-2 border-t border-gray-100 pt-3">
                                            <form action="{{ route('admin.badge-evidence.approve', $ev->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white text-[10px] font-bold px-3 py-1.5 rounded-lg shadow-sm transition-colors">Approve</button>
                                            </form>
                                            <form action="{{ route('admin.badge-evidence.reject', $ev->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white text-[10px] font-bold px-3 py-1.5 rounded-lg shadow-sm transition-colors">Reject</button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-10 bg-gray-50/30 rounded-xl border border-dashed border-gray-200">
                                <span class="text-xl">✅</span>
                                <p class="text-[11px] text-gray-400 mt-1">No pending badge evidence reviews</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Tab 3: Badge Suggestions -->
                <div class="bg-white rounded-2xl border border-gray-100 p-5 flex flex-col justify-between shadow-sm">
                    <div>
                        <div class="flex items-center justify-between mb-4 border-b border-gray-50 pb-3">
                            <h4 class="font-bold text-sm text-gray-800 flex items-center gap-2">
                                <span class="w-2.5 h-2.5 bg-purple-500 rounded-full"></span>
                                Badge Suggestions ({{ $pendingSuggestionsCount }})
                            </h4>
                            <a href="{{ route('admin.badge-suggestions.index') }}" class="text-[11px] font-bold text-purple-600 hover:underline">Queue ➔</a>
                        </div>
                        
                        @if($pendingSuggestions->count() > 0)
                            <div class="space-y-4 max-h-[350px] overflow-y-auto pr-1">
                                @foreach($pendingSuggestions as $sug)
                                    <div class="bg-gray-50/50 hover:bg-gray-55 border border-gray-100 rounded-xl p-3.5 transition-colors">
                                        <h5 class="font-bold text-xs text-gray-900 leading-tight">Badge: {{ $sug->name }}</h5>
                                        <span class="text-[9px] text-gray-400 font-medium block">Proposed by: {{ $sug->user?->name ?? 'User' }}</span>
                                        <p class="text-[11px] text-gray-500 mt-2 line-clamp-2 leading-relaxed">{{ $sug->description }}</p>
                                        
                                        @if($sug->certification_url)
                                            <a href="{{ $sug->certification_url }}" target="_blank" class="text-[10px] text-blue-500 hover:underline mt-1.5 inline-block font-semibold">Certification Link ➔</a>
                                        @endif
                                        
                                        <div class="mt-3.5 flex items-center justify-end gap-2 border-t border-gray-100 pt-3">
                                            <form action="{{ route('admin.badge-suggestions.approve', $sug->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white text-[10px] font-bold px-3 py-1.5 rounded-lg shadow-sm transition-colors">Approve</button>
                                            </form>
                                            <form action="{{ route('admin.badge-suggestions.reject', $sug->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white text-[10px] font-bold px-3 py-1.5 rounded-lg shadow-sm transition-colors">Reject</button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-10 bg-gray-50/30 rounded-xl border border-dashed border-gray-200">
                                <span class="text-xl">✅</span>
                                <p class="text-[11px] text-gray-400 mt-1">No pending badge suggestions</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
@else
    <!-- GUEST HERO -->
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
@endauth


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
                    data-tool='@include("partials._tool_data", ["tool" => $newestTool])'
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


@if($officialTools->count() > 0)
<div id="official-section" class="mb-14">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <div class="w-1.5 h-8 bg-gradient-to-b from-ans-orange to-ans-yellow rounded-full"></div>
            <div>
                <h3 class="text-xl font-heading font-bold text-gray-900">Official Tools</h3>
                <p class="text-xs text-gray-500 mt-0.5">Official tools & approved platforms</p>
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
                 data-tool='@include("partials._tool_data", ["tool" => $oTool])'
                 onclick="openToolModal(JSON.parse(this.dataset.tool))">
                <div class="absolute top-0 right-0 w-24 h-24 {{ $palette['bg'] }} rounded-bl-full"></div>
                
                <!-- Heart Button -->
                @include('partials._tool_heart', ['tool' => $oTool])

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
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <div class="w-1.5 h-8 bg-gradient-to-b from-red-500 to-ans-orange rounded-full"></div>
            <div>
                <h3 class="text-xl font-heading font-bold text-gray-900">Trending Now</h3>
                <p class="text-xs text-gray-500 mt-0.5">Most used AI platforms this week</p>
            </div>
        </div>
        <span class="inline-flex items-center gap-1.5 text-xs font-semibold bg-red-50 text-red-600 px-3 py-1.5 rounded-full border border-red-100">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
            </span>
            Live Stats
        </span>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($trendingTools as $tTool)
            <div class="group premium-card bg-gradient-to-br from-white to-red-50/10 rounded-2xl border border-gray-100 p-6 hover:shadow-xl hover:shadow-red-500/5 hover:-translate-y-1 transition-all duration-300 relative overflow-hidden cursor-pointer animate-fade-in-up"
                 style="animation-delay: {{ 0.05 * $loop->iteration }}s;"
                 data-tool='@include("partials._tool_data", ["tool" => $tTool])'
                 onclick="openToolModal(JSON.parse(this.dataset.tool))">
                 
                <!-- Heart Button -->
                @include('partials._tool_heart', ['tool' => $tTool])

                <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-red-500/5 to-transparent rounded-bl-full overflow-hidden"></div>
                @php
                    $grad = $tTool->logo_url ? '' : $getGradientForName($tTool->name);
                @endphp
                
                <div class="flex items-center gap-4 mb-4 relative z-10">
                    <div class="w-14 h-14 @if($tTool->logo_url) bg-gradient-to-br from-gray-100 to-gray-50 @else bg-gradient-to-br {{ $grad }} shadow-md shadow-black/10 @endif rounded-xl flex items-center justify-center @if(!$tTool->logo_url) text-white @else text-gray-400 @endif font-bold text-2xl group-hover:scale-105 transition-all duration-300 shadow-sm border border-gray-100/50 flex-shrink-0">
                        @if($tTool->logo_url)
                            <img src="{{ asset($tTool->logo_url) }}" alt="{{ $tTool->name }}" class="w-full h-full object-cover rounded-xl">
                        @else
                            {{ substr($tTool->name, 0, 1) }}
                        @endif
                    </div>
                    <div class="min-w-0">
                        <span class="text-[10px] font-bold text-red-500 uppercase tracking-wider block mb-0.5">Top {{ $loop->iteration }}</span>
                        <h4 class="font-heading font-bold text-gray-900 text-base leading-tight truncate group-hover:text-red-600 transition-colors">{{ $tTool->name }}</h4>
                    </div>
                </div>

                <p class="text-xs text-gray-500 leading-relaxed line-clamp-2 mb-4">{{ $tTool->description }}</p>
                
                <div class="flex items-center justify-between pt-3 border-t border-gray-100/80">
                    <div class="flex items-center gap-1.5">
                        @if($tTool->is_official)
                            <span class="text-[9px] font-bold bg-ans-orange/10 text-ans-orange px-2 py-0.5 rounded-full uppercase tracking-wider">★ Official</span>
                        @endif
                        @if($tTool->is_google_workspace)
                            <span class="text-[9px] font-bold bg-ans-blue/10 text-ans-blue px-2 py-0.5 rounded-full uppercase tracking-wider">Workspace</span>
                        @else
                            <span class="text-[9px] font-bold bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full uppercase tracking-wider">3rd Party</span>
                        @endif
                    </div>
                    <!-- Click Count Badge -->
                    <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-red-50 text-red-600 px-2.5 py-1 rounded-md border border-red-100 group-hover:scale-105 transition-transform duration-300">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.555-.398-1.15-.398-1.81a1 1 0 00-.263-.683c-.23-.25-.568-.4-.925-.4a1 1 0 00-.973.763 8.243 8.243 0 00-.17 1.834c0 3.328 2.673 6.033 6 6.033s6-2.705 6-6.033c0-2.457-1.11-4.66-2.847-6.033zM10 18a6 6 0 006-6c0-2.316-.763-4.454-2.055-6.183A5.022 5.022 0 0112 9.5a1 1 0 01-1 1 5.002 5.002 0 01-5-5c0 .324.017.653.053.98A7.002 7.002 0 0010 18z" clip-rule="evenodd"></path></svg>
                        {{ $tTool->click_count }}
                    </span>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif


<!-- ═══════════════════════════════════════════════════════════ -->
<!-- GENERAL CATALOG — Filterable tool grid                     -->
<!-- ═══════════════════════════════════════════════════════════ -->
<div class="mb-12" id="catalog-section">
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
                @if(strtolower($category->name) === 'google workspace' || $category->slug === 'google-workspace')
                    @continue
                @endif
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
        @include('partials.tool_grid')


    </div>

    <!-- Pagination Container -->
    <div id="pagination-container" class="mt-8 flex justify-center">
        {{ $tools->links() }}
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
    @include('partials._tool_detail_modal')
@endsection

<script>
// State
let currentCategory = 'all';
let currentSearchQuery = '';
let debounceTimer = null;

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

// Real-time Search input binding with Debounce
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
    let trendingVisibleCount = 0;
    
    // Filter Official Tools (local instant filtering)
    document.querySelectorAll('#official-section .group[data-tool]').forEach(card => {
        try {
            const tool = JSON.parse(card.dataset.tool);
            const name = tool.name.toLowerCase();
            const desc = tool.desc.toLowerCase();
            const cat = (tool.cat || 'General').toLowerCase();
            
            const matchesSearch = name.includes(currentSearchQuery) || 
                                  desc.includes(currentSearchQuery) || 
                                  cat.includes(currentSearchQuery);
            
            let matchesCategory = false;
            if (currentCategory === 'all') {
                matchesCategory = true;
            } else if (currentCategory === 'workspace') {
                matchesCategory = true;
            } else if (currentCategory === '3rdparty') {
                matchesCategory = false;
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
    
    // Filter Trending Tools (local instant filtering)
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

    // Dynamic header visibility based on filter visibility
    const officialSection = document.getElementById('official-section');
    if (officialSection) {
        officialSection.style.display = officialVisibleCount === 0 ? 'none' : '';
    }

    const trendingSection = document.getElementById('trending-section');
    if (trendingSection) {
        trendingSection.style.display = trendingVisibleCount === 0 ? 'none' : '';
    }

    // Server-side dynamic catalog grid filtering with 250ms debounce
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        fetchCatalogData(1);
    }, 250);
}

function fetchCatalogData(page = 1) {
    const favs = getFavs();
    const url = new URL(window.location.origin + window.location.pathname);
    url.searchParams.set('page', page);
    url.searchParams.set('category', currentCategory);
    url.searchParams.set('search', currentSearchQuery);
    url.searchParams.set('ajax', '1');
    
    favs.forEach(fav => url.searchParams.append('favs[]', fav));

    const grid = document.getElementById('catalog-grid');
    if (grid) grid.style.opacity = '0.6';

    fetch(url)
        .then(res => res.json())
        .then(data => {
            if (grid) {
                grid.style.opacity = '1';
                grid.innerHTML = data.html;
            }
            
            const paginationContainer = document.getElementById('pagination-container');
            if (paginationContainer) {
                paginationContainer.innerHTML = data.pagination;
                bindPaginationClicks();
            }

            // Dynamic global empty state toggle
            const emptyState = document.getElementById('catalog-empty-state');
            if (emptyState) {
                const officialSection = document.getElementById('official-section');
                const trendingSection = document.getElementById('trending-section');
                const officialVisible = officialSection && officialSection.style.display !== 'none';
                const trendingVisible = trendingSection && trendingSection.style.display !== 'none';
                if (!officialVisible && !trendingVisible && data.total === 0) {
                    emptyState.classList.remove('hidden');
                } else {
                    emptyState.classList.add('hidden');
                }
            }
            
            if (typeof initHearts === 'function') {
                initHearts();
            }
        })
        .catch(err => {
            console.error('Error fetching catalog data:', err);
            if (grid) grid.style.opacity = '1';
        });
}

function bindPaginationClicks() {
    const container = document.getElementById('pagination-container');
    if (!container) return;
    container.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();
            const urlObj = new URL(link.href);
            const page = urlObj.searchParams.get('page') || 1;
            fetchCatalogData(page);
            
            // Smooth scroll to catalog
            const grid = document.getElementById('catalog-grid');
            if (grid) {
                grid.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        });
    });
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
    bindPaginationClicks();
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



// Dynamic Dashboards Logic
document.addEventListener('DOMContentLoaded', () => {
    // 1. Sync student hero search with global search
    const heroSearch = document.getElementById('hero-ai-search');
    const globalSearch = document.getElementById('global-search');
    if (heroSearch) {
        heroSearch.addEventListener('input', e => {
            currentSearchQuery = e.target.value.toLowerCase().trim();
            if (globalSearch) {
                globalSearch.value = e.target.value;
                const kbdBadge = document.getElementById('search-kbd');
                if (kbdBadge) {
                    if (e.target.value) kbdBadge.classList.add('opacity-0');
                    else kbdBadge.classList.remove('opacity-0');
                }
            }
            applyFilters();
        });
        
        if (globalSearch) {
            globalSearch.addEventListener('input', e => {
                heroSearch.value = e.target.value;
            });
        }

        // 2. Animated typing placeholder for student hero search
        const placeholders = [
            "Search AI tools...",
            "Find tools for your project...",
            "Discover creative AI apps...",
            "Search classroom assistance...",
            "Ask our AI EdTech companion..."
        ];
        let index = 0;
        let charIndex = 0;
        let currentText = '';
        let isDeleting = false;
        
        function typePlaceholder() {
            if (!document.getElementById('hero-ai-search')) return;
            const fullText = placeholders[index];
            if (isDeleting) {
                currentText = fullText.substring(0, charIndex - 1);
                charIndex--;
            } else {
                currentText = fullText.substring(0, charIndex + 1);
                charIndex++;
            }
            
            heroSearch.placeholder = currentText;
            
            let typeSpeed = 80;
            if (isDeleting) typeSpeed /= 2;
            
            if (!isDeleting && currentText === fullText) {
                typeSpeed = 1500; // Pause at end of word
                isDeleting = true;
            } else if (isDeleting && currentText === '') {
                isDeleting = false;
                index = (index + 1) % placeholders.length;
                typeSpeed = 300; // Pause before typing next word
            }
            
            setTimeout(typePlaceholder, typeSpeed);
        }
        
        typePlaceholder();
    }

    // 3. Counter Animation for Teacher dashboard
    function animateCounter(id, start, end, duration) {
        const obj = document.getElementById(id);
        if (!obj) return;
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            obj.innerHTML = Math.floor(progress * (end - start) + start);
            if (progress < 1) {
                window.requestAnimationFrame(step);
            } else {
                obj.innerHTML = end;
            }
        };
        window.requestAnimationFrame(step);
    }

    // Initialize counts if element is present
    @auth
        @if(Auth::user()->isTeacher())
            setTimeout(() => {
                animateCounter('count-plans', 0, {{ $lessonPlanCount }}, 1200);
                animateCounter('count-badges', 0, {{ $badgeCount }}, 1200);
                animateCounter('count-favorites', 0, {{ $favoriteToolsCount }}, 1200);
            }, 300);
        @endif
    @endauth
});

// AI Prompt copying utility
function copyPrompt(btn, text) {
    navigator.clipboard.writeText(text).then(() => {
        const originalHTML = btn.innerHTML;
        btn.innerHTML = `<svg class="w-4 h-4 text-green-500 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Copied!`;
        btn.classList.add('bg-green-50', 'text-green-600', 'border-green-200');
        setTimeout(() => {
            btn.innerHTML = originalHTML;
            btn.classList.remove('bg-green-50', 'text-green-600', 'border-green-200');
        }, 2000);
    }).catch(err => {
        console.error('Failed to copy text: ', err);
    });
}
</script>

@endsection
