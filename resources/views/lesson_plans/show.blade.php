@extends('layouts.app')

@section('header-title', 'AI Lesson Planner')
@section('header-subtitle', 'View and export your pedagogical lesson plan')

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

        <div class="flex items-center gap-2.5 flex-wrap">
            <button onclick="exportToGoogleDocs()" id="export-btn" class="px-5 py-2.5 bg-ans-dark-green hover:bg-[#0f483c] text-white text-xs font-bold rounded-xl shadow-md shadow-ans-dark-green/20 transition-all hover:-translate-y-0.5 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span id="export-text">Google Docs</span>
            </button>
            <button onclick="exportToGoogleSlides()" id="slides-btn" class="px-5 py-2.5 bg-blue-600 hover:bg-[#0f4080] text-white text-xs font-bold rounded-xl shadow-md shadow-blue-600/20 transition-all hover:-translate-y-0.5 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                <span id="slides-text">Google Slides</span>
            </button>
            <button onclick="openClassroomModal()" class="px-5 py-2.5 bg-[#007934] hover:bg-[#005e28] text-white text-xs font-bold rounded-xl shadow-md shadow-green-700/20 transition-all hover:-translate-y-0.5 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path></svg>
                Classroom
            </button>
            <button onclick="openCalendarModal()" class="px-5 py-2.5 bg-ans-orange hover:bg-[#e67600] text-white text-xs font-bold rounded-xl shadow-md shadow-ans-orange/20 transition-all hover:-translate-y-0.5 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Schedule Class
            </button>
            <button onclick="window.print()" class="px-5 py-2.5 bg-gray-600 hover:bg-gray-700 text-white text-xs font-bold rounded-xl shadow-sm transition-all hover:-translate-y-0.5 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Print / PDF
            </button>
            <form action="{{ route('lesson-plans.destroy', $lessonPlan) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this plan?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-5 py-2.5 border border-red-200 text-red-600 hover:bg-red-50 text-xs font-bold rounded-xl transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Delete
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
                        <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Duration</span>
                        <span class="font-bold text-gray-700 mt-0.5 block">{{ $lessonPlan->duration ?? 'Not specified' }}</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Created at</span>
                        <span class="font-bold text-gray-700 mt-0.5 block">{{ $lessonPlan->created_at->format('d/m/Y') }}</span>
                    </div>
                    <div class="col-span-2 md:col-span-1">
                        <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider">Created by</span>
                        <span class="font-bold text-gray-700 mt-0.5 block">{{ $lessonPlan->user->name }}</span>
                    </div>
                </div>
            </div>

            <!-- Markdown Rendered Content -->
            <article id="markdown-rendered-content" class="prose max-w-none text-gray-700 text-sm leading-relaxed prose-headings:text-gray-800 prose-headings:font-heading prose-strong:text-ans-dark-green prose-code:bg-gray-50 prose-code:px-2 prose-code:py-1 prose-code:rounded prose-code:border prose-code:border-gray-100 prose-a:text-ans-orange prose-a:underline"></article>

            @if(auth()->check() && $lessonPlan->user_id === auth()->id())
                <!-- AI Refinement Panel (Punto 2 - Part B) -->
                <div id="ai-refine-box" class="mt-8 border-t border-gray-100 pt-6 space-y-4 print:hidden">
                    <div class="flex items-center gap-2">
                        <span class="p-1.5 rounded-lg bg-ans-dark-green text-white">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                            </svg>
                        </span>
                        <div>
                            <h4 class="text-sm font-bold text-gray-800 font-heading">Refine Lesson Plan with AI</h4>
                            <p class="text-xs text-gray-500">Do you want to adjust the generated plan? Ask the AI to make specific changes.</p>
                        </div>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="flex-1">
                            <textarea id="refine-instructions" rows="2" placeholder="e.g. Add a key vocabulary section at the end or reduce duration to 45 minutes."
                                      class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green transition-all placeholder:text-gray-400 text-sm leading-relaxed"></textarea>
                            <p id="refine-error" class="hidden mt-1 text-xs font-semibold text-red-500">⚠️ Please enter the refinement instructions.</p>
                        </div>
                        <button id="btn-refine" type="button" onclick="refinePlan()" class="self-end sm:self-center px-6 py-3 bg-ans-dark-green hover:bg-ans-seal-green text-white font-bold rounded-xl shadow-md shadow-ans-dark-green/10 transition-all flex items-center justify-center gap-2 text-sm whitespace-nowrap">
                            <span id="btn-refine-text">Refine Plan</span>
                            <svg id="btn-refine-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            <svg id="btn-refine-spinner" class="w-4 h-4 animate-spin hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- AI Refined Preview Block (Punto 2 - Part B) -->
                <div id="ai-preview-box" class="hidden mt-6 border-2 border-dashed border-ans-dark-green/20 bg-ans-dark-green/[0.01] rounded-2xl p-6 space-y-4 print:hidden">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-ans-orange animate-pulse"></span>
                            <h4 class="text-sm font-bold text-gray-800 font-heading">Refined Plan Preview</h4>
                        </div>
                        <span class="text-xs text-ans-dark-green font-semibold">AI proposed changes</span>
                    </div>
                    
                    <article id="markdown-refined-preview" class="prose max-w-none text-gray-700 text-sm leading-relaxed prose-headings:text-gray-800 prose-headings:font-heading prose-strong:text-ans-dark-green prose-code:bg-gray-50 prose-code:px-2 prose-code:py-1 prose-code:rounded prose-code:border prose-code:border-gray-100 prose-a:text-ans-orange prose-a:underline bg-white p-4 rounded-xl border border-gray-100 max-h-96 overflow-y-auto"></article>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" onclick="cancelRefinement()" class="px-5 py-2.5 border border-gray-200 text-gray-500 font-bold rounded-xl hover:bg-gray-50 transition-all text-xs">
                            Discard
                        </button>
                        <button type="button" onclick="saveRefinement()" id="btn-save-refine" class="px-5 py-2.5 bg-ans-dark-green hover:bg-ans-seal-green text-white font-bold rounded-xl shadow-md shadow-ans-dark-green/20 transition-all hover:-translate-y-0.5 flex items-center justify-center gap-2 text-xs">
                            <span id="btn-save-refine-text">Apply & Save Changes</span>
                            <svg id="btn-save-refine-spinner" class="w-4 h-4 animate-spin hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            @endif
        </div>

        <!-- Right: Connected AINS Tools (Sidebar) -->
        <div id="tools-sidebar-column" class="space-y-6">
            <div class="bg-white border border-gray-100 rounded-3xl p-6 shadow-sm">
                <h4 class="font-extrabold text-gray-800 text-sm mb-4 uppercase tracking-wider">Associated Applications</h4>
                
                @if($tools->isEmpty())
                    <p class="text-xs text-gray-400 italic">No catalog tools were associated with this plan.</p>
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
                                    View Details →
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
                    Export Tip
                </h5>
                <p class="text-[11px] text-gray-600 leading-relaxed">
                    Press the **Print / PDF** button to open the browser print window. Choose the **Save as PDF** option in your browser to archive or send your lesson plan digitally.
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
        text.innerText = 'Exporting...';

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
                text.innerText = 'Exported!';
                setTimeout(() => {
                    text.innerText = originalText;
                    btn.disabled = false;
                }, 3000);
            } else {
                alert(data.error || 'An error occurred while exporting.');
                text.innerText = originalText;
                btn.disabled = false;
            }
        } catch (err) {
            console.error(err);
            alert('Network error while exporting.');
            text.innerText = originalText;
            btn.disabled = false;
        }
    }

    let currentMarkdown = @json($lessonPlan->content);
    let refinedMarkdown = '';

    function refinePlan() {
        try {
            const instructionsEl = document.getElementById('refine-instructions');
            const refineError = document.getElementById('refine-error');
            const btnRefine = document.getElementById('btn-refine');
            const btnText = document.getElementById('btn-refine-text');
            const btnIcon = document.getElementById('btn-refine-icon');
            const btnSpinner = document.getElementById('btn-refine-spinner');
            
            if (!instructionsEl || !instructionsEl.value.trim()) {
                if (refineError) refineError.classList.remove('hidden');
                if (instructionsEl) instructionsEl.focus();
                return;
            }
            if (refineError) refineError.classList.add('hidden');
            
            const instructionsVal = instructionsEl.value.trim();
            
            // Disable controls & show loader
            btnRefine.disabled = true;
            instructionsEl.disabled = true;
            btnRefine.classList.add('opacity-75', 'cursor-not-allowed');
            if (btnText) btnText.textContent = 'Refining...';
            if (btnIcon) btnIcon.classList.add('hidden');
            if (btnSpinner) btnSpinner.classList.remove('hidden');
            
            fetch('{{ route("lesson-plans.refine") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    content: currentMarkdown,
                    instructions: instructionsVal
                })
            })
            .then(res => {
                if (!res.ok) {
                    return res.json().then(data => { throw new Error(data.error || 'Server error ' + res.status) });
                }
                return res.json();
            })
            .then(data => {
                refinedMarkdown = data.markdown;
                
                // Render preview
                const previewContainer = document.getElementById('markdown-refined-preview');
                if (previewContainer) {
                    previewContainer.innerHTML = marked.parse(refinedMarkdown);
                }
                
                // Show preview box
                const previewBox = document.getElementById('ai-preview-box');
                if (previewBox) {
                    previewBox.classList.remove('hidden');
                    previewBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
                
                instructionsEl.value = '';
                
                // Re-enable controls
                btnRefine.disabled = false;
                instructionsEl.disabled = false;
                btnRefine.classList.remove('opacity-75', 'cursor-not-allowed');
                if (btnText) btnText.textContent = 'Refine Plan';
                if (btnIcon) btnIcon.classList.remove('hidden');
                if (btnSpinner) btnSpinner.classList.add('hidden');
            })
            .catch(err => {
                console.error('[AINS] Refine error:', err);
                alert('Error refining lesson plan: ' + err.message);
                
                // Re-enable controls
                btnRefine.disabled = false;
                instructionsEl.disabled = false;
                btnRefine.classList.remove('opacity-75', 'cursor-not-allowed');
                if (btnText) btnText.textContent = 'Refine Plan';
                if (btnIcon) btnIcon.classList.remove('hidden');
                if (btnSpinner) btnSpinner.classList.add('hidden');
            });
        } catch (e) {
            console.error(e);
        }
    }

    function cancelRefinement() {
        refinedMarkdown = '';
        const previewBox = document.getElementById('ai-preview-box');
        if (previewBox) {
            previewBox.classList.add('hidden');
        }
    }

    function saveRefinement() {
        try {
            const btnSave = document.getElementById('btn-save-refine');
            const btnText = document.getElementById('btn-save-refine-text');
            const btnSpinner = document.getElementById('btn-save-refine-spinner');
            
            if (!refinedMarkdown) return;
            
            btnSave.disabled = true;
            btnSave.classList.add('opacity-75', 'cursor-not-allowed');
            if (btnText) btnText.textContent = 'Saving...';
            if (btnSpinner) btnSpinner.classList.remove('hidden');
            
            fetch('{{ route("lesson-plans.update", $lessonPlan) }}', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    content: refinedMarkdown
                })
            })
            .then(res => {
                if (!res.ok) {
                    return res.json().then(data => { throw new Error(data.error || 'Server error') });
                }
                return res.json();
            })
            .then(data => {
                currentMarkdown = refinedMarkdown;
                refinedMarkdown = '';
                
                // Render main content with updated markdown
                const container = document.getElementById('markdown-rendered-content');
                container.innerHTML = marked.parse(currentMarkdown);
                
                // Hide preview box
                const previewBox = document.getElementById('ai-preview-box');
                if (previewBox) {
                    previewBox.classList.add('hidden');
                }
                
                // Reset save button state
                btnSave.disabled = false;
                btnSave.classList.remove('opacity-75', 'cursor-not-allowed');
                if (btnText) btnText.textContent = 'Apply & Save Changes';
                if (btnSpinner) btnSpinner.classList.add('hidden');
                
                alert('Lesson plan updated successfully.');
            })
            .catch(err => {
                console.error('[AINS] Save error:', err);
                alert('Error saving lesson plan: ' + err.message);
                
                // Reset save button state
                btnSave.disabled = false;
                btnSave.classList.remove('opacity-75', 'cursor-not-allowed');
                if (btnText) btnText.textContent = 'Apply & Save Changes';
                if (btnSpinner) btnSpinner.classList.add('hidden');
            });
        } catch (e) {
            console.error(e);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const container = document.getElementById('markdown-rendered-content');
        container.innerHTML = marked.parse(currentMarkdown);

        // Check for export flag in URL
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('export')) {
            const cleanUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
            window.history.replaceState({path: cleanUrl}, '', cleanUrl);
            exportToGoogleDocs();
        }
    });

    // ─── Google Slides ───
    async function exportToGoogleSlides() {
        const btn = document.getElementById('slides-btn');
        const text = document.getElementById('slides-text');
        const originalText = text.innerText;

        btn.disabled = true;
        text.innerText = 'Creating Presentation...';

        try {
            const response = await fetch("{{ route('lesson-plans.slides', $lessonPlan) }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.redirect) {
                window.location.href = data.redirect;
            } else if (data.success && data.url) {
                window.open(data.url, '_blank');
                text.innerText = 'Slides Created!';
                setTimeout(() => {
                    text.innerText = originalText;
                    btn.disabled = false;
                }, 3000);
            } else {
                alert(data.error || 'An error occurred while generating slides.');
                text.innerText = originalText;
                btn.disabled = false;
            }
        } catch (err) {
            console.error(err);
            alert('Network error.');
            text.innerText = originalText;
            btn.disabled = false;
        }
    }

    // ─── Google Classroom Modal Handlers ───
    function openClassroomModal() {
        const modal = document.getElementById('classroom-modal');
        modal.classList.remove('hidden');
        
        // Fetch courses list
        fetch("{{ route('classroom.courses') }}")
            .then(res => {
                if (res.status === 401) {
                    return res.json().then(data => { window.location.href = data.redirect; });
                }
                return res.json();
            })
            .then(data => {
                if (data.success) {
                    const select = document.getElementById('course_select');
                    select.innerHTML = '';
                    data.courses.forEach(c => {
                        const opt = document.createElement('option');
                        opt.value = c.id;
                        opt.textContent = c.name + (c.section ? ' (' + c.section + ')' : '');
                        select.appendChild(opt);
                    });
                    
                    document.getElementById('classroom-loading').classList.add('hidden');
                    document.getElementById('classroom-form').classList.remove('hidden');
                } else {
                    alert(data.error || 'Failed to fetch courses.');
                    closeClassroomModal();
                }
            })
            .catch(err => {
                console.error(err);
                alert('Connection error.');
                closeClassroomModal();
            });
    }

    function closeClassroomModal() {
        document.getElementById('classroom-modal').classList.add('hidden');
        document.getElementById('classroom-loading').classList.remove('hidden');
        document.getElementById('classroom-form').classList.add('hidden');
    }

    function toggleDueDate(select) {
        const dueContainer = document.getElementById('due-date-container');
        if (select.value === 'announcement') {
            dueContainer.classList.add('hidden');
        } else {
            dueContainer.classList.remove('hidden');
        }
    }

    async function submitClassroomShare(e) {
        e.preventDefault();
        const btn = document.getElementById('classroom-submit-btn');
        const originalText = btn.innerText;
        btn.disabled = true;
        btn.innerText = 'Sharing...';

        const payload = {
            course_id: document.getElementById('course_select').value,
            share_type: document.getElementById('share_type').value,
            title: document.getElementById('class_title').value,
            instructions: document.getElementById('class_instructions').value,
            due_date: document.getElementById('class_due_date').value,
        };

        try {
            const response = await fetch("{{ route('lesson-plans.classroom-share', $lessonPlan) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            const data = await response.json();
            if (data.success) {
                alert(data.message);
                closeClassroomModal();
            } else {
                alert(data.error || 'Failed to share plan.');
            }
        } catch (err) {
            console.error(err);
            alert('Network error.');
        } finally {
            btn.disabled = false;
            btn.innerText = originalText;
        }
    }

    // ─── Google Calendar Modal Handlers ───
    function openCalendarModal() {
        document.getElementById('calendar-modal').classList.remove('hidden');
        // Pre-fill tomorrow's date
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        document.getElementById('cal_date').value = tomorrow.toISOString().split('T')[0];
    }

    function closeCalendarModal() {
        document.getElementById('calendar-modal').classList.add('hidden');
    }

    async function submitCalendarSchedule(e) {
        e.preventDefault();
        const btn = document.getElementById('calendar-submit-btn');
        const originalText = btn.innerText;
        btn.disabled = true;
        btn.innerText = 'Scheduling...';

        const payload = {
            date: document.getElementById('cal_date').value,
            time: document.getElementById('cal_time').value,
            duration: document.getElementById('cal_duration').value,
        };

        try {
            const response = await fetch("{{ route('lesson-plans.calendar', $lessonPlan) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            const data = await response.json();
            if (data.success) {
                alert(data.message);
                closeCalendarModal();
            } else {
                alert(data.error || 'Failed to schedule event.');
            }
        } catch (err) {
            console.error(err);
            alert('Network error.');
        } finally {
            btn.disabled = false;
            btn.innerText = originalText;
        }
    }
</script>

<!-- Google Classroom Modal -->
<div id="classroom-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm hidden">
    <div class="bg-white rounded-3xl p-6 md:p-8 max-w-md w-full shadow-2xl border border-gray-100 transform scale-95 transition-all duration-300">
        <div class="flex items-center justify-between mb-6">
            <h3 class="font-extrabold text-gray-800 text-lg flex items-center gap-2">
                <svg class="w-5 h-5 text-[#007934]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path></svg>
                Google Classroom
            </h3>
            <button onclick="closeClassroomModal()" class="text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</button>
        </div>
        
        <div id="classroom-loading" class="py-8 flex flex-col items-center justify-center text-center space-y-3">
            <svg class="w-8 h-8 animate-spin text-[#007934]" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <p class="text-xs text-gray-500 font-medium">Fetching your courses...</p>
        </div>

        <form id="classroom-form" onsubmit="submitClassroomShare(event)" class="space-y-4 hidden">
            <div>
                <label for="course_select" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Select Course</label>
                <select id="course_select" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#007934]/20 focus:border-[#007934] text-sm bg-white"></select>
            </div>
            <div>
                <label for="share_type" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Share as</label>
                <select id="share_type" onchange="toggleDueDate(this)" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#007934]/20 focus:border-[#007934] text-sm bg-white">
                    <option value="assignment">Assignment / CourseWork</option>
                    <option value="announcement">Announcement Post</option>
                </select>
            </div>
            <div>
                <label for="class_title" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Title</label>
                <input type="text" id="class_title" value="{{ $lessonPlan->title }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#007934]/20 focus:border-[#007934] text-sm">
            </div>
            <div>
                <label for="class_instructions" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Instructions (Optional)</label>
                <textarea id="class_instructions" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#007934]/20 focus:border-[#007934] text-sm"></textarea>
            </div>
            <div id="due-date-container">
                <label for="class_due_date" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Due Date (Optional)</label>
                <input type="date" id="class_due_date" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#007934]/20 focus:border-[#007934] text-sm">
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <button type="button" onclick="closeClassroomModal()" class="px-5 py-2.5 border border-gray-200 text-gray-500 font-bold rounded-xl hover:bg-gray-50 text-xs">Cancel</button>
                <button type="submit" id="classroom-submit-btn" class="px-5 py-2.5 bg-[#007934] hover:bg-[#005e28] text-white font-bold rounded-xl text-xs shadow-md shadow-green-700/10">Share to Classroom</button>
            </div>
        </form>
    </div>
</div>

<!-- Google Calendar Modal -->
<div id="calendar-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm hidden">
    <div class="bg-white rounded-3xl p-6 md:p-8 max-w-md w-full shadow-2xl border border-gray-100 transform scale-95 transition-all duration-300">
        <div class="flex items-center justify-between mb-6">
            <h3 class="font-extrabold text-gray-800 text-lg flex items-center gap-2">
                <svg class="w-5 h-5 text-ans-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Schedule Class Session
            </h3>
            <button onclick="closeCalendarModal()" class="text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</button>
        </div>
        
        <form onsubmit="submitCalendarSchedule(event)" class="space-y-4">
            <div>
                <label for="cal_date" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Class Date</label>
                <input type="date" id="cal_date" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-ans-orange/20 focus:border-ans-orange text-sm">
            </div>
            <div>
                <label for="cal_time" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Start Time</label>
                <input type="time" id="cal_time" required value="09:00" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-ans-orange/20 focus:border-ans-orange text-sm">
            </div>
            <div>
                <label for="cal_duration" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Duration (Minutes)</label>
                <input type="number" id="cal_duration" required value="60" min="10" max="480" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-ans-orange/20 focus:border-ans-orange text-sm">
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <button type="button" onclick="closeCalendarModal()" class="px-5 py-2.5 border border-gray-200 text-gray-500 font-bold rounded-xl hover:bg-gray-50 text-xs">Cancel</button>
                <button type="submit" id="calendar-submit-btn" class="px-5 py-2.5 bg-ans-orange hover:bg-[#e67600] text-white font-bold rounded-xl text-xs shadow-md shadow-ans-orange/10">Add to Calendar</button>
            </div>
        </form>
    </div>
</div>
@endsection
