<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Curated AI Tools - ANS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Montserrat:wght@500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 font-sans antialiased min-h-screen flex">

    <!-- Sidebar -->
    <aside class="w-64 bg-ans-dark-green text-white flex-shrink-0 min-h-screen hidden md:flex flex-col">
        <div class="p-6">
            <h1 class="text-2xl font-heading font-bold text-white tracking-tight">AI Tools</h1>
            <p class="text-sm text-ans-light-green mt-1">American Nicaraguan School</p>
        </div>
        
        <nav class="flex-1 px-4 space-y-2 mt-4">
            <a href="/" class="block px-4 py-2 rounded bg-ans-seal-green text-white font-medium">Todos</a>
            <a href="#" class="block px-4 py-2 rounded hover:bg-ans-seal-green text-gray-200 transition">Video</a>
            <a href="#" class="block px-4 py-2 rounded hover:bg-ans-seal-green text-gray-200 transition">Photos</a>
            <a href="#" class="block px-4 py-2 rounded hover:bg-ans-seal-green text-gray-200 transition">Presentations</a>
            <a href="#" class="block px-4 py-2 rounded hover:bg-ans-seal-green text-gray-200 transition">Dashboard/Analysis</a>
            <a href="#" class="block px-4 py-2 rounded hover:bg-ans-seal-green text-gray-200 transition">Music</a>
            <a href="#" class="block px-4 py-2 rounded hover:bg-ans-seal-green text-gray-200 transition">Editor</a>
        </nav>

        <!-- Admin Link -->
        @auth
        @if(Auth::user()->role === 'admin')
        <div class="px-4 pb-6 mt-auto">
            <a href="/admin/solicitudes" class="block px-4 py-2 rounded border border-white/20 text-gray-300 hover:bg-ans-seal-green text-sm transition text-center">
                ⚙ Panel Admin
            </a>
        </div>
        @endif
        @endauth
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-100 p-4 flex justify-between items-center sticky top-0 z-10">
            <div class="flex-1 max-w-xl">
                <input type="search" placeholder="Buscar herramientas..." class="w-full bg-gray-100 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-ans-dark-green">
            </div>
            <div class="ml-4 flex items-center space-x-4">
                @auth
                    <div class="flex items-center space-x-3 border-r border-gray-200 pr-4">
                        @if(Auth::user()->avatar)
                            <img src="{{ Auth::user()->avatar }}" alt="Avatar" class="w-8 h-8 rounded-full">
                        @endif
                        <span class="text-sm font-medium text-gray-700">{{ Auth::user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-xs text-red-500 hover:text-red-700">Salir</button>
                        </form>
                    </div>
                    <button onclick="window.location='/solicitudes/nueva'" class="bg-ans-orange hover:bg-orange-600 text-white px-4 py-2 rounded-lg font-medium shadow-sm transition">Sugerir App</button>
                @else
                    <a href="/auth/google" class="flex items-center space-x-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg font-medium shadow-sm transition">
                        <svg class="w-4 h-4" viewBox="0 0 24 24">
                            <path fill="currentColor" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                            <path fill="#34A853" d="M12 24c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 21.53 7.7 24 12 24z" />
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                            <path fill="#EA4335" d="M12 4.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 1.18 14.97 0 12 0 7.7 0 3.99 2.47 2.18 6.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                        </svg>
                        <span>Iniciar Sesión</span>
                    </a>
                @endauth
            </div>
        </header>

        <!-- Content -->
        <main class="flex-1 p-8 overflow-y-auto">
            @yield('content')
        </main>
    </div>

</body>
</html>
