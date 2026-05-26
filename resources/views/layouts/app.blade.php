<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Curated AI Tools - ANS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Montserrat:wght@500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* ─── Splash Screen Premium Fusion ─── */
        #splash {
            position: fixed; inset: 0; z-index: 9999;
            background: linear-gradient(135deg, #007934 0%, #005624 50%, #003314 100%);
            display: flex; align-items: center; justify-content: center;
            flex-direction: column; gap: 0;
            transition: opacity 0.7s cubic-bezier(0.4, 0, 0.2, 1), visibility 0.7s ease;
        }
        #splash.hidden { opacity: 0; visibility: hidden; pointer-events: none; }
        
        .splash-logo-ans {
            position: absolute;
            left: 50%; top: 50%;
            margin-left: -60px; margin-top: -60px;
            width: 120px; height: 120px; border-radius: 50%;
            overflow: hidden; border: 4px solid rgba(255,255,255,0.4);
            box-shadow: 0 0 50px rgba(0,0,0,0.4);
            transform: translate(-120px, 150px); opacity: 0;
            animation: splashAnsMove 2.2s cubic-bezier(0.25, 1, 0.5, 1) 0.2s forwards;
            z-index: 10;
        }
        .splash-logo-ans img { width: 100%; height: 100%; object-fit: cover; }
        
        .splash-logo-gemini {
            position: absolute;
            left: 50%; top: 50%;
            margin-left: -40px; margin-top: -40px;
            width: 80px; height: 80px; border-radius: 50%;
            background: white; display: flex; align-items: center; justify-content: center;
            box-shadow: 0 0 30px rgba(0,0,0,0.3);
            transform: translate(120px, 180px); opacity: 0;
            animation: splashGeminiMove 2.2s cubic-bezier(0.25, 1, 0.5, 1) 0.2s forwards;
            z-index: 20;
        }
        
        .splash-shockwave {
            position: absolute;
            left: 50%; top: 50%;
            margin-left: -70px; margin-top: -70px;
            width: 140px; height: 140px;
            border-radius: 50%;
            border: 4px solid #FF8300;
            opacity: 0;
            transform: scale(0.5);
            animation: shockwavePulse 2.2s ease-out 0.2s forwards;
            pointer-events: none;
            z-index: 5;
        }
        
        .splash-text {
            opacity: 0;
            animation: splashFadeText 0.6s ease 1.7s forwards;
            text-align: center; color: white; margin-top: 180px;
        }
        .splash-bar {
            width: 180px; height: 3px;
            background: rgba(255,255,255,0.15);
            border-radius: 99px; margin-top: 24px; overflow: hidden;
            opacity: 0; animation: splashFadeText 0.4s ease 1.8s forwards;
        }
        .splash-bar-fill {
            height: 100%; width: 0%; background: #FF8300;
            border-radius: 99px;
            animation: splashProgress 1.4s ease 1.9s forwards;
        }
        
        @keyframes splashAnsMove {
            0% { transform: translate(-150px, 100px) scale(0.8); opacity: 0; }
            45% { transform: translate(-45px, 0px) scale(1); opacity: 1; }
            70% { transform: translate(0px, 0px) scale(1); opacity: 1; border-color: rgba(255,255,255,0.4); }
            80% { transform: translate(0px, 0px) scale(1.1); border-color: #FF8300; box-shadow: 0 0 80px rgba(255, 131, 0, 0.8), 0 0 120px rgba(0, 121, 52, 0.6); }
            100% { transform: translate(0px, 0px) scale(1); opacity: 1; border-color: rgba(255,255,255,0.7); box-shadow: 0 0 40px rgba(0,0,0,0.3); }
        }
        
        @keyframes splashGeminiMove {
            0% { transform: translate(150px, 130px) scale(0.8); opacity: 0; }
            45% { transform: translate(45px, 0px) scale(1); opacity: 1; }
            70% { transform: translate(0px, 0px) scale(1); opacity: 1; }
            80% { transform: translate(0px, 0px) scale(0.85); box-shadow: 0 0 50px rgba(255,255,255,0.9); }
            100% { transform: translate(0px, 0px) scale(0.75); opacity: 0.95; box-shadow: 0 0 25px rgba(0,0,0,0.2); }
        }
        
        @keyframes shockwavePulse {
            0%, 65% { opacity: 0; transform: scale(0.5); }
            72% { opacity: 1; border-color: #FF8300; box-shadow: 0 0 50px #FF8300; }
            85% { opacity: 0.6; transform: scale(1.4); border-color: #007934; }
            100% { opacity: 0; transform: scale(1.8); }
        }
        
        @keyframes splashFadeText {
            to { opacity: 1; }
        }
        @keyframes splashProgress {
            to { width: 100%; }
        }

        /* ─── Sidebar Toggle ─── */
        #sidebar {
            transition: width 0.3s cubic-bezier(0.4,0,0.2,1),
                        transform 0.3s cubic-bezier(0.4,0,0.2,1);
        }
        #sidebar.collapsed { width: 0 !important; overflow: hidden; }
        #sidebar-inner { transition: opacity 0.2s ease; }
        #sidebar.collapsed #sidebar-inner { opacity: 0; pointer-events: none; }
        #toggle-sidebar {
            transition: transform 0.3s ease;
        }
        body.sidebar-collapsed #toggle-sidebar svg {
            transform: rotate(180deg);
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 font-sans antialiased min-h-screen flex overflow-hidden">

<!-- ════════════════════════════════════════════════════════════ -->
<!-- SPLASH SCREEN                                               -->
<!-- ════════════════════════════════════════════════════════════ -->
<div id="splash">
    <!-- Logos container for absolute alignment of fusion -->
    <div class="relative w-64 h-48 flex items-center justify-center">
        <div class="splash-logo-ans">
            <img src="{{ asset('logo.png') }}" alt="ANS Logo">
        </div>
        <div class="splash-logo-gemini">
            <!-- Gemini Logo SVG -->
            <svg width="44" height="44" viewBox="0 0 192 192" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <linearGradient id="g1" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" style="stop-color:#4285F4"/>
                        <stop offset="100%" style="stop-color:#1967D2"/>
                    </linearGradient>
                </defs>
                <path d="M96 20 C96 20 60 96 20 96 C60 96 96 172 96 172 C96 172 132 96 172 96 C132 96 96 20 96 20Z" fill="url(#g1)"/>
            </svg>
        </div>
        <div class="splash-shockwave"></div>
    </div>
    <div class="splash-text">
        <p class="font-heading font-extrabold text-2xl tracking-tight">AINS AI Portal</p>
        <p style="color: rgba(255,255,255,0.6); font-size: 13px; margin-top: 4px;">American Nicaraguan School</p>
    </div>
    <div class="splash-bar">
        <div class="splash-bar-fill"></div>
    </div>
</div>

<!-- ════════════════════════════════════════════════════════════ -->
<!-- SIDEBAR                                                     -->
<!-- ════════════════════════════════════════════════════════════ -->
<aside id="sidebar" class="w-72 bg-gradient-to-b from-ans-dark-green to-ans-seal-green text-white flex-shrink-0 h-screen hidden md:flex flex-col shadow-xl z-20 relative">
    <div id="sidebar-inner" class="flex flex-col h-full">
        <!-- Logo Area -->
        <div class="p-8 pb-4">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-12 h-12 bg-white rounded-xl overflow-hidden shadow-md border-2 border-white/20 flex-shrink-0">
                    <img src="{{ asset('logo.png') }}" alt="ANS Logo" class="w-full h-full object-cover scale-110">
                </div>
                <div>
                    <h1 class="text-2xl font-heading font-extrabold tracking-tight text-white leading-none">AINS</h1>
                    <span class="text-[10px] font-bold tracking-wider text-ans-light-green uppercase">AI Portal</span>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 mt-6 overflow-y-auto space-y-6">
            <!-- Portal Hub -->
            <div class="space-y-1">
                <p class="px-4 text-[10px] font-bold text-ans-light-green uppercase tracking-wider mb-2">Portal Hub</p>
                <a href="/" class="flex items-center gap-3 px-4 py-2.5 rounded-xl {{ request()->is('/') ? 'bg-white/10 text-white font-medium border-l-4 border-ans-orange shadow-inner' : 'hover:bg-white/5 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-ans-light-green' }} transition-all">
                    <svg class="w-5 h-5 opacity-70 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span class="whitespace-nowrap">Directory Hub</span>
                </a>
                @auth
                @if(Auth::user()->isTeacher() || Auth::user()->isAdmin())
                <a href="{{ route('lesson-plans.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('lesson-plans.*') ? 'bg-white/10 text-white font-medium border-l-4 border-ans-orange shadow-inner' : 'hover:bg-white/5 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-ans-light-green' }} transition-all">
                    <svg class="w-5 h-5 opacity-70 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span class="whitespace-nowrap">AI Lesson Planner</span>
                </a>
                <a href="{{ route('ai-detection.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('ai-detection.*') ? 'bg-white/10 text-white font-medium border-l-4 border-ans-orange shadow-inner' : 'hover:bg-white/5 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-ans-light-green' }} transition-all">
                    <svg class="w-5 h-5 opacity-70 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <span class="whitespace-nowrap">AI Detection Tools</span>
                </a>
                @endif
                @endauth
            </div>

            <!-- School & AI Policy -->
            <div class="space-y-1">
                <p class="px-4 text-[10px] font-bold text-ans-light-green uppercase tracking-wider mb-2">School & AI Policy</p>
                <a href="{{ route('policy') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('policy') ? 'bg-white/10 text-white font-medium border-l-4 border-ans-orange shadow-inner' : 'hover:bg-white/5 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-ans-light-green' }} transition-all">
                    <svg class="w-5 h-5 opacity-70 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    <span class="whitespace-nowrap">AI School Policy</span>
                </a>
                <a href="{{ route('task-force') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('task-force') ? 'bg-white/10 text-white font-medium border-l-4 border-ans-orange shadow-inner' : 'hover:bg-white/5 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-ans-light-green' }} transition-all">
                    <svg class="w-5 h-5 opacity-70 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <span class="whitespace-nowrap">AI Task Force</span>
                </a>
            </div>

            <!-- Knowledge & Help -->
            <div class="space-y-1">
                <p class="px-4 text-[10px] font-bold text-ans-light-green uppercase tracking-wider mb-2">Knowledge & Help</p>
                <a href="{{ route('tips') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('tips') ? 'bg-white/10 text-white font-medium border-l-4 border-ans-orange shadow-inner' : 'hover:bg-white/5 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-ans-light-green' }} transition-all">
                    <svg class="w-5 h-5 opacity-70 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                    <span class="whitespace-nowrap">EdTech Prompt Tips</span>
                </a>
                @auth
                @if(Auth::user()->isTeacher() || Auth::user()->isAdmin())
                <a href="{{ route('badges.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('badges.*') ? 'bg-white/10 text-white font-medium border-l-4 border-ans-orange shadow-inner' : 'hover:bg-white/5 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-ans-light-green' }} transition-all">
                    <svg class="w-5 h-5 opacity-70 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                    <span class="whitespace-nowrap">EdTech Badges</span>
                </a>
                @endif
                @endauth
                @auth
                <a href="{{ route('requests.create') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('requests.create') ? 'bg-white/10 text-white font-medium border-l-4 border-ans-orange shadow-inner' : 'hover:bg-white/5 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-ans-light-green' }} transition-all">
                    <svg class="w-5 h-5 opacity-70 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="whitespace-nowrap">Suggest Tool</span>
                </a>
                @endauth
            </div>

            <!-- Authenticated User Links -->
            @auth
            <div class="space-y-1">
                <p class="px-4 text-[10px] font-bold text-ans-light-green uppercase tracking-wider mb-2">My Account</p>
                <a href="{{ route('favorites.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl {{ request()->is('favorites') ? 'bg-white/10 text-white font-medium border-l-4 border-ans-orange shadow-inner' : 'hover:bg-white/5 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-ans-light-green' }} transition-all">
                    <svg class="w-5 h-5 opacity-70 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    <span class="whitespace-nowrap">My Favorites</span>
                </a>
                <a href="{{ route('profile.show') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl {{ request()->is('profile') ? 'bg-white/10 text-white font-medium border-l-4 border-ans-orange shadow-inner' : 'hover:bg-white/5 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-ans-light-green' }} transition-all">
                    <svg class="w-5 h-5 opacity-70 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <span class="whitespace-nowrap">My Profile</span>
                </a>
            </div>
            @endauth

            <!-- Admin Controls -->
            @auth
            @if(Auth::user()->role === 'admin')
            <div class="pt-4 border-t border-white/10 space-y-1">
                <p class="px-4 text-[10px] font-bold text-ans-orange uppercase tracking-wider mb-2">Administration</p>
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl {{ request()->is('admin/dashboard') ? 'bg-white/10 text-white font-medium border-l-4 border-ans-orange shadow-inner' : 'hover:bg-white/5 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-ans-orange' }} transition-all">
                    <svg class="w-5 h-5 opacity-70 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    <span class="whitespace-nowrap">Dashboard</span>
                </a>
                <a href="{{ route('admin.requests.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl {{ request()->is('admin/requests*') ? 'bg-white/10 text-white font-medium border-l-4 border-ans-orange shadow-inner' : 'hover:bg-white/5 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-ans-orange' }} transition-all">
                    <svg class="w-5 h-5 opacity-70 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    <span class="whitespace-nowrap">Requests</span>
                </a>
                <a href="{{ route('admin.tools.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl {{ request()->is('admin/tools*') ? 'bg-white/10 text-white font-medium border-l-4 border-ans-orange shadow-inner' : 'hover:bg-white/5 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-ans-orange' }} transition-all">
                    <svg class="w-5 h-5 opacity-70 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                    <span class="whitespace-nowrap">Tools Catalog</span>
                </a>
                <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl {{ request()->is('admin/categories*') ? 'bg-white/10 text-white font-medium border-l-4 border-ans-orange shadow-inner' : 'hover:bg-white/5 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-ans-orange' }} transition-all">
                    <svg class="w-5 h-5 opacity-70 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                    <span class="whitespace-nowrap">Categories</span>
                </a>
                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl {{ request()->is('admin/users*') ? 'bg-white/10 text-white font-medium border-l-4 border-ans-orange shadow-inner' : 'hover:bg-white/5 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-ans-orange' }} transition-all">
                    <svg class="w-5 h-5 opacity-70 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <span class="whitespace-nowrap">Users</span>
                </a>
                <a href="{{ route('admin.task-force.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl {{ request()->is('admin/task-force*') ? 'bg-white/10 text-white font-medium border-l-4 border-ans-orange shadow-inner' : 'hover:bg-white/5 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-ans-orange' }} transition-all">
                    <svg class="w-5 h-5 opacity-70 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <span class="whitespace-nowrap">Task Force</span>
                </a>
                <a href="{{ route('admin.prompt-tips.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl {{ request()->is('admin/prompt-tips*') ? 'bg-white/10 text-white font-medium border-l-4 border-ans-orange shadow-inner' : 'hover:bg-white/5 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-ans-orange' }} transition-all">
                    <svg class="w-5 h-5 opacity-70 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                    <span class="whitespace-nowrap">Prompt Tips</span>
                </a>
                <a href="{{ route('admin.badges.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl {{ request()->is('admin/badges*') ? 'bg-white/10 text-white font-medium border-l-4 border-ans-orange shadow-inner' : 'hover:bg-white/5 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-ans-orange' }} transition-all">
                    <svg class="w-5 h-5 opacity-70 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                    <span class="whitespace-nowrap">Badges</span>
                </a>
                <a href="{{ route('admin.badge-evidence.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl {{ request()->is('admin/badge-evidence*') ? 'bg-white/10 text-white font-medium border-l-4 border-ans-orange shadow-inner' : 'hover:bg-white/5 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-ans-orange' }} transition-all">
                    <svg class="w-5 h-5 opacity-70 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    <span class="whitespace-nowrap">Badge Evidence</span>
                </a>
                <a href="{{ route('admin.chatbot-settings') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl {{ request()->routeIs('admin.chatbot-settings') ? 'bg-white/10 text-white font-medium border-l-4 border-ans-orange shadow-inner' : 'hover:bg-white/5 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-ans-orange' }} transition-all">
                    <svg class="w-5 h-5 opacity-70 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span class="whitespace-nowrap">Chatbot Settings</span>
                </a>
            </div>
            @endif
            @endauth
        </nav>

        <!-- Footer -->
        <div class="p-4 mt-auto">
            <div class="bg-black/20 rounded-xl p-4 text-center border border-white/5">
                <p class="text-[10px] text-white/50 leading-tight">ANS Educational AI Platform</p>
                <p class="text-[9px] text-white/30 mt-1">© {{ date('Y') }} All rights reserved.</p>
            </div>
        </div>
    </div>
</aside>

<!-- ════════════════════════════════════════════════════════════ -->
<!-- MOBILE DRAWER                                               -->
<!-- ════════════════════════════════════════════════════════════ -->
<div id="mobile-drawer" class="fixed inset-0 z-50 hidden" onclick="if(event.target===this) toggleMobileDrawer()">
    <!-- Glass Backdrop -->
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity duration-300 opacity-0" id="mobile-drawer-backdrop"></div>
    
    <!-- Panel -->
    <div class="absolute left-0 top-0 bottom-0 w-72 bg-gradient-to-b from-ans-dark-green to-ans-seal-green text-white flex flex-col shadow-2xl transform -translate-x-full transition-transform duration-300" id="mobile-drawer-panel">
        <!-- Close Button -->
        <button onclick="toggleMobileDrawer()" class="absolute top-4 right-4 w-9 h-9 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center text-white/80 hover:text-white transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <!-- Logo Area -->
        <div class="p-8 pb-4">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 bg-white rounded-xl overflow-hidden shadow-md border-2 border-white/20 flex-shrink-0">
                    <img src="{{ asset('logo.png') }}" alt="ANS Logo" class="w-full h-full object-cover scale-110">
                </div>
                <div>
                    <h1 class="text-xl font-heading font-extrabold tracking-tight text-white leading-none">AINS</h1>
                    <span class="text-[9px] font-bold tracking-wider text-ans-light-green uppercase">AI Portal</span>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 mt-4 overflow-y-auto space-y-5">
            <!-- Portal Hub -->
            <div class="space-y-1">
                <p class="px-4 text-[9px] font-bold text-ans-light-green uppercase tracking-wider mb-1.5">Portal Hub</p>
                <a href="/" class="flex items-center gap-3 px-4 py-2 rounded-xl {{ request()->is('/') ? 'bg-white/10 text-white font-medium border-l-4 border-ans-orange shadow-inner' : 'hover:bg-white/5 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-ans-light-green' }} transition-all">
                    <svg class="w-5 h-5 opacity-70 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span class="text-sm">Directory Hub</span>
                </a>
                @auth
                @if(Auth::user()->isTeacher() || Auth::user()->isAdmin())
                <a href="{{ route('lesson-plans.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-xl {{ request()->routeIs('lesson-plans.*') ? 'bg-white/10 text-white font-medium border-l-4 border-ans-orange shadow-inner' : 'hover:bg-white/5 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-ans-light-green' }} transition-all">
                    <svg class="w-5 h-5 opacity-70 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span class="text-sm">AI Lesson Planner</span>
                </a>
                <a href="{{ route('ai-detection.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-xl {{ request()->routeIs('ai-detection.*') ? 'bg-white/10 text-white font-medium border-l-4 border-ans-orange shadow-inner' : 'hover:bg-white/5 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-ans-light-green' }} transition-all">
                    <svg class="w-5 h-5 opacity-70 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <span class="text-sm">AI Detection Tools</span>
                </a>
                @endif
                @endauth
            </div>

            <!-- School & AI Policy -->
            <div class="space-y-1">
                <p class="px-4 text-[9px] font-bold text-ans-light-green uppercase tracking-wider mb-1.5">School & AI Policy</p>
                <a href="{{ route('policy') }}" class="flex items-center gap-3 px-4 py-2 rounded-xl {{ request()->routeIs('policy') ? 'bg-white/10 text-white font-medium border-l-4 border-ans-orange shadow-inner' : 'hover:bg-white/5 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-ans-light-green' }} transition-all">
                    <svg class="w-5 h-5 opacity-70 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    <span class="text-sm">AI School Policy</span>
                </a>
                <a href="{{ route('task-force') }}" class="flex items-center gap-3 px-4 py-2 rounded-xl {{ request()->routeIs('task-force') ? 'bg-white/10 text-white font-medium border-l-4 border-ans-orange shadow-inner' : 'hover:bg-white/5 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-ans-light-green' }} transition-all">
                    <svg class="w-5 h-5 opacity-70 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <span class="text-sm">AI Task Force</span>
                </a>
            </div>

            <!-- Knowledge & Help -->
            <div class="space-y-1">
                <p class="px-4 text-[9px] font-bold text-ans-light-green uppercase tracking-wider mb-1.5">Knowledge & Help</p>
                <a href="{{ route('tips') }}" class="flex items-center gap-3 px-4 py-2 rounded-xl {{ request()->routeIs('tips') ? 'bg-white/10 text-white font-medium border-l-4 border-ans-orange shadow-inner' : 'hover:bg-white/5 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-ans-light-green' }} transition-all">
                    <svg class="w-5 h-5 opacity-70 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                    <span class="text-sm">EdTech Prompt Tips</span>
                </a>
                @auth
                @if(Auth::user()->isTeacher() || Auth::user()->isAdmin())
                <a href="{{ route('badges.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-xl {{ request()->routeIs('badges.*') ? 'bg-white/10 text-white font-medium border-l-4 border-ans-orange shadow-inner' : 'hover:bg-white/5 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-ans-light-green' }} transition-all">
                    <svg class="w-5 h-5 opacity-70 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                    <span class="text-sm">EdTech Badges</span>
                </a>
                @endif
                @endauth
                @auth
                <a href="{{ route('requests.create') }}" class="flex items-center gap-3 px-4 py-2 rounded-xl {{ request()->routeIs('requests.create') ? 'bg-white/10 text-white font-medium border-l-4 border-ans-orange shadow-inner' : 'hover:bg-white/5 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-ans-light-green' }} transition-all">
                    <svg class="w-5 h-5 opacity-70 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="text-sm">Suggest Tool</span>
                </a>
                @endauth
            </div>

            <!-- Authenticated User Links -->
            @auth
            <div class="space-y-1">
                <p class="px-4 text-[9px] font-bold text-ans-light-green uppercase tracking-wider mb-1.5">My Account</p>
                <a href="{{ route('favorites.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-xl {{ request()->is('favorites') ? 'bg-white/10 text-white font-medium border-l-4 border-ans-orange shadow-inner' : 'hover:bg-white/5 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-ans-light-green' }} transition-all">
                    <svg class="w-5 h-5 opacity-70 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    <span class="text-sm">My Favorites</span>
                </a>
                <a href="{{ route('profile.show') }}" class="flex items-center gap-3 px-4 py-2 rounded-xl {{ request()->is('profile') ? 'bg-white/10 text-white font-medium border-l-4 border-ans-orange shadow-inner' : 'hover:bg-white/5 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-ans-light-green' }} transition-all">
                    <svg class="w-5 h-5 opacity-70 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <span class="text-sm">My Profile</span>
                </a>
            </div>
            @endauth

            <!-- Admin Controls -->
            @auth
            @if(Auth::user()->role === 'admin')
            <div class="pt-3 border-t border-white/10 space-y-1">
                <p class="px-4 text-[9px] font-bold text-ans-orange uppercase tracking-wider mb-1.5">Administration</p>
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2 rounded-xl {{ request()->is('admin/dashboard') ? 'bg-white/10 text-white font-medium border-l-4 border-ans-orange shadow-inner' : 'hover:bg-white/5 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-ans-orange' }} transition-all">
                    <svg class="w-5 h-5 opacity-70 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    <span class="text-sm">Dashboard</span>
                </a>
                <a href="{{ route('admin.requests.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-xl {{ request()->is('admin/requests*') ? 'bg-white/10 text-white font-medium border-l-4 border-ans-orange shadow-inner' : 'hover:bg-white/5 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-ans-orange' }} transition-all">
                    <svg class="w-5 h-5 opacity-70 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    <span class="text-sm">Requests</span>
                </a>
                <a href="{{ route('admin.tools.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-xl {{ request()->is('admin/tools*') ? 'bg-white/10 text-white font-medium border-l-4 border-ans-orange shadow-inner' : 'hover:bg-white/5 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-ans-orange' }} transition-all">
                    <svg class="w-5 h-5 opacity-70 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                    <span class="text-sm">Tools Catalog</span>
                </a>
                <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-xl {{ request()->is('admin/categories*') ? 'bg-white/10 text-white font-medium border-l-4 border-ans-orange shadow-inner' : 'hover:bg-white/5 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-ans-orange' }} transition-all">
                    <svg class="w-5 h-5 opacity-70 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                    <span class="text-sm">Categories</span>
                </a>
                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-xl {{ request()->is('admin/users*') ? 'bg-white/10 text-white font-medium border-l-4 border-ans-orange shadow-inner' : 'hover:bg-white/5 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-ans-orange' }} transition-all">
                    <svg class="w-5 h-5 opacity-70 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <span class="text-sm">Users</span>
                </a>
                <a href="{{ route('admin.task-force.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-xl {{ request()->is('admin/task-force*') ? 'bg-white/10 text-white font-medium border-l-4 border-ans-orange shadow-inner' : 'hover:bg-white/5 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-ans-orange' }} transition-all">
                    <svg class="w-5 h-5 opacity-70 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <span class="text-sm">Task Force</span>
                </a>
                <a href="{{ route('admin.prompt-tips.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-xl {{ request()->is('admin/prompt-tips*') ? 'bg-white/10 text-white font-medium border-l-4 border-ans-orange shadow-inner' : 'hover:bg-white/5 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-ans-orange' }} transition-all">
                    <svg class="w-5 h-5 opacity-70 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                    <span class="text-sm">Prompt Tips</span>
                </a>
                <a href="{{ route('admin.badges.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-xl {{ request()->is('admin/badges*') ? 'bg-white/10 text-white font-medium border-l-4 border-ans-orange shadow-inner' : 'hover:bg-white/5 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-ans-orange' }} transition-all">
                    <svg class="w-5 h-5 opacity-70 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                    <span class="text-sm">Badges</span>
                </a>
                <a href="{{ route('admin.badge-evidence.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-xl {{ request()->is('admin/badge-evidence*') ? 'bg-white/10 text-white font-medium border-l-4 border-ans-orange shadow-inner' : 'hover:bg-white/5 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-ans-orange' }} transition-all">
                    <svg class="w-5 h-5 opacity-70 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    <span class="text-sm">Badge Evidence</span>
                </a>
                <a href="{{ route('admin.chatbot-settings') }}" class="flex items-center gap-3 px-4 py-2 rounded-xl {{ request()->routeIs('admin.chatbot-settings') ? 'bg-white/10 text-white font-medium border-l-4 border-ans-orange shadow-inner' : 'hover:bg-white/5 text-gray-300 hover:text-white border-l-4 border-transparent hover:border-ans-orange' }} transition-all">
                    <svg class="w-5 h-5 opacity-70 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span class="text-sm">Chatbot Settings</span>
                </a>
            </div>
            @endif
            @endauth
        </nav>

        <div class="p-4 mt-auto">
            <div class="bg-black/20 rounded-xl p-4 text-center border border-white/5">
                <p class="text-[10px] text-white/50 leading-tight">ANS Educational AI Platform</p>
                <p class="text-[9px] text-white/30 mt-1">© {{ date('Y') }} All rights reserved.</p>
            </div>
        </div>
    </div>
</div>

<!-- ════════════════════════════════════════════════════════════ -->
<!-- MAIN WORKSPACE                                              -->
<!-- ════════════════════════════════════════════════════════════ -->
<div class="flex-1 flex flex-col h-screen overflow-hidden bg-gray-50/50 relative">

    <!-- Decorative Background -->
    <div class="absolute top-0 right-0 w-96 h-96 bg-ans-light-green/10 rounded-full blur-3xl -z-10 translate-x-1/3 -translate-y-1/3"></div>
    <div class="absolute bottom-0 left-0 w-96 h-96 bg-ans-orange/5 rounded-full blur-3xl -z-10 -translate-x-1/3 translate-y-1/3"></div>

    <!-- Top Header (Glassmorphism) -->
    <header class="glass sticky top-0 z-30 px-6 py-4 flex justify-between items-center border-b border-gray-200/50">

        <!-- Mobile Burger Button -->
        <button id="toggle-mobile-menu" onclick="toggleMobileDrawer()" class="md:hidden flex w-9 h-9 items-center justify-center rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-gray-500 hover:text-ans-dark-green transition-all shadow-sm flex-shrink-0 mr-4" title="Open Menu">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>

        <!-- Mobile Search Button -->
        <button id="open-mobile-search" onclick="toggleMobileSearch(true)" class="lg:hidden flex w-9 h-9 items-center justify-center rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-gray-500 hover:text-ans-dark-green transition-all shadow-sm flex-shrink-0 mr-4" title="Search">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </button>

        <!-- Sidebar Toggle Button (Desktop Only) -->
        <button id="toggle-sidebar" onclick="toggleSidebar()" class="hidden md:flex w-9 h-9 items-center justify-center rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-gray-500 hover:text-ans-dark-green transition-all shadow-sm flex-shrink-0 mr-4" title="Toggle Sidebar">
            <svg class="w-5 h-5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
            </svg>
        </button>

        <!-- Left: Dynamic Page Header -->
        <div class="flex-1 min-w-0">
            <h2 class="text-xl font-heading font-bold text-gray-800 tracking-tight animate-fade-in-up" style="animation-duration: 0.4s;">
                @hasSection('header-title')
                    @yield('header-title')
                @else
                    @auth
                        Welcome, <span class="text-ans-dark-green">{{ explode(' ', Auth::user()->name)[0] }}</span> 👋
                    @else
                        Discover AI Tools
                    @endauth
                @endif
            </h2>
            <p class="text-sm text-gray-500 animate-fade-in-up" style="animation-duration: 0.6s;">
                @hasSection('header-subtitle')
                    @yield('header-subtitle')
                @else
                    @auth
                        Find the best AI tools for your classes today.
                    @else
                        Sign in to access premium features.
                    @endauth
                @endif
            </p>
        </div>

        <!-- Center: Search -->
        <div class="flex-1 max-w-md hidden lg:block mx-8">
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400 group-focus-within:text-ans-dark-green transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="search" id="global-search" placeholder="Search tools..." class="block w-full pl-10 pr-16 py-2.5 bg-white border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green transition-all shadow-sm">
                <div id="search-kbd" class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none transition-opacity duration-200">
                    <kbd class="inline-flex items-center border border-gray-200 px-1.5 py-0.5 rounded text-[10px] font-sans font-medium text-gray-400 bg-gray-50/80 shadow-sm">Ctrl K</kbd>
                </div>
            </div>
        </div>

        <!-- Right: User Actions -->
        <div class="flex-1 flex justify-end items-center space-x-4">
            <!-- Language Switcher (Disabled for English-only baseline) -->
            <!--
            <div class="relative group hidden sm:block">
                <button class="flex items-center gap-1 text-sm font-medium text-gray-500 hover:text-ans-dark-green transition-colors py-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path></svg>
                    {{ strtoupper(app()->getLocale()) }}
                </button>
                <div class="absolute right-0 top-full w-32 bg-white rounded-xl shadow-lg border border-gray-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50">
                    <div class="py-1">
                        <a href="{{ route('lang.switch', 'en') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-ans-dark-green {{ app()->getLocale() === 'en' ? 'font-bold text-ans-dark-green' : '' }}">English</a>
                        <a href="{{ route('lang.switch', 'es') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-ans-dark-green {{ app()->getLocale() === 'es' ? 'font-bold text-ans-dark-green' : '' }}">Español</a>
                    </div>
                </div>
            </div>
            -->

            @auth
                @if(!request()->is('requests/*') && !request()->is('admin/*'))
                <button onclick="window.location='{{ route('requests.create') }}'" class="hidden md:flex items-center gap-2 bg-ans-orange hover:bg-[#e67600] text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-md shadow-ans-orange/20 hover:shadow-lg transition-all hover:-translate-y-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Suggest App
                </button>
                @endif

                <div class="h-8 w-px bg-gray-200 mx-2"></div>

                <div class="flex items-center gap-4">
                    <div class="text-right hidden sm:block pr-1">
                        <p class="text-sm font-bold text-gray-800 leading-none">{{ Auth::user()->name }}</p>
                        <p class="text-[11px] text-gray-500 font-medium mt-1 uppercase tracking-wide">{{ Auth::user()->role ?? 'User' }}</p>
                    </div>
                    @if(Auth::user()->avatar)
                        <img src="{{ Auth::user()->avatar }}" alt="Avatar" class="w-10 h-10 rounded-full border-2 border-white shadow-sm ring-2 ring-transparent hover:ring-ans-dark-green transition-all cursor-pointer">
                    @else
                        <div class="w-10 h-10 rounded-full bg-ans-dark-green text-white flex items-center justify-center font-bold text-lg border-2 border-white shadow-sm cursor-pointer">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('logout') }}" class="ml-2">
                        @csrf
                        <button type="submit" class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all" title="Sign out">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        </button>
                    </form>
                </div>
            @else
                <a href="/auth/google" class="flex items-center gap-3 bg-white border border-gray-200 hover:border-gray-300 text-gray-700 px-5 py-2.5 rounded-xl text-sm font-semibold shadow-sm hover:shadow-md transition-all">
                    <svg class="w-4 h-4" viewBox="0 0 24 24">
                        <path fill="currentColor" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                        <path fill="#34A853" d="M12 24c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 21.53 7.7 24 12 24z" />
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                        <path fill="#EA4335" d="M12 4.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 1.18 14.97 0 12 0 7.7 0 3.99 2.47 2.18 6.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                    </svg>
                    <span>Sign in with Google</span>
                </a>
            @endauth
        </div>
    </header>

    <!-- Mobile Search Overlay (Slide-down) -->
    <div id="mobile-search-overlay" class="fixed top-0 left-0 right-0 bg-white shadow-xl border-b border-gray-200 z-50 transform -translate-y-full transition-transform duration-300 ease-in-out p-4 flex items-center gap-3 lg:hidden">
        <div class="relative flex-1">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input type="search" id="mobile-search-input" placeholder="Search tools..." class="block w-full pl-10 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green transition-all">
        </div>
        <button onclick="toggleMobileSearch(false)" class="w-10 h-10 rounded-xl bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-500 hover:text-gray-700 transition-all flex-shrink-0" title="Close Search">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <!-- Content -->
    <main class="flex-1 overflow-y-auto p-8 z-10 scroll-smooth">
        <div class="max-w-7xl mx-auto">
            @yield('content')
        </div>
    </main>
</div>

<script>
// ─── Mobile Search Overlay Toggle ────────────────────────────
function toggleMobileSearch(show) {
    const overlay = document.getElementById('mobile-search-overlay');
    const input = document.getElementById('mobile-search-input');
    if (overlay) {
        if (show) {
            overlay.classList.remove('-translate-y-full');
            if (input) {
                setTimeout(() => input.focus(), 100);
            }
        } else {
            overlay.classList.add('-translate-y-full');
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const mobileSearchInput = document.getElementById('mobile-search-input');
    const desktopSearchInput = document.getElementById('global-search');
    
    if (mobileSearchInput && desktopSearchInput) {
        mobileSearchInput.addEventListener('input', e => {
            const val = e.target.value;
            desktopSearchInput.value = val;
            desktopSearchInput.dispatchEvent(new Event('input', { bubbles: true }));
        });
        
        desktopSearchInput.addEventListener('input', e => {
            mobileSearchInput.value = e.target.value;
        });
    }

    if (mobileSearchInput) {
        mobileSearchInput.addEventListener('keydown', e => {
            if (e.key === 'Enter') {
                const query = mobileSearchInput.value.trim();
                if (window.location.pathname !== '/') {
                    window.location.href = '/?search=' + encodeURIComponent(query);
                } else {
                    toggleMobileSearch(false);
                }
            }
        });
    }
});

// ─── Splash Screen ───────────────────────────────────────────
window.addEventListener('DOMContentLoaded', () => {
    // Only show on first visit per session
    if (!sessionStorage.getItem('splashShown')) {
        sessionStorage.setItem('splashShown', '1');
        setTimeout(() => {
            document.getElementById('splash').classList.add('hidden');
        }, 3400); // 3.4 seconds gives plenty of time for the fusion animation
    } else {
        // Already seen — hide immediately
        const s = document.getElementById('splash');
        if (s) {
            s.style.transition = 'none';
            s.classList.add('hidden');
        }
    }
});

// ─── Sidebar Toggle ──────────────────────────────────────────
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const body = document.body;
    const isCollapsed = sidebar.classList.toggle('collapsed');
    body.classList.toggle('sidebar-collapsed', isCollapsed);
    localStorage.setItem('sidebar-collapsed', isCollapsed ? '1' : '0');
}

// Restore sidebar state on load
(function() {
    if (localStorage.getItem('sidebar-collapsed') === '1') {
        const sidebar = document.getElementById('sidebar');
        if (sidebar) {
            sidebar.classList.add('collapsed');
            document.body.classList.add('sidebar-collapsed');
        }
    }
})();

// ─── Mobile Drawer Toggle ────────────────────────────────────
function toggleMobileDrawer() {
    const drawer = document.getElementById('mobile-drawer');
    const panel = document.getElementById('mobile-drawer-panel');
    const backdrop = document.getElementById('mobile-drawer-backdrop');
    
    if (drawer && panel && backdrop) {
        if (drawer.classList.contains('hidden')) {
            drawer.classList.remove('hidden');
            setTimeout(() => {
                panel.classList.remove('-translate-x-full');
                backdrop.classList.remove('opacity-0');
                backdrop.classList.add('opacity-100');
            }, 10);
        } else {
            panel.classList.add('-translate-x-full');
            backdrop.classList.remove('opacity-100');
            backdrop.classList.add('opacity-0');
            setTimeout(() => {
                drawer.classList.add('hidden');
            }, 300);
        }
    }
}

// ─── Ctrl+K to focus search input ─────────────────────────────
document.addEventListener('keydown', e => {
    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
        e.preventDefault();
        const searchInput = document.getElementById('global-search');
        if (searchInput) searchInput.focus();
    }
});

// Redirect search on other pages
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('global-search');
    if (searchInput) {
        searchInput.addEventListener('keydown', e => {
            if (e.key === 'Enter') {
                const query = searchInput.value.trim();
                if (window.location.pathname !== '/') {
                    window.location.href = '/?search=' + encodeURIComponent(query);
                }
            }
        });
    }
});

// ─── KBD Badge Focus Transitions ────────────────────
document.addEventListener('DOMContentLoaded', () => {
    // KBD search badge focus transitions
    const globSearch = document.getElementById('global-search');
    const kbdBadge = document.getElementById('search-kbd');
    if (globSearch && kbdBadge) {
        globSearch.addEventListener('focus', () => kbdBadge.classList.add('opacity-0'));
        globSearch.addEventListener('blur', () => {
            if (!globSearch.value) kbdBadge.classList.remove('opacity-0');
        });
    }
});

// ─── AI Companion Chatbot Engine ────────────────────────────────
let chatHistory = [];
let activeConversationId = null;
const csrfToken = '{{ csrf_token() }}';

function toggleChatbot() {
    const panel = document.getElementById('ai-chatbot-panel');
    const fab = document.getElementById('ai-chatbot-fab');
    if (panel) {
        if (panel.classList.contains('opacity-0')) {
            panel.classList.remove('opacity-0', 'scale-95', 'pointer-events-none');
            panel.classList.add('opacity-100', 'scale-100', 'pointer-events-auto');
            // Scroll to bottom
            const feed = document.getElementById('chatbot-feed');
            if (feed) feed.scrollTop = feed.scrollHeight;
        } else {
            panel.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
            panel.classList.remove('opacity-100', 'scale-100', 'pointer-events-auto');
        }
    }
}

function sendPresetPrompt(promptText) {
    const input = document.getElementById('chatbot-input');
    if (input) {
        input.value = promptText;
        submitChatQuery();
    }
}

function submitChatQuery() {
    const input = document.getElementById('chatbot-input');
    if (!input) return;
    const query = input.value.trim();
    if (!query) return;

    // Clear input
    input.value = '';

    // Append User Message
    appendChatMessage('user', query);

    // Show Typing Indicator
    showTypingIndicator(true);

    // Call API Securely
    fetch('/api/chat', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            message: query,
            conversation_id: activeConversationId,
            history: chatHistory
        })
    })
    .then(response => response.json())
    .then(data => {
        showTypingIndicator(false);
        const reply = data.reply || "*Sorry, I could not process the response.*";
        appendChatMessage('assistant', reply);
        
        if (data.conversation_id) {
            activeConversationId = data.conversation_id;
        }
        
        // Save to History
        chatHistory.push({ role: 'user', content: query });
        chatHistory.push({ role: 'assistant', content: reply });
    })
    .catch(error => {
        showTypingIndicator(false);
        appendChatMessage('assistant', "*Connection error. Please check your network and try again.*");
        console.error(error);
    });
}

function appendChatMessage(role, text) {
    const feed = document.getElementById('chatbot-feed');
    if (!feed) return;

    const wrapper = document.createElement('div');
    wrapper.className = `flex ${role === 'user' ? 'justify-end' : 'justify-start'} mb-3 items-end gap-2 animate-fade-in-up`;

    // Avatar if assistant
    let avatarHTML = '';
    if (role === 'assistant') {
        avatarHTML = `
            <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-ans-dark-green to-ans-light-green flex-shrink-0 flex items-center justify-center text-white text-[10px] font-bold shadow-md shadow-ans-dark-green/10">
                AI
            </div>
        `;
    }

    const bubble = document.createElement('div');
    bubble.className = `max-w-[78%] px-4 py-3 rounded-2xl text-xs leading-relaxed shadow-sm ${
        role === 'user' 
        ? 'bg-gradient-to-r from-ans-dark-green to-ans-seal-green text-white rounded-br-none' 
        : 'bg-white/90 text-gray-800 border border-gray-100 rounded-bl-none backdrop-blur-sm'
    }`;

    // Simple markdown formatting
    bubble.innerHTML = parseBotMarkdown(text);
    
    if (role === 'assistant') {
        wrapper.appendChild(bubble);
        feed.appendChild(wrapper);
    } else {
        wrapper.appendChild(bubble);
        feed.appendChild(wrapper);
    }

    // Scroll to bottom
    feed.scrollTop = feed.scrollHeight;
}

function showTypingIndicator(show) {
    let indicator = document.getElementById('chatbot-typing-indicator');
    const feed = document.getElementById('chatbot-feed');
    if (!feed) return;

    if (show) {
        if (!indicator) {
            indicator = document.createElement('div');
            indicator.id = 'chatbot-typing-indicator';
            indicator.className = 'flex justify-start mb-3 items-center gap-2 animate-pulse';
            indicator.innerHTML = `
                <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-ans-dark-green to-ans-light-green flex-shrink-0 flex items-center justify-center text-white text-[10px] font-bold shadow-md">
                    AI
                </div>
                <div class="bg-white/80 border border-gray-100 px-4 py-2.5 rounded-2xl rounded-bl-none text-xs text-gray-400 flex items-center gap-1.5 backdrop-blur-sm">
                    <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                    <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                    <span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                </div>
            `;
            feed.appendChild(indicator);
        }
        feed.scrollTop = feed.scrollHeight;
    } else {
        if (indicator) {
            indicator.remove();
        }
    }
}

function copyToClipboard(base64Text, btn) {
    try {
        const text = decodeURIComponent(escape(atob(base64Text)));
        navigator.clipboard.writeText(text).then(() => {
            const span = btn.querySelector('span');
            const originalText = span.textContent;
            span.textContent = 'Copied!';
            btn.classList.add('bg-green-600/30', 'border-green-500/30', 'text-green-300');
            setTimeout(() => {
                span.textContent = originalText;
                btn.classList.remove('bg-green-600/30', 'border-green-500/30', 'text-green-300');
            }, 2000);
        });
    } catch (err) {
        console.error('Failed to copy: ', err);
    }
}

function parseBotMarkdown(text) {
    // Escape HTML first to prevent XSS
    let escaped = text
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;");

    // Code blocks with Copy Button
    let codeBlockCount = 0;
    let formatted = escaped.replace(/```(?:[a-zA-Z0-9]+)?\n([\s\S]*?)```/g, function(match, code) {
        codeBlockCount++;
        const cleanCode = code.trim();
        const base64Code = btoa(unescape(encodeURIComponent(cleanCode)));
        
        return `<div class="relative group my-3">
            <div class="absolute right-3 top-3 opacity-0 group-hover:opacity-100 transition-opacity duration-200 z-10">
                <button onclick="copyToClipboard('${base64Code}', this)" class="bg-white/10 hover:bg-white/20 text-white text-[10px] px-2 py-1.5 rounded-lg border border-white/10 transition-all font-sans flex items-center gap-1 shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m-3 8h3m-3 3h3m-9-13h3m-3 3h3"></path></svg>
                    <span>Copy</span>
                </button>
            </div>
            <pre class="bg-gray-900/95 text-gray-100 rounded-2xl p-4 font-mono text-[10px] overflow-x-auto whitespace-pre leading-relaxed border border-white/5">${cleanCode}</pre>
        </div>`;
    });

    // Headings (H1 to H6)
    formatted = formatted.replace(/^(#{1,6})\s+(.*?)$/gm, function(match, hashes, title) {
        const level = hashes.length;
        const classes = {
            1: 'text-base font-bold my-2 text-ans-dark-green',
            2: 'text-sm font-bold my-2 text-ans-dark-green',
            3: 'text-xs font-bold my-1.5 text-ans-dark-green',
            4: 'text-xs font-bold my-1 text-gray-800',
            5: 'text-[11px] font-bold my-1 text-gray-700',
            6: 'text-[11px] font-bold my-1 text-gray-600'
        };
        const cls = classes[level] || 'font-bold';
        return `<div class="${cls}">${title}</div>`;
    });

    // Bold (**text** or __text__)
    formatted = formatted.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
    formatted = formatted.replace(/__(.*?)__/g, '<strong>$1</strong>');

    // Italic (*text* or _text_)
    formatted = formatted.replace(/\*(.*?)\*/g, '<em>$1</em>');
    formatted = formatted.replace(/_(.*?)_/g, '<em>$1</em>');

    // Bullet lists starting with - or *
    formatted = formatted.replace(/^\s*[-*]\s+(.*?)$/gm, '<li class="ml-4 list-disc text-gray-700 my-0.5">$1</li>');

    // Group consecutive list items into a ul container
    formatted = formatted.replace(/(<li.*?>.*?<\/li>)+/g, '<ul class="my-2 space-y-1">$1</ul>');

    // Convert newlines to <br> (but not inside <pre> tags)
    let parts = formatted.split(/(<pre[\s\S]*?<\/pre>)/g);
    for (let i = 0; i < parts.length; i++) {
        if (!parts[i].startsWith('<pre')) {
            parts[i] = parts[i].replace(/\n/g, '<br>');
        }
    }
    formatted = parts.join('');

    return formatted;
}

// Bind Enter Key
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('chatbot-input');
    if (input) {
        input.addEventListener('keydown', e => {
            if (e.key === 'Enter') {
                e.preventDefault();
                submitChatQuery();
            }
        });
    }
});
</script>

@auth
    <!-- Chat Drawer Panel -->
    <div id="ai-chatbot-panel" class="fixed bottom-24 right-6 z-50 w-96 h-[520px] bg-white/80 backdrop-blur-xl border border-white/20 rounded-3xl shadow-2xl flex flex-col overflow-hidden mb-4 opacity-0 scale-95 pointer-events-none transition-all duration-300 transform origin-bottom-right">
        <!-- Header -->
        <div class="p-4 bg-gradient-to-r from-ans-dark-green to-ans-seal-green text-white flex items-center justify-between shadow-md">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center border border-white/10 shadow-inner">
                    <svg class="w-5 h-5 text-ans-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                </div>
                <div>
                    <h4 class="text-sm font-bold tracking-tight">AINS AI Companion</h4>
                    <span class="text-[9px] text-ans-light-green font-bold tracking-wider uppercase flex items-center gap-1">
                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-ping"></span>
                        EdTech Advisor
                    </span>
                </div>
            </div>
            <button onclick="toggleChatbot()" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 transition-all flex items-center justify-center" title="Close Panel">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Chat Feed -->
        <div id="chatbot-feed" class="flex-1 p-4 overflow-y-auto space-y-4 bg-gray-50/30">
            <!-- Welcome Message -->
            <div class="flex justify-start mb-3 items-end gap-2">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-ans-dark-green to-ans-light-green flex-shrink-0 flex items-center justify-center text-white text-[10px] font-bold shadow-md shadow-ans-dark-green/10">
                    AI
                </div>
                <div class="max-w-[78%] px-4 py-3 bg-white border border-gray-100 rounded-2xl rounded-bl-none text-xs leading-relaxed shadow-sm text-gray-800">
                    <p class="font-bold text-ans-dark-green mb-1">Hello!</p>
                    I am <strong>AINS AI Companion</strong>, your digital advisor at ANS. I can help you to:
                    <ul class="list-disc list-inside mt-1.5 space-y-1 text-gray-600">
                        <li>Design lesson plans under the <strong>SAMR</strong> model.</li>
                        <li>Write <strong>efficient prompts</strong> for your classes.</li>
                        <li>Recommend the best <strong>approved apps</strong> in the portal.</li>
                    </ul>
                    <p class="mt-2 text-gray-500 italic text-[10px]">What would you like to talk about today?</p>
                </div>
            </div>

            <!-- Suggested Quick Links -->
            <div class="space-y-2 pl-10">
                @php
                    $suggestedTools = \App\Models\Tool::approved()->where('is_official', true)->take(3)->get();
                @endphp
                @if($suggestedTools->count() > 0)
                    @foreach($suggestedTools as $sTool)
                        @php
                            $promptText = "Write a structured prompt for the teacher to generate an activity with " . $sTool->name . ".";
                            if (str_contains(strtolower($sTool->category), 'plan') || str_contains(strtolower($sTool->name), 'stitch')) {
                                $promptText = "How can I integrate " . $sTool->name . " in a lesson plan under the SAMR model?";
                            } elseif (str_contains(strtolower($sTool->category), 'assist') || str_contains(strtolower($sTool->name), 'antigravity')) {
                                $promptText = "How does " . $sTool->name . " help me as a virtual assistant in my classes?";
                            } elseif (str_contains(strtolower($sTool->category), 'product') || str_contains(strtolower($sTool->name), 'flow') || str_contains(strtolower($sTool->name), 'pomelo')) {
                                $promptText = "How do I optimize my administrative workflow using " . $sTool->name . "?";
                            }
                        @endphp
                        <button onclick="sendPresetPrompt('{{ addslashes($promptText) }}')" class="block w-full text-left text-[11px] px-3 py-2 bg-white hover:bg-ans-light-green/10 border border-gray-100 rounded-xl text-gray-700 hover:text-ans-dark-green hover:border-ans-light-green transition-all shadow-sm truncate">
                            {{ $sTool->name }}: {{ str_replace(['?', '¿'], '', $promptText) }}
                        </button>
                    @endforeach
                @else
                    <p class="text-[10px] text-gray-400 italic px-2">Write your question below. The administrator has not configured suggested tools yet.</p>
                @endif
            </div>
        </div>

        <!-- Input Box -->
        <div class="p-3 bg-white border-t border-gray-100 flex items-center gap-2">
            <input type="text" id="chatbot-input" placeholder="Ask your EdTech Companion..." class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 focus:border-ans-dark-green focus:ring-1 focus:ring-ans-dark-green text-xs bg-gray-50 outline-none transition-all">
            <button onclick="submitChatQuery()" class="w-9 h-9 rounded-xl bg-ans-dark-green hover:bg-ans-seal-green text-white flex items-center justify-center transition-all shadow-md shadow-ans-dark-green/10" title="Send Message">
                <svg class="w-4 h-4 transform rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
            </button>
        </div>
    </div>

    <!-- Floating Chat Button (FAB) -->
    <button id="ai-chatbot-fab" onclick="toggleChatbot()" class="fixed bottom-6 right-6 z-50 w-14 h-14 bg-gradient-to-tr from-ans-dark-green to-ans-seal-green hover:from-ans-seal-green hover:to-ans-dark-green text-white rounded-full flex items-center justify-center shadow-xl shadow-ans-dark-green/20 hover:shadow-2xl hover:scale-105 transition-all duration-300 group pointer-events-auto" title="Chat with AI Companion">
        <!-- Pulse Effect -->
        <div class="absolute inset-0 rounded-full bg-ans-dark-green/30 animate-ping group-hover:animate-none opacity-70"></div>
        <!-- Icon -->
        <svg class="w-6 h-6 relative z-10 transition-transform duration-300 group-hover:rotate-6 text-ans-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
    </button>
@endauth

@yield('modals')

</body>
</html>
