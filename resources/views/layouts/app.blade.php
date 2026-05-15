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
        <div class="px-4 pb-6 mt-auto">
            <a href="/admin/solicitudes" class="block px-4 py-2 rounded border border-white/20 text-gray-300 hover:bg-ans-seal-green text-sm transition text-center">
                ⚙ Panel Admin
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-100 p-4 flex justify-between items-center sticky top-0 z-10">
            <div class="flex-1 max-w-xl">
                <input type="search" placeholder="Buscar herramientas..." class="w-full bg-gray-100 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-ans-dark-green">
            </div>
            <div class="ml-4 flex items-center space-x-4">
                <button onclick="window.location='/solicitudes/nueva'" class="bg-ans-orange hover:bg-orange-600 text-white px-4 py-2 rounded-lg font-medium shadow-sm transition">Sugerir App</button>
            </div>
        </header>

        <!-- Content -->
        <main class="flex-1 p-8 overflow-y-auto">
            @yield('content')
        </main>
    </div>

</body>
</html>
