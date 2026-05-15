@extends('layouts.app')

@section('content')
<div class="mb-8">
    <h2 class="text-3xl font-heading font-bold text-gray-900">Bienvenido al Directorio de IA</h2>
    <p class="text-gray-600 mt-2">Explora y descubre herramientas impulsadas por Inteligencia Artificial aprobadas para su uso en el American Nicaraguan School.</p>
</div>

<!-- Official Tools -->
<div class="mb-10">
    <h3 class="text-xl font-heading font-semibold text-gray-800 mb-4 flex items-center">
        <span class="w-2 h-6 bg-ans-orange rounded mr-2"></span>
        Herramientas Oficiales MINED/GOOGLE
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- Stitch -->
        <div class="bg-white rounded-xl shadow-sm border border-orange-100 p-6 hover:shadow-md transition">
            <div class="w-12 h-12 bg-ans-orange/10 rounded-lg flex items-center justify-center mb-4 text-ans-orange font-bold text-xl">S</div>
            <h4 class="font-heading font-bold text-gray-900 text-lg">Stitch</h4>
            <p class="text-sm text-gray-600 mt-2 line-clamp-2">Plataforma centralizada de integraciones y automatización de flujos de trabajo.</p>
            <div class="mt-4 flex gap-2">
                <span class="text-xs font-medium bg-gray-100 text-gray-600 px-2 py-1 rounded">Oficial</span>
            </div>
        </div>

        <!-- Pomelo -->
        <div class="bg-white rounded-xl shadow-sm border border-orange-100 p-6 hover:shadow-md transition">
            <div class="w-12 h-12 bg-ans-orange/10 rounded-lg flex items-center justify-center mb-4 text-ans-orange font-bold text-xl">P</div>
            <h4 class="font-heading font-bold text-gray-900 text-lg">Pomelo</h4>
            <p class="text-sm text-gray-600 mt-2 line-clamp-2">Sistema inteligente de gestión de aprendizaje y evaluación académica.</p>
            <div class="mt-4 flex gap-2">
                <span class="text-xs font-medium bg-gray-100 text-gray-600 px-2 py-1 rounded">Oficial</span>
            </div>
        </div>

        <!-- Antigravity -->
        <div class="bg-white rounded-xl shadow-sm border border-orange-100 p-6 hover:shadow-md transition">
            <div class="w-12 h-12 bg-ans-orange/10 rounded-lg flex items-center justify-center mb-4 text-ans-orange font-bold text-xl">A</div>
            <h4 class="font-heading font-bold text-gray-900 text-lg">Antigravity</h4>
            <p class="text-sm text-gray-600 mt-2 line-clamp-2">Asistente de programación e infraestructura impulsado por IA.</p>
            <div class="mt-4 flex gap-2">
                <span class="text-xs font-medium bg-gray-100 text-gray-600 px-2 py-1 rounded">Oficial</span>
            </div>
        </div>

        <!-- Flow -->
        <div class="bg-white rounded-xl shadow-sm border border-orange-100 p-6 hover:shadow-md transition">
            <div class="w-12 h-12 bg-ans-orange/10 rounded-lg flex items-center justify-center mb-4 text-ans-orange font-bold text-xl">F</div>
            <h4 class="font-heading font-bold text-gray-900 text-lg">Flow</h4>
            <p class="text-sm text-gray-600 mt-2 line-clamp-2">Herramienta colaborativa para planificación y seguimiento.</p>
            <div class="mt-4 flex gap-2">
                <span class="text-xs font-medium bg-gray-100 text-gray-600 px-2 py-1 rounded">Oficial</span>
            </div>
        </div>

    </div>
</div>

<!-- Catálogo General -->
<div>
    <div class="flex justify-between items-end mb-4">
        <h3 class="text-xl font-heading font-semibold text-gray-800 flex items-center">
            <span class="w-2 h-6 bg-ans-dark-green rounded mr-2"></span>
            Catálogo General
        </h3>
        <div class="flex gap-2">
            <button class="text-sm px-3 py-1 bg-white border border-gray-200 rounded-md hover:bg-gray-50">Google Workspace</button>
            <button class="text-sm px-3 py-1 bg-white border border-gray-200 rounded-md hover:bg-gray-50">3rd Party</button>
        </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($tools ?? [] as $tool)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition">
            <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center mb-4 text-gray-500 font-bold text-xl">
                {{ substr($tool->name, 0, 1) }}
            </div>
            <h4 class="font-heading font-bold text-gray-900 text-lg">{{ $tool->name }}</h4>
            <p class="text-sm text-gray-600 mt-2 line-clamp-2">{{ $tool->description }}</p>
            <div class="mt-4 flex gap-2">
                <span class="text-xs font-medium bg-gray-100 text-gray-600 px-2 py-1 rounded">
                    {{ $tool->is_google_workspace ? 'Workspace' : '3rd Party' }}
                </span>
            </div>
        </div>
        @endforeach

        @if(empty($tools) || count($tools) == 0)
        <!-- Demo Card if DB is empty -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition">
            <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center mb-4 text-gray-500 font-bold text-xl">C</div>
            <h4 class="font-heading font-bold text-gray-900 text-lg">ChatGPT</h4>
            <p class="text-sm text-gray-600 mt-2 line-clamp-2">Modelo de lenguaje avanzado para asistencia general.</p>
            <div class="mt-4 flex gap-2">
                <span class="text-xs font-medium bg-gray-100 text-gray-600 px-2 py-1 rounded">3rd Party</span>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Floating Chatbot Button -->
<button class="fixed bottom-8 right-8 w-14 h-14 bg-ans-dark-green text-white rounded-full shadow-lg flex items-center justify-center hover:bg-ans-seal-green transition hover:scale-105 z-50">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
    </svg>
</button>

@endsection
