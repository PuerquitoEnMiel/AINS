@extends('layouts.app')

@section('header-title', 'EdTech Prompt Tips')
@section('header-subtitle', 'Biblioteca interactiva de prompts educativos y técnicas de ingeniería de prompts listas para copiar y usar en tu rol diario.')

@section('content')

<style>
    /* Efecto de borde dinámico y escala en tarjetas de prompts */
    .prompt-card {
        transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        border: 1px solid rgba(229, 231, 235, 0.6);
    }
    .prompt-card:hover {
        transform: translateY(-5px);
        border-color: rgba(0, 105, 55, 0.25);
        box-shadow: 0 16px 28px -10px rgba(0, 105, 55, 0.08);
    }
    
    /* Pestañas de Roles */
    .tab-btn {
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .tab-btn.active {
        background-color: #006937;
        color: white;
        box-shadow: 0 8px 16px -6px rgba(0, 105, 55, 0.3);
    }

    /* Animación del Toast de Copiado */
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(20px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
    .animate-slide-in-up {
        animation: slideInUp 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
    }
</style>

<div class="space-y-8 pb-12 animate-fade-in-up">
    
    <!-- 1. Controles de Filtrado e Introducción -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-gray-200 pb-6">
        <div>
            <h3 class="text-2xl font-heading font-extrabold text-gray-800 tracking-tight">
                Biblioteca de Prompts
            </h3>
            <p class="text-gray-500 mt-1">Explora estructuras y plantillas optimizadas para obtener los mejores resultados con asistentes de IA.</p>
        </div>

        <div class="flex items-center gap-3 flex-wrap md:flex-nowrap">
            <!-- Filtros por Rol -->
            <div class="flex bg-gray-100 p-1.5 rounded-2xl border border-gray-200/50 shadow-inner">
                <button onclick="switchTab('docentes')" id="tab-docentes" class="tab-btn active px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-1.5">
                    <span>👨‍🏫</span> Docentes
                </button>
                <button onclick="switchTab('estudiantes')" id="tab-estudiantes" class="tab-btn px-4 py-2 rounded-xl text-xs font-bold text-gray-600 hover:text-gray-900 flex items-center gap-1.5">
                    <span>🎓</span> Estudiantes
                </button>
                <button onclick="switchTab('comunidad')" id="tab-comunidad" class="tab-btn px-4 py-2 rounded-xl text-xs font-bold text-gray-600 hover:text-gray-900 flex items-center gap-1.5">
                    <span>🌐</span> Comunidad
                </button>
            </div>

            <!-- Compartir Prompt Button -->
            @auth
                <button onclick="openSubmitModal()" class="px-5 py-2.5 bg-gradient-to-r from-ans-dark-green to-ans-light-green text-white text-xs font-bold rounded-2xl hover:shadow-md hover:-translate-y-0.5 transition-all">
                    ➕ Compartir Prompt
                </button>
            @endauth
        </div>
    </div>

    @php
        $colors = ['border-ans-dark-green', 'border-ans-orange', 'border-blue-500', 'border-purple-500'];
        
        // Filter subsets
        $docentesPrompts = $prompts->where('target_role', 'docentes')->where('is_community', false)->groupBy('category');
        $estudiantesPrompts = $prompts->where('target_role', 'estudiantes')->where('is_community', false)->groupBy('category');
        $comunidadPrompts = $prompts->where('is_community', true);
    @endphp

    <!-- 2. Contenedor de Vista: Para Docentes -->
    <div id="view-docentes" class="space-y-8">
        @php $categoryIndex = 0; @endphp
        @forelse($docentesPrompts as $category => $categoryPrompts)
            @php 
                $borderColor = $colors[$categoryIndex % count($colors)];
                $categoryIndex++;
            @endphp
            <!-- Categoría -->
            <div class="space-y-6">
                <div class="flex items-center gap-2 border-l-4 {{ $borderColor }} pl-3">
                    <h4 class="font-heading font-extrabold text-gray-800 text-lg">{{ $category }}</h4>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($categoryPrompts as $prompt)
                        @include('partials.prompt_card', ['prompt' => $prompt])
                    @endforeach
                </div>
            </div>
        @empty
            <div class="py-12 bg-white rounded-3xl border border-gray-200/50 text-center text-gray-500 shadow-sm">
                No hay prompts oficiales para docentes en este momento.
            </div>
        @endforelse
    </div>

    <!-- 3. Contenedor de Vista: Para Estudiantes -->
    <div id="view-estudiantes" class="space-y-8 hidden">
        @php $categoryIndex = 0; @endphp
        @forelse($estudiantesPrompts as $category => $categoryPrompts)
            @php 
                $borderColor = $colors[$categoryIndex % count($colors)];
                $categoryIndex++;
            @endphp
            <!-- Categoría -->
            <div class="space-y-6">
                <div class="flex items-center gap-2 border-l-4 {{ $borderColor }} pl-3">
                    <h4 class="font-heading font-extrabold text-gray-800 text-lg">{{ $category }}</h4>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($categoryPrompts as $prompt)
                        @include('partials.prompt_card', ['prompt' => $prompt])
                    @endforeach
                </div>
            </div>
        @empty
            <div class="py-12 bg-white rounded-3xl border border-gray-200/50 text-center text-gray-500 shadow-sm">
                No hay prompts oficiales para estudiantes en este momento.
            </div>
        @endforelse
    </div>

    <!-- 4. Contenedor de Vista: Comunidad -->
    <div id="view-comunidad" class="space-y-8 hidden">
        <div class="space-y-6">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <h4 class="font-heading font-extrabold text-gray-800 text-lg">💡 Compartidos por Docentes</h4>
                <p class="text-xs text-gray-500">Prompts de ingeniería compartidos por la facultad y el staff de ANS.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($comunidadPrompts as $prompt)
                    @include('partials.prompt_card', ['prompt' => $prompt])
                @empty
                    <div class="col-span-full py-12 bg-white rounded-3xl border border-gray-200/50 text-center text-gray-500 shadow-sm">
                        No hay prompts compartidos por la comunidad todavía. ¡Sé el primero en aportar!
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>

<!-- 5. Modal de Envío de Prompt -->
@auth
<div id="submit-modal" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm"></div>
        </div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-middle bg-white rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-gray-100">
            <div class="bg-gradient-to-r from-ans-dark-green to-ans-light-green px-6 py-4 flex items-center justify-between text-white">
                <h3 class="text-base font-extrabold font-heading">💡 Compartir un Prompt Educativo</h3>
                <button onclick="closeSubmitModal()" class="text-white hover:text-gray-200 focus:outline-none text-xl font-bold">&times;</button>
            </div>

            <form action="{{ route('tips.submit') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Título del Prompt</label>
                    <input type="text" name="title" required placeholder="Ej. Generador de Rúbricas de Ciencias" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green transition-all placeholder:text-gray-400 text-sm">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Dirigido a</label>
                        <select name="target_role" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green transition-all text-sm bg-white">
                            <option value="docentes">Docentes / Staff</option>
                            <option value="estudiantes">Estudiantes</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Complejidad</label>
                        <select name="complexity" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green transition-all text-sm bg-white">
                            <option value="Básico">Básico</option>
                            <option value="Intermedio">Intermedio</option>
                            <option value="Avanzado">Avanzado</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Categoría / Tema</label>
                    <input type="text" name="category" required placeholder="Ej. Evaluación, Planificación, STEM" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green transition-all placeholder:text-gray-400 text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Descripción de uso</label>
                    <textarea name="description" required rows="2" placeholder="Explica brevemente cuándo usar este prompt y qué problema educativo resuelve." class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green outline-none transition-all resize-none"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Texto del Prompt (Fórmula)</label>
                    <textarea name="prompt_text" required rows="4" placeholder="Escribe la fórmula exacta del prompt. Usa [corchetes] para las variables que el docente deba rellenar." class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-mono focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green outline-none transition-all resize-none"></textarea>
                </div>

                <div class="pt-2 flex justify-end gap-3">
                    <button type="button" onclick="closeSubmitModal()" class="px-5 py-2.5 border border-gray-200 text-gray-500 text-sm font-semibold rounded-xl hover:bg-gray-50 transition-all">Cancelar</button>
                    <button type="submit" class="px-5 py-2.5 bg-ans-dark-green text-white text-sm font-semibold rounded-xl hover:bg-ans-seal-green transition-all">Enviar a Moderación</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endauth

<!-- 6. Global Toast de Copiado Exitoso -->
<div id="copy-toast" class="hidden fixed bottom-6 right-6 z-50 bg-gray-900/95 backdrop-blur-md text-white px-5 py-3.5 rounded-2xl flex items-center gap-3 shadow-xl border border-white/10 animate-slide-in-up">
    <div class="w-6 h-6 rounded-full bg-emerald-500 flex items-center justify-center text-white text-xs font-bold shadow-inner">
        ✓
    </div>
    <div class="text-xs">
        <p class="font-bold">Prompt copiado al portapapeles</p>
        <p class="text-gray-400 mt-0.5">Listo para pegar en ChatGPT, Claude o tu IA preferida.</p>
    </div>
</div>

<script>
    function switchTab(roleKey) {
        // Desactivar botones de pestañas
        document.getElementById('tab-docentes').className = "tab-btn px-4 py-2 rounded-xl text-xs font-bold text-gray-600 hover:text-gray-900 flex items-center gap-1.5";
        document.getElementById('tab-estudiantes').className = "tab-btn px-4 py-2 rounded-xl text-xs font-bold text-gray-600 hover:text-gray-900 flex items-center gap-1.5";
        document.getElementById('tab-comunidad').className = "tab-btn px-4 py-2 rounded-xl text-xs font-bold text-gray-600 hover:text-gray-900 flex items-center gap-1.5";
        
        // Activar el correspondiente
        document.getElementById('tab-' + roleKey).className = "tab-btn active px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-1.5";

        // Ocultar todas las vistas
        document.getElementById('view-docentes').classList.add('hidden');
        document.getElementById('view-estudiantes').classList.add('hidden');
        document.getElementById('view-comunidad').classList.add('hidden');

        // Mostrar la vista activa
        document.getElementById('view-' + roleKey).classList.remove('hidden');
    }

    function copyPrompt(buttonEl, promptId) {
        const textToCopy = buttonEl.getAttribute('data-prompt-text');
        navigator.clipboard.writeText(textToCopy).then(() => {
            // Mostrar Toast Global
            const toast = document.getElementById('copy-toast');
            toast.classList.remove('hidden');

            // Actualizar texto del botón a Copiado de forma temporal
            const originalHTML = buttonEl.innerHTML;
            buttonEl.innerHTML = `
                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>¡Copiado!</span>
            `;
            buttonEl.classList.remove('bg-ans-dark-green', 'hover:bg-ans-seal-green');
            buttonEl.classList.add('bg-emerald-600', 'hover:bg-emerald-700');

            // Increment usage/copy count on server
            fetch(`/tips/${promptId}/copy`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const counter = document.getElementById(`copy-count-${promptId}`);
                    if (counter) counter.textContent = data.usage_count + ' copias';
                }
            });

            // Ocultar elementos tras 2.5 segundos
            setTimeout(() => {
                toast.classList.add('hidden');
                buttonEl.innerHTML = originalHTML;
                buttonEl.classList.remove('bg-emerald-600', 'hover:bg-emerald-700');
                buttonEl.classList.add('bg-ans-dark-green', 'hover:bg-ans-seal-green');
            }, 2500);
        }).catch(err => {
            console.error('Error al copiar al portapapeles: ', err);
        });
    }

    function toggleComments(promptId) {
        const commentBox = document.getElementById(`comments-${promptId}`);
        commentBox.classList.toggle('hidden');
    }

    function handleVote(promptId, voteType) {
        @guest
            window.location.href = "{{ route('login') }}";
            return;
        @endguest

        fetch(`/tips/${promptId}/vote`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ type: voteType })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const scoreBadge = document.getElementById(`score-${promptId}`);
                scoreBadge.textContent = data.score;

                const upBtn = document.getElementById(`upvote-btn-${promptId}`);
                const downBtn = document.getElementById(`downvote-btn-${promptId}`);

                upBtn.classList.remove('text-emerald-600', 'bg-emerald-50');
                downBtn.classList.remove('text-red-600', 'bg-red-50');

                if (data.voted === 'upvote') {
                    upBtn.classList.add('text-emerald-600', 'bg-emerald-50');
                } else if (data.voted === 'downvote') {
                    downBtn.classList.add('text-red-600', 'bg-red-50');
                }
            }
        });
    }

    function submitComment(event, promptId) {
        event.preventDefault();
        const input = document.getElementById(`comment-input-${promptId}`);
        const body = input.value.trim();

        if (!body) return;

        fetch(`/tips/${promptId}/comment`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ body: body })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                input.value = '';
                const list = document.getElementById(`comment-list-${promptId}`);
                
                const newCommentHtml = `
                    <div class="p-3 bg-gray-50 rounded-xl flex items-start gap-3 border border-gray-100/50 animate-slide-in-up">
                        <div class="w-7 h-7 rounded-full bg-ans-dark-green text-white flex items-center justify-center text-xs font-bold">
                            ${data.comment.user_name.substring(0, 1)}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <p class="text-xs font-bold text-gray-800">${data.comment.user_name}</p>
                                <span class="text-[9px] text-gray-400">${data.comment.created_at}</span>
                            </div>
                            <p class="text-xs text-gray-600 mt-1">${data.comment.body}</p>
                        </div>
                    </div>
                `;
                
                list.insertAdjacentHTML('afterbegin', newCommentHtml);

                const countEl = document.getElementById(`comment-count-badge-${promptId}`);
                if (countEl) {
                    const currentCount = parseInt(countEl.textContent.match(/\d+/)[0]);
                    countEl.textContent = `💬 Comentarios (${currentCount + 1})`;
                }
            }
        });
    }

    function openSubmitModal() {
        document.getElementById('submit-modal').classList.remove('hidden');
    }

    function closeSubmitModal() {
        document.getElementById('submit-modal').classList.add('hidden');
    }
</script>

@endsection
