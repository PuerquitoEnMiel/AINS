@extends('layouts.app')

@section('header-title', 'AI Detection Tools')
@section('header-subtitle', 'Herramientas para detectar contenido generado por IA en trabajos estudiantiles')

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
            <div class="w-16 h-16 rounded-2xl bg-red-500/20 border border-red-500/30 flex items-center justify-center text-3xl flex-shrink-0">
                🔍
            </div>
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-white mb-2">Centro de Detección de IA</h1>
                <p class="text-red-100/80 text-sm md:text-base max-w-2xl">
                    Herramientas especializadas para verificar la integridad académica. Identifica contenido generado por IA en ensayos, trabajos de investigación y tareas estudiantiles.
                </p>
                <div class="flex flex-wrap gap-3 mt-4">
                    <span class="px-3 py-1 bg-red-500/20 border border-red-500/30 rounded-full text-xs text-red-200 font-medium">🔒 Solo para Docentes y Administradores</span>
                    <span class="px-3 py-1 bg-white/10 border border-white/20 rounded-full text-xs text-white/70 font-medium">{{ $tools->count() }} herramientas disponibles</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Info Card -->
<div class="mb-6 p-5 bg-amber-50 border border-amber-200 rounded-2xl flex gap-4">
    <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
    <div class="text-sm text-amber-800">
        <p class="font-semibold mb-1">Uso Responsable de las Herramientas de Detección</p>
        <p class="text-amber-700/80">Estas herramientas son una <strong>ayuda</strong>, no un veredicto definitivo. Ninguna herramienta tiene precisión del 100%. Se recomienda combinar estos resultados con una evaluación pedagógica y conversación directa con el estudiante antes de tomar decisiones académicas.</p>
    </div>
</div>

@if($tools->isEmpty())
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-16 text-center">
    <div class="flex flex-col items-center gap-4">
        <div class="w-16 h-16 rounded-2xl bg-gray-50 flex items-center justify-center text-3xl">🔍</div>
        <h3 class="font-semibold text-gray-700">No hay herramientas de detección aún</h3>
        <p class="text-sm text-gray-400 max-w-xs">El administrador puede agregar herramientas de detección de IA desde el panel de administración.</p>
        @if(Auth::user()->isAdmin())
        <a href="{{ route('admin.tools.create') }}" class="mt-2 px-5 py-2.5 bg-ans-dark-green text-white rounded-xl text-sm font-semibold hover:bg-ans-seal-green transition-all">
            + Agregar herramienta
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
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl bg-red-50 border border-red-100">🔍</div>
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
                <span class="px-2 py-0.5 bg-blue-50 text-blue-700 text-xs font-semibold rounded-full flex-shrink-0">Oficial</span>
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
                    Ver herramienta
                    <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </span>
            </div>
        </div>
    </a>
    @endforeach
</div>
@endif

@endsection
