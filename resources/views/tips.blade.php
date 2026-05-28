@extends('layouts.app')

@section('header-title', 'EdTech Prompt Tips')
@section('header-subtitle', 'Interactive library of educational prompts and prompt engineering techniques ready to copy and use in your daily role.')

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
                Prompt Library
            </h3>
            <p class="text-gray-500 mt-1">Explore structures and templates optimized to get the best results with AI assistants.</p>
        </div>

        <div class="flex items-center gap-3 flex-wrap md:flex-nowrap">
            <!-- Búsqueda en tiempo real (Punto 3 - Part A) -->
            <div class="relative w-64">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </span>
                <input type="text" id="prompt-search-input" oninput="filterPrompts()" placeholder="Search prompts..." 
                       class="w-full pl-9 pr-3 py-2 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green transition-all placeholder:text-gray-400 text-xs shadow-sm">
            </div>

            <!-- Filtros por Rol -->
            <div class="flex bg-gray-100 p-1.5 rounded-2xl border border-gray-200/50 shadow-inner">
                <button onclick="switchTab('docentes')" id="tab-docentes" class="tab-btn @if($tab === 'docentes') active @else text-gray-600 hover:text-gray-900 @endif px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-1.5">
                    <span class="mr-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path></svg></span> Teachers
                </button>
                <button onclick="switchTab('estudiantes')" id="tab-estudiantes" class="tab-btn @if($tab === 'estudiantes') active @else text-gray-600 hover:text-gray-900 @endif px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-1.5">
                    <span class="mr-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg></span> Students
                </button>
                <button onclick="switchTab('comunidad')" id="tab-comunidad" class="tab-btn @if($tab === 'comunidad') active @else text-gray-600 hover:text-gray-900 @endif px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-1.5">
                    <span class="mr-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg></span> Community
                </button>
            </div>

            <!-- Compartir Prompt Button -->
            @auth
                <button onclick="openSubmitModal()" class="px-5 py-2.5 bg-gradient-to-r from-ans-dark-green to-ans-light-green text-white text-xs font-bold rounded-2xl hover:shadow-md hover:-translate-y-0.5 transition-all flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Share
                </button>
            @endauth
        </div>
    </div>

    @php
        $colors = ['border-ans-dark-green', 'border-ans-orange', 'border-blue-500', 'border-purple-500'];
        
        // Filter subsets (already filtered by controller, so just group)
        $docentesPrompts = $tab === 'docentes' ? $prompts->groupBy('category') : collect();
        $estudiantesPrompts = $tab === 'estudiantes' ? $prompts->groupBy('category') : collect();

        // Mapeo de categorías a Inglés
        $catMap = [
            'Evaluación' => 'Evaluation',
            'Planificación' => 'Planning',
            'Administración' => 'Administration',
            'Retroalimentación' => 'Feedback',
            'Investigación' => 'Research',
            'Diseño de Actividades' => 'Activity Design',
            'Desarrollo Profesional' => 'Professional Development'
        ];
    @endphp

    <!-- 2. Contenedor de Vista: Para Docentes -->
    @if($tab === 'docentes')
    <div id="view-docentes" class="space-y-8">
        @php $categoryIndex = 0; @endphp
        @forelse($docentesPrompts as $category => $categoryPrompts)
            @php 
                $borderColor = $colors[$categoryIndex % count($colors)];
                $categoryIndex++;
                $displayCategory = isset($catMap[$category]) ? $catMap[$category] : $category;
            @endphp
            <!-- Categoría -->
            <div class="prompt-category-group space-y-6">
                <div class="flex items-center gap-2 border-l-4 {{ $borderColor }} pl-3">
                    <h4 class="font-heading font-extrabold text-gray-800 text-lg">{{ $displayCategory }}</h4>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($categoryPrompts as $prompt)
                        @include('partials.prompt_card', ['prompt' => $prompt])
                    @endforeach
                </div>
            </div>
        @empty
            <div class="py-12 bg-white rounded-3xl border border-gray-200/50 text-center text-gray-500 shadow-sm">
                There are no official prompts for teachers at this time.
            </div>
        @endforelse

        <div class="mt-8 flex justify-center">
            {{ $prompts->links() }}
        </div>
    </div>
    @endif

    <!-- 3. Contenedor de Vista: Para Estudiantes -->
    @if($tab === 'estudiantes')
    <div id="view-estudiantes" class="space-y-8">
        @php $categoryIndex = 0; @endphp
        @forelse($estudiantesPrompts as $category => $categoryPrompts)
            @php 
                $borderColor = $colors[$categoryIndex % count($colors)];
                $categoryIndex++;
                $displayCategory = isset($catMap[$category]) ? $catMap[$category] : $category;
            @endphp
            <!-- Categoría -->
            <div class="prompt-category-group space-y-6">
                <div class="flex items-center gap-2 border-l-4 {{ $borderColor }} pl-3">
                    <h4 class="font-heading font-extrabold text-gray-800 text-lg">{{ $displayCategory }}</h4>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($categoryPrompts as $prompt)
                        @include('partials.prompt_card', ['prompt' => $prompt])
                    @endforeach
                </div>
            </div>
        @empty
            <div class="py-12 bg-white rounded-3xl border border-gray-200/50 text-center text-gray-500 shadow-sm">
                There are no official prompts for students at this time.
            </div>
        @endforelse

        <div class="mt-8 flex justify-center">
            {{ $prompts->links() }}
        </div>
    </div>
    @endif

    <!-- 4. Contenedor de Vista: Comunidad -->
    @if($tab === 'comunidad')
    <div id="view-comunidad" class="space-y-8">
        <div class="prompt-category-group space-y-6">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <h4 class="font-heading font-extrabold text-gray-800 text-lg">Shared by Teachers</h4>
                <p class="text-xs text-gray-500">Prompt engineering shared by ANS faculty and staff.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($prompts as $prompt)
                    @include('partials.prompt_card', ['prompt' => $prompt])
                @empty
                    <div class="col-span-full py-12 bg-white rounded-3xl border border-gray-200/50 text-center text-gray-500 shadow-sm">
                        No community prompts shared yet. Be the first to contribute!
                    </div>
                @endforelse
            </div>
        </div>

        <div class="mt-8 flex justify-center">
            {{ $prompts->links() }}
        </div>
    </div>
    @endif

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
                <h3 class="text-base font-extrabold font-heading">Share an Educational Prompt</h3>
                <button onclick="closeSubmitModal()" class="text-white hover:text-gray-200 focus:outline-none text-xl font-bold">&times;</button>
            </div>

            <form action="{{ route('tips.submit') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Prompt Title</label>
                    <input type="text" name="title" required placeholder="e.g. Science Rubric Generator" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green transition-all placeholder:text-gray-400 text-sm">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Target Role</label>
                        <select name="target_role" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green transition-all text-sm bg-white">
                            <option value="docentes">Teachers / Staff</option>
                            <option value="estudiantes">Students</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Complexity</label>
                        <select name="complexity" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green transition-all text-sm bg-white">
                            <option value="Básico">Basic</option>
                            <option value="Intermedio">Intermediate</option>
                            <option value="Avanzado">Advanced</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Category / Topic</label>
                    <input type="text" name="category" required placeholder="e.g. Evaluation, Planning, STEM" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green transition-all placeholder:text-gray-400 text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Description of use</label>
                    <textarea name="description" required rows="2" placeholder="Briefly explain when to use this prompt and what educational problem it solves." class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green outline-none transition-all resize-none"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Prompt Text (Formula)</label>
                    <textarea name="prompt_text" required rows="4" placeholder="Write the exact prompt formula. Use [brackets] for variables that the teacher must fill out." class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-mono focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green outline-none transition-all resize-none"></textarea>
                </div>

                <div class="pt-2 flex justify-end gap-3">
                    <button type="button" onclick="closeSubmitModal()" class="px-5 py-2.5 border border-gray-200 text-gray-500 text-sm font-semibold rounded-xl hover:bg-gray-50 transition-all">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 bg-ans-dark-green text-white text-sm font-semibold rounded-xl hover:bg-ans-seal-green transition-all">Submit for Moderation</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endauth

<!-- Modal para Personalizar Variables (Punto 3 - Part B) -->
<div id="variables-modal" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm"></div>
        </div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-middle bg-white rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-gray-100">
            <div class="bg-gradient-to-r from-ans-dark-green to-ans-light-green px-6 py-4 flex items-center justify-between text-white">
                <h3 class="text-base font-extrabold font-heading">Customize Prompt</h3>
                <button onclick="closeVariablesModal()" class="text-white hover:text-gray-200 focus:outline-none text-xl font-bold">&times;</button>
            </div>

            <div class="p-6 space-y-4">
                <p class="text-xs text-gray-500">Fill out the fields to adapt the prompt automatically before copying it.</p>
                
                <div id="variables-fields-container" class="space-y-4 max-h-60 overflow-y-auto py-1">
                    <!-- Dynamic fields will be loaded here -->
                </div>

                <div class="pt-2 flex justify-end gap-3">
                    <button type="button" onclick="closeVariablesModal()" class="px-5 py-2.5 border border-gray-200 text-gray-500 text-sm font-semibold rounded-xl hover:bg-gray-50 transition-all">Cancel</button>
                    <button type="button" onclick="confirmCopyVariables()" class="px-5 py-2.5 bg-ans-dark-green text-white text-sm font-semibold rounded-xl hover:bg-ans-seal-green transition-all flex items-center gap-1.5 shadow-md shadow-ans-dark-green/10">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Copy Prompt
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 6. Global Toast de Copiado Exitoso -->
<div id="copy-toast" class="hidden fixed bottom-6 right-6 z-50 bg-gray-900/95 backdrop-blur-md text-white px-5 py-3.5 rounded-2xl flex items-center gap-3 shadow-xl border border-white/10 animate-slide-in-up">
    <div class="w-6 h-6 rounded-full bg-emerald-500 flex items-center justify-center text-white text-xs font-bold shadow-inner">
        ✓
    </div>
    <div class="text-xs">
        <p class="font-bold">Prompt copied to clipboard</p>
        <p class="text-gray-400 mt-0.5">Ready to paste into ChatGPT, Claude, or your preferred AI.</p>
    </div>
</div>

<script>
    // Variable customization state
    let activePromptText = '';
    let activeVariables = [];
    let activeButtonEl = null;
    let activePromptId = null;

    function switchTab(roleKey) {
        window.location.href = "{{ route('tips') }}?tab=" + roleKey;
    }

    function filterPrompts() {
        const query = document.getElementById('prompt-search-input').value.trim().toLowerCase();
        
        // Target active view
        const activeTab = document.querySelector('.tab-btn.active').id; 
        const roleKey = activeTab.replace('tab-', ''); 
        const activeView = document.getElementById('view-' + roleKey);
        
        if (!activeView) return;
        
        const categoryGroups = activeView.querySelectorAll('.prompt-category-group');
        let totalVisibleCards = 0;
        
        categoryGroups.forEach(group => {
            const cards = group.querySelectorAll('.prompt-card');
            let visibleCardsInGroup = 0;
            
            cards.forEach(card => {
                const title = card.querySelector('h5')?.textContent || '';
                const desc = card.querySelector('p.text-gray-600')?.textContent || '';
                const formula = card.querySelector('.font-mono')?.textContent || '';
                const category = card.querySelector('span.tracking-wider')?.textContent || '';
                
                const searchableText = `${title} ${desc} ${formula} ${category}`.toLowerCase();
                
                if (searchableText.includes(query)) {
                    card.classList.remove('hidden');
                    visibleCardsInGroup++;
                    totalVisibleCards++;
                } else {
                    card.classList.add('hidden');
                }
            });
            
            // Hide category group if no cards are visible
            if (visibleCardsInGroup === 0 && query !== '') {
                group.classList.add('hidden');
            } else {
                group.classList.remove('hidden');
            }
        });
        
        // Show/hide empty state message
        let emptyState = activeView.querySelector('.no-search-results-message');
        if (totalVisibleCards === 0) {
            if (!emptyState) {
                emptyState = document.createElement('div');
                emptyState.className = 'no-search-results-message py-12 bg-white rounded-3xl border border-gray-200/50 text-center text-gray-500 shadow-sm font-semibold w-full col-span-full';
                emptyState.innerHTML = 'No prompts found matching your search.';
                activeView.appendChild(emptyState);
            }
            emptyState.classList.remove('hidden');
        } else {
            if (emptyState) {
                emptyState.classList.add('hidden');
            }
        }
    }

    function extractVariables(text) {
        const regex = /\[([^\]]+)\]/g;
        const matches = [];
        let match;
        while ((match = regex.exec(text)) !== null) {
            const varName = match[1];
            if (!matches.includes(varName)) {
                matches.push(varName);
            }
        }
        return matches;
    }

    function copyPrompt(buttonEl, promptId) {
        const textToCopy = buttonEl.getAttribute('data-prompt-text');
        const variables = extractVariables(textToCopy);
        
        if (variables.length > 0) {
            openVariablesModal(textToCopy, variables, buttonEl, promptId);
        } else {
            performCopy(textToCopy, buttonEl, promptId);
        }
    }

    function openVariablesModal(promptText, variables, buttonEl, promptId) {
        activePromptText = promptText;
        activeVariables = variables;
        activeButtonEl = buttonEl;
        activePromptId = promptId;
        
        const container = document.getElementById('variables-fields-container');
        container.innerHTML = '';
        
        variables.forEach((variable, index) => {
            const div = document.createElement('div');
            div.className = 'space-y-1.5';
            const labelText = variable.charAt(0).toUpperCase() + variable.slice(1);
            
            div.innerHTML = `
                <label for="var-input-${index}" class="block text-xs font-bold text-gray-500 uppercase tracking-wider">${labelText}</label>
                <input type="text" id="var-input-${index}" data-var-name="${variable}" placeholder="Enter ${variable.toLowerCase()}..." 
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green transition-all placeholder:text-gray-400 text-sm">
            `;
            container.appendChild(div);
        });
        
        document.getElementById('variables-modal').classList.remove('hidden');
        
        setTimeout(() => {
            const firstInput = document.getElementById('var-input-0');
            if (firstInput) firstInput.focus();
        }, 100);
    }
    
    function closeVariablesModal() {
        document.getElementById('variables-modal').classList.add('hidden');
    }
    
    function confirmCopyVariables() {
        let customizedText = activePromptText;
        
        activeVariables.forEach((variable, index) => {
            const input = document.getElementById(`var-input-${index}`);
            const value = input ? input.value.trim() : '';
            
            if (value) {
                const escapedVar = variable.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
                const regex = new RegExp('\\[' + escapedVar + '\\]', 'g');
                customizedText = customizedText.replace(regex, value);
            }
        });
        
        performCopy(customizedText, activeButtonEl, activePromptId);
        closeVariablesModal();
    }

    function performCopy(textToCopy, buttonEl, promptId) {
        navigator.clipboard.writeText(textToCopy).then(() => {
            // Mostrar Toast Global
            const toast = document.getElementById('copy-toast');
            if (toast) toast.classList.remove('hidden');

            // Actualizar texto del botón a Copiado de forma temporal
            if (buttonEl) {
                const originalHTML = buttonEl.innerHTML;
                buttonEl.innerHTML = `
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>Copied!</span>
                `;
                buttonEl.classList.remove('bg-ans-dark-green', 'hover:bg-ans-seal-green');
                buttonEl.classList.add('bg-emerald-600', 'hover:bg-emerald-700');

                // Ocultar elementos tras 2.5 segundos
                setTimeout(() => {
                    if (toast) toast.classList.add('hidden');
                    buttonEl.innerHTML = originalHTML;
                    buttonEl.classList.remove('bg-emerald-600', 'hover:bg-emerald-700');
                    buttonEl.classList.add('bg-ans-dark-green', 'hover:bg-ans-seal-green');
                }, 2500);
            } else {
                setTimeout(() => {
                    if (toast) toast.classList.add('hidden');
                }, 2500);
            }

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
                    if (counter) counter.textContent = data.usage_count + ' copies';
                }
            })
            .catch(err => console.error('[AINS] Increment count error:', err));
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
                    countEl.textContent = `Comments (${currentCount + 1})`;
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
