@extends('layouts.app')

@section('header-title', 'AI Lesson Planner')
@section('header-subtitle', 'Visualiza y exporta tu planificación de clase pedagógica')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

<style>
    /* ─── Stylesheet for clean printing ─── */
    @media print {
        body, html, main {
            background: white !important;
            color: black !important;
            height: auto !important;
            overflow: visible !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        aside, header, #print-action-bar, #tools-sidebar-column, #ai-chatbot-panel, #ai-chatbot-fab, footer {
            display: none !important;
        }
        #main-print-area {
            width: 100% !important;
            max-width: 100% !important;
            border: none !important;
            padding: 0 !important;
            box-shadow: none !important;
            margin: 0 !important;
        }
        .prose {
            max-width: 100% !important;
            font-size: 14px !important;
            line-height: 1.6 !important;
        }
    }
</style>

<div class="space-y-6 animate-fade-in">
    <!-- ACTION BAR -->
    <div id="print-action-bar" class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white border border-gray-100 p-6 rounded-2xl shadow-sm">
        <div class="flex items-center gap-3">
            <a href="{{ route('lesson-plans.index') }}" class="w-10 h-10 rounded-xl border border-gray-200 flex items-center justify-center text-gray-500 hover:text-ans-dark-green hover:bg-gray-50 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h3 class="font-extrabold text-gray-800 text-base">Plan: {{ $lessonPlan->title }}</h3>
                <p class="text-xs text-gray-500">{{ $lessonPlan->subject }} • {{ $lessonPlan->grade_level }}</p>
            </div>
        </div>

        <div class="flex items-center gap-2.5">
            <button onclick="exportToGoogleDocs()" id="export-btn" class="px-5 py-2.5 bg-ans-dark-green hover:bg-[#0f483c] text-white text-xs font-bold rounded-xl shadow-md shadow-ans-dark-green/20 transition-all hover:-translate-y-0.5 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span id="export-text">Exportar a Google Docs</span>
            </button>
            <button onclick="window.print()" class="px-5 py-2.5 bg-ans-orange hover:bg-[#e67600] text-white text-xs font-bold rounded-xl shadow-md shadow-ans-orange/20 transition-all hover:-translate-y-0.5 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Imprimir / PDF
            </button>
            <form action="{{ route('lesson-plans.destroy', $lessonPlan) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este plan?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-5 py-2.5 border border-red-200 text-red-600 hover:bg-red-50 text-xs font-bold rounded-xl transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Eliminar
                </button>
            </form>
        </div>
    </div>

    <!-- MAIN TWO-COLUMN LAYOUT -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        
        <!-- Left: Document View (Plan Content) -->
        <div id="main-print-area" class="lg:col-span-2 bg-white border border-gray-100 rounded-3xl p-8 shadow-sm">
            <!-- Header Meta block for print (visible only or styled beautifully) -->
            <div class="border-b border-gray-100 pb-6 mb-6">
                <div class="flex items-center gap-2 mb-2">
                    <span class="px-2.5 py-1 bg-ans-light-green/10 text-ans-dark-green text-[10px] font-bold uppercase rounded-lg">
                        {{ $lessonPlan->subject }}
                    </span>
                    <span class="px-2.5 py-1 bg-ans-orange/10 text-ans-orange text-[10px] font-bold uppercase rounded-lg">
                        {{ $lessonPlan->grade_level }}
                    </span>
                </div>
                <h1 class="text-2xl font-extrabold text-gray-800 tracking-tight">{{ $lessonPlan->title }}</h1>
                
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-6 text-xs text-gray-500 bg-gray-50 p-4 rounded-xl border border-gray-100/50">
                    <div>
                        <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Duración</span>
                        <span class="font-bold text-gray-700 mt-0.5 block">{{ $lessonPlan->duration ?? 'No especificada' }}</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Creado el</span>
                        <span class="font-bold text-gray-700 mt-0.5 block">{{ $lessonPlan->created_at->format('d/m/Y') }}</span>
                    </div>
                    <div class="col-span-2 md:col-span-1">
                        <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Creado por</span>
                        <span class="font-bold text-gray-700 mt-0.5 block">{{ $lessonPlan->user->name }}</span>
                    </div>
                </div>
            </div>

            <!-- Markdown Rendered Content -->
            <article id="markdown-rendered-content" class="prose max-w-none text-gray-700 text-sm leading-relaxed prose-headings:text-gray-800 prose-headings:font-heading prose-strong:text-ans-dark-green prose-code:bg-gray-50 prose-code:px-2 prose-code:py-1 prose-code:rounded prose-code:border prose-code:border-gray-100 prose-a:text-ans-orange prose-a:underline"></article>
        </div>

        <!-- Right: Connected AINS Tools (Sidebar) -->
        <div id="tools-sidebar-column" class="space-y-6">
            <div class="bg-white border border-gray-100 rounded-3xl p-6 shadow-sm">
                <h4 class="font-extrabold text-gray-800 text-sm mb-4 uppercase tracking-wider">Aplicaciones Asociadas</h4>
                
                @if($tools->isEmpty())
                    <p class="text-xs text-gray-400 italic">No se asociaron herramientas del catálogo a esta planeación.</p>
                @else
                    <div class="space-y-4">
                        @foreach($tools as $tool)
                            <div class="p-4 border border-gray-100 rounded-2xl hover:border-ans-dark-green/20 hover:bg-ans-light-green/5 transition-all flex flex-col justify-between h-40">
                                <div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-[10px] font-bold text-ans-dark-green uppercase bg-ans-light-green/10 px-2 py-0.5 rounded">
                                            {{ $tool->category }}
                                        </span>
                                    </div>
                                    <h5 class="font-bold text-gray-800 text-sm mt-2 line-clamp-1">{{ $tool->name }}</h5>
                                    <p class="text-[11px] text-gray-500 line-clamp-2 mt-1 leading-relaxed">{{ $tool->description }}</p>
                                </div>
                                <a href="{{ route('tools.show', $tool) }}" class="text-[11px] text-ans-orange hover:text-[#e67600] font-bold hover:underline self-start mt-2">
                                    Ver Ficha Técnica →
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Print instruction card -->
            <div class="bg-gradient-to-r from-ans-dark-green/5 to-ans-seal-green/10 border border-ans-dark-green/10 rounded-3xl p-6 shadow-sm">
                <h5 class="font-bold text-ans-dark-green text-xs uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-ans-dark-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Tip de Exportación
                </h5>
                <p class="text-[11px] text-gray-600 leading-relaxed">
                    Presiona el botón **Imprimir / PDF** para abrir la ventana de impresión del navegador. Elige la opción **Guardar como PDF** en tu navegador para archivar o enviar tu planificación de forma digital.
                </p>
            </div>
        </div>

    </div>
</div>

<script>
    async function exportToGoogleDocs() {
        const btn = document.getElementById('export-btn');
        const text = document.getElementById('export-text');
        const originalText = text.innerText;

        btn.disabled = true;
        text.innerText = 'Exportando...';

        try {
            const response = await fetch("{{ route('lesson-plans.export', $lessonPlan) }}", {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.redirect) {
                window.location.href = data.redirect;
            } else if (data.success && data.url) {
                window.open(data.url, '_blank');
                text.innerText = '¡Exportado!';
                setTimeout(() => {
                    text.innerText = originalText;
                    btn.disabled = false;
                }, 3000);
            } else {
                alert(data.error || 'Ocurrió un error al exportar.');
                text.innerText = originalText;
                btn.disabled = false;
            }
        } catch (err) {
            console.error(err);
            alert('Error de red al intentar exportar.');
            text.innerText = originalText;
            btn.disabled = false;
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const rawContent = @json($lessonPlan->content);
        const container = document.getElementById('markdown-rendered-content');
        container.innerHTML = marked.parse(rawContent);

        // Check for export flag in URL
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('export')) {
            const cleanUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
            window.history.replaceState({path: cleanUrl}, '', cleanUrl);
            exportToGoogleDocs();
        }
    });
</script>
@endsection
