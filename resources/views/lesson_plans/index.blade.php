@extends('layouts.app')

@section('header-title', 'AI Lesson Planner')
@section('header-subtitle', 'Diseña y gestiona tus planeaciones de clase potenciadas con Inteligencia Artificial')

@section('content')
<div class="space-y-8 animate-fade-in">
    <!-- Header Action Area -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h3 class="text-lg font-bold text-gray-800">Mis Planeaciones</h3>
            <p class="text-sm text-gray-500">Historial de clases diseñadas con herramientas EdTech AINS</p>
        </div>
        <a href="{{ route('lesson-plans.create') }}" class="flex items-center justify-center gap-2 bg-gradient-to-r from-ans-dark-green to-ans-seal-green hover:shadow-lg hover:shadow-ans-dark-green/20 text-white px-6 py-3 rounded-xl font-bold transition-all hover:-translate-y-0.5 self-start">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Nueva Planeación
        </a>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-xl shadow-sm text-sm">
            {{ session('success') }}
        </div>
    @endif

    <!-- Content Grid -->
    @if($plans->isEmpty())
        <!-- Empty State -->
        <div class="bg-white border border-gray-100 rounded-3xl p-12 text-center shadow-sm max-w-xl mx-auto mt-8 flex flex-col items-center">
            <div class="w-20 h-20 bg-ans-light-green/10 rounded-2xl flex items-center justify-center text-ans-dark-green mb-6">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <h4 class="text-xl font-extrabold text-gray-800 mb-2">No tienes planeaciones aún</h4>
            <p class="text-gray-500 text-sm mb-8 leading-relaxed">
                Utiliza nuestro asistente pedagógico para estructurar tus clases en segundos, integrando las herramientas digitales autorizadas de ANS.
            </p>
            <a href="{{ route('lesson-plans.create') }}" class="bg-ans-orange hover:bg-[#e67600] text-white px-6 py-3 rounded-xl font-semibold shadow-md shadow-ans-orange/20 transition-all hover:-translate-y-0.5">
                Comenzar a Planificar
            </a>
        </div>
    @else
        <!-- Grid of Plans -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($plans as $plan)
                <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden hover:shadow-xl hover:shadow-gray-200/50 transition-all group flex flex-col justify-between h-64">
                    <!-- Top section -->
                    <div class="p-6">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="px-2.5 py-1 bg-ans-light-green/10 text-ans-dark-green text-[10px] font-bold uppercase rounded-lg">
                                {{ $plan->subject }}
                            </span>
                            <span class="px-2.5 py-1 bg-ans-orange/10 text-ans-orange text-[10px] font-bold uppercase rounded-lg">
                                {{ $plan->grade_level }}
                            </span>
                        </div>
                        <h4 class="font-bold text-gray-800 line-clamp-2 group-hover:text-ans-dark-green transition-colors text-base">
                            <a href="{{ route('lesson-plans.show', $plan) }}">{{ $plan->title }}</a>
                        </h4>
                        <p class="text-gray-500 text-xs mt-2 line-clamp-3 leading-relaxed">
                            {{ $plan->objectives }}
                        </p>
                    </div>

                    <!-- Bottom Details & Actions -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between text-xs text-gray-400">
                        <div class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>{{ $plan->duration }}</span>
                        </div>

                        <div class="flex items-center gap-2">
                            <a href="{{ route('lesson-plans.show', $plan) }}" class="text-ans-dark-green hover:underline font-semibold" title="Ver plan completo">
                                Ver Plan
                            </a>
                            <span class="text-gray-200">|</span>
                            <form action="{{ route('lesson-plans.destroy', $plan) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este plan de clase?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 transition-colors" title="Eliminar plan">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
