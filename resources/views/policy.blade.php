@extends('layouts.app')

@section('header-title', 'AI School Policy')
@section('header-subtitle', 'Rules and guidelines for the ethical, honest, and responsible use of artificial intelligence at ANS.')

@section('content')

<style>
    /* Efecto hover premium para tarjetas */
    .policy-card {
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .policy-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px -10px rgba(0, 105, 55, 0.15);
    }
    
    /* Probador de casos activo */
    .case-item {
        transition: all 0.2s ease-in-out;
    }
    .case-item.active {
        background-color: rgba(0, 105, 55, 0.08);
        border-color: #006937;
        color: #006937;
    }
    
    /* Animación de expansión para el acordeón FAQ */
    .faq-answer {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .faq-item.active .faq-answer {
        max-height: 500px;
    }
    .faq-item.active .faq-icon {
        transform: rotate(180deg);
    }
</style>

<div class="space-y-12 pb-12 animate-fade-in-up">
    
    <!-- 1. Pilares de la Política (Grid) -->
    <div class="space-y-6">
        <div class="border-b border-gray-200 pb-4">
            <h3 class="text-2xl font-heading font-extrabold text-gray-800 tracking-tight">
                Core Pillars
            </h3>
            <p class="text-gray-500 mt-1">The four central principles of AI use in the educational community.</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Pilar 1: Integridad -->
            <div class="policy-card bg-white border border-gray-200/60 rounded-2xl p-6 shadow-sm flex flex-col justify-between relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1.5 h-full bg-ans-dark-green"></div>
                <div class="space-y-4">
                    <div class="w-12 h-12 bg-ans-dark-green/10 rounded-xl flex items-center justify-center text-ans-dark-green">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <h4 class="text-lg font-bold text-gray-800">Academic Integrity</h4>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        AI is a partner to co-create and learn, not to replace critical thinking. The work turned in must reflect the student's genuine effort.
                    </p>
                </div>
            </div>

            <!-- Pilar 2: Atribución -->
            <div class="policy-card bg-white border border-gray-200/60 rounded-2xl p-6 shadow-sm flex flex-col justify-between relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1.5 h-full bg-ans-orange"></div>
                <div class="space-y-4">
                    <div class="w-12 h-12 bg-ans-orange/10 rounded-xl flex items-center justify-center text-ans-orange">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                    <h4 class="text-lg font-bold text-gray-800">Attribution & Citations</h4>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        AI use must always be declared. Cite the tool, the prompt used, and what part of the work was generated or assisted by the software.
                    </p>
                </div>
            </div>

            <!-- Pilar 3: Confidencialidad -->
            <div class="policy-card bg-white border border-gray-200/60 rounded-2xl p-6 shadow-sm flex flex-col justify-between relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1.5 h-full bg-blue-500"></div>
                <div class="space-y-4">
                    <div class="w-12 h-12 bg-blue-500/10 rounded-xl flex items-center justify-center text-blue-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <h4 class="text-lg font-bold text-gray-800">Data Privacy</h4>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        Never upload confidential information, health data, private grades, or identifiable photos of the school or people to public AI platforms.
                    </p>
                </div>
            </div>

            <!-- Pilar 4: Desarrollo -->
            <div class="policy-card bg-white border border-gray-200/60 rounded-2xl p-6 shadow-sm flex flex-col justify-between relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1.5 h-full bg-ans-light-green"></div>
                <div class="space-y-4">
                    <div class="w-12 h-12 bg-ans-light-green/10 rounded-xl flex items-center justify-center text-ans-light-green">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <h4 class="text-lg font-bold text-gray-800">Professional Development</h4>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        Use technology to enhance your skills, learn to program, debug ideas, and actively prepare for a professional and work ecosystem with AI.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Probador de Casos Interactivo (Dos columnas) -->
    <div class="space-y-6">
        <div class="border-b border-gray-200 pb-4">
            <h3 class="text-2xl font-heading font-extrabold text-gray-800 tracking-tight">
                Interactive Case Analyzer
            </h3>
            <p class="text-gray-500 mt-1">Select a daily scenario to verify if it is permitted by our school policy.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
            <!-- Columna Izquierda: Botones de situaciones (5 cols) -->
            <div class="lg:col-span-5 flex flex-col gap-3">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Classroom Scenarios</p>
                
                <button onclick="selectCase('brainstorming')" id="btn-brainstorming" class="case-item w-full text-left p-4 rounded-2xl border border-gray-200 bg-white hover:border-ans-dark-green/30 flex items-center gap-4 group">
                    <span class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center text-gray-500 group-hover:bg-ans-dark-green/10 group-hover:text-ans-dark-green transition-all">
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-ans-dark-green transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                    </span>
                    <div>
                        <p class="font-bold text-gray-800 text-sm">Project Brainstorming</p>
                        <p class="text-xs text-gray-500 mt-0.5">Creating topics and approaches</p>
                    </div>
                </button>

                <button onclick="selectCase('writing')" id="btn-writing" class="case-item w-full text-left p-4 rounded-2xl border border-gray-200 bg-white hover:border-ans-dark-green/30 flex items-center gap-4 group">
                    <span class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center text-gray-500 group-hover:bg-ans-dark-green/10 group-hover:text-ans-dark-green transition-all">
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-ans-dark-green transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </span>
                    <div>
                        <p class="font-bold text-gray-800 text-sm">Writing a Complete Essay</p>
                        <p class="text-xs text-gray-500 mt-0.5">Submitting AI-generated text</p>
                    </div>
                </button>

                <button onclick="selectCase('translation')" id="btn-translation" class="case-item w-full text-left p-4 rounded-2xl border border-gray-200 bg-white hover:border-ans-dark-green/30 flex items-center gap-4 group">
                    <span class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center text-gray-500 group-hover:bg-ans-dark-green/10 group-hover:text-ans-dark-green transition-all">
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-ans-dark-green transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5c-.347 2.187-1.54 4.545-3.114 6.879M9 9a12.435 12.435 0 01-3-3m0 0H3.75"></path></svg>
                    </span>
                    <div>
                        <p class="font-bold text-gray-800 text-sm">Translating Study Materials</p>
                        <p class="text-xs text-gray-500 mt-0.5">Multilingual reading comprehension</p>
                    </div>
                </button>

                <button onclick="selectCase('math')" id="btn-math" class="case-item w-full text-left p-4 rounded-2xl border border-gray-200 bg-white hover:border-ans-dark-green/30 flex items-center gap-4 group">
                    <span class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center text-gray-500 group-hover:bg-ans-dark-green/10 group-hover:text-ans-dark-green transition-all">
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-ans-dark-green transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    </span>
                    <div>
                        <p class="font-bold text-gray-800 text-sm">Step-by-Step Math Solving</p>
                        <p class="text-xs text-gray-500 mt-0.5">Explanation of complex problems</p>
                    </div>
                </button>

                <button onclick="selectCase('grammar')" id="btn-grammar" class="case-item w-full text-left p-4 rounded-2xl border border-gray-200 bg-white hover:border-ans-dark-green/30 flex items-center gap-4 group">
                    <span class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center text-gray-500 group-hover:bg-ans-dark-green/10 group-hover:text-ans-dark-green transition-all">
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-ans-dark-green transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </span>
                    <div>
                        <p class="font-bold text-gray-800 text-sm">Grammar & Spelling Correction</p>
                        <p class="text-xs text-gray-500 mt-0.5">Polishing essays and style</p>
                    </div>
                </button>

                <button onclick="selectCase('code')" id="btn-code" class="case-item w-full text-left p-4 rounded-2xl border border-gray-200 bg-white hover:border-ans-dark-green/30 flex items-center gap-4 group">
                    <span class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center text-gray-500 group-hover:bg-ans-dark-green/10 group-hover:text-ans-dark-green transition-all">
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-ans-dark-green transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                    </span>
                    <div>
                        <p class="font-bold text-gray-800 text-sm">Debugging Code Errors</p>
                        <p class="text-xs text-gray-500 mt-0.5">Assistant in technology projects</p>
                    </div>
                </button>
            </div>

            <!-- Columna Derecha: Tarjeta de Veredicto (7 cols) -->
            <div class="lg:col-span-7 bg-white border border-gray-200/60 rounded-3xl p-8 flex flex-col justify-between shadow-sm relative overflow-hidden">
                <!-- Decorative background elements -->
                <div class="absolute -right-12 -top-12 w-48 h-48 bg-ans-dark-green/5 rounded-full blur-2xl"></div>
                
                <div id="verdict-placeholder" class="h-full flex flex-col items-center justify-center text-center p-8 space-y-4">
                    <div class="w-20 h-20 bg-ans-dark-green/5 text-ans-dark-green rounded-full flex items-center justify-center">
                        <svg class="w-10 h-10 text-ans-dark-green" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                    </div>
                    <div>
                        <h5 class="text-lg font-bold text-gray-800">Unsure about a practical case?</h5>
                        <p class="text-sm text-gray-500 mt-2 max-w-sm">Click on any of the school situations in the left menu to analyze its feasibility and verdict under AINS guidelines.</p>
                    </div>
                </div>

                <div id="verdict-display" class="hidden h-full flex flex-col justify-between space-y-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Selected Case</span>
                            <span id="case-verdict-badge" class="px-3.5 py-1 rounded-full text-xs font-extrabold uppercase border">ALLOWED</span>
                        </div>
                        <h4 id="case-title" class="text-2xl font-heading font-extrabold text-gray-900 leading-tight">Project Brainstorming</h4>
                        <p id="case-description" class="text-gray-600 text-sm leading-relaxed">Using generative artificial intelligence to suggest topics, research approaches, outline structures, or initial ideas for an academic project.</p>
                    </div>

                    <div class="bg-gray-50 border border-gray-100 rounded-2xl p-5 space-y-3">
                        <p class="text-xs font-bold text-ans-dark-green uppercase tracking-wider flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Pedagogical Recommendation & Advice
                        </p>
                        <p id="case-advice" class="text-xs text-gray-600 leading-relaxed">Excellent tool to break writer's block. Be sure to develop the final proposal on your own and that the ideas reflect your personal perspective.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Acordeón FAQ (Accordion) -->
    <div class="space-y-6">
        <div class="border-b border-gray-200 pb-4">
            <h3 class="text-2xl font-heading font-extrabold text-gray-800 tracking-tight">
                Frequently Asked Questions (FAQ)
            </h3>
            <p class="text-gray-500 mt-1">Answers to common questions regarding the practical use of artificial intelligence.</p>
        </div>

        <div class="space-y-4 max-w-4xl mx-auto">
            <!-- FAQ 1 -->
            <div class="faq-item bg-white border border-gray-200/70 rounded-2xl overflow-hidden shadow-sm transition-all duration-300">
                <button onclick="toggleFaq(this)" class="w-full px-6 py-5 text-left flex justify-between items-center gap-4">
                    <span class="font-bold text-gray-800 hover:text-ans-dark-green transition-colors text-base">What happens if my teacher finds out I used undeclared AI?</span>
                    <span class="faq-icon w-8 h-8 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400 border border-gray-100 transition-transform duration-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </span>
                </button>
                <div class="faq-answer">
                    <div class="px-6 pb-6 text-sm text-gray-600 leading-relaxed border-t border-gray-50 pt-4">
                        The undeclared use of generative AI tools is classified as an academic integrity violation, similar to traditional plagiarism. Consequences will be applied in accordance with the school rules handbook and the AINS Honor Code, which may include grade invalidation and corresponding reports.
                    </div>
                </div>
            </div>

            <!-- FAQ 2 -->
            <div class="faq-item bg-white border border-gray-200/70 rounded-2xl overflow-hidden shadow-sm transition-all duration-300">
                <button onclick="toggleFaq(this)" class="w-full px-6 py-5 text-left flex justify-between items-center gap-4">
                    <span class="font-bold text-gray-800 hover:text-ans-dark-green transition-colors text-base">How should I cite an AI tool in an assignment?</span>
                    <span class="faq-icon w-8 h-8 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400 border border-gray-100 transition-transform duration-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </span>
                </button>
                <div class="faq-answer">
                    <div class="px-6 pb-6 text-sm text-gray-600 leading-relaxed border-t border-gray-50 pt-4">
                        You must include a citation at the end of your work with the following elements:
                        <ul class="list-disc pl-5 mt-2 space-y-1.5 text-xs">
                            <li>Model name (e.g., ChatGPT v4o, Claude 3.5 Sonnet).</li>
                            <li>Date you consulted it.</li>
                            <li>The specific prompt you used.</li>
                            <li>A brief description of how the AI helped you (e.g., "Used to structure the outline and correct grammatical errors in the introduction section").</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- FAQ 3 -->
            <div class="faq-item bg-white border border-gray-200/70 rounded-2xl overflow-hidden shadow-sm transition-all duration-300">
                <button onclick="toggleFaq(this)" class="w-full px-6 py-5 text-left flex justify-between items-center gap-4">
                    <span class="font-bold text-gray-800 hover:text-ans-dark-green transition-colors text-base">Can teachers use software to detect if I used AI?</span>
                    <span class="faq-icon w-8 h-8 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400 border border-gray-100 transition-transform duration-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </span>
                </button>
                <div class="faq-answer">
                    <div class="px-6 pb-6 text-sm text-gray-600 leading-relaxed border-t border-gray-50 pt-4">
                        Yes, the school has detection tools integrated into platforms like Google Classroom and Turnitin. However, we recognize that detectors are not infallible. The primary criterion for evaluating the integrity of a work will be the direct dialogue between the teacher and the student regarding the creation process and understanding of the assignment.
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    // Configuración del Probador de Casos
    const casesData = {
        'brainstorming': {
            title: 'Project Brainstorming',
            verdict: 'ALLOWED',
            badgeClass: 'bg-emerald-100/75 text-emerald-800 border-emerald-200/80',
            description: 'Using generative artificial intelligence to suggest topics, research approaches, outline structures, or initial ideas for an academic project.',
            advice: 'Excellent tool to break writer\'s block. Be sure to develop the final proposal on your own and that the ideas reflect your personal perspective.'
        },
        'writing': {
            title: 'Writing a Complete Essay',
            verdict: 'NOT ALLOWED',
            badgeClass: 'bg-rose-100/75 text-rose-800 border-rose-200/80',
            description: 'Asking the AI to draft complete paragraphs or an entire essay, lab report, exam, or project to turn it in as your own work without attribution.',
            advice: 'This constitutes a direct violation of the school\'s intellectual honesty rules. The drafting, final analysis, and critical conclusions must always be developed by you.'
        },
        'translation': {
            title: 'Translating Study Materials',
            verdict: 'ALLOWED',
            badgeClass: 'bg-emerald-100/75 text-emerald-800 border-emerald-200/80',
            description: 'Translating technical literature, extensive academic readings, or news articles from English to Spanish (or vice versa) to facilitate study and comprehension.',
            advice: 'Productive use of AI to overcome language barriers. If you are completing specific tasks for language courses (English, Spanish, French), do not use direct translators to generate compositions, as you block your skill development.'
        },
        'math': {
            title: 'Step-by-Step Math Solving',
            verdict: 'CAUTION',
            badgeClass: 'bg-amber-100/75 text-amber-800 border-amber-200/80',
            description: 'Pasting an algebra, physics, or chemistry problem to get the numerical answer and step-by-step logical steps resolved by the model.',
            advice: 'You are allowed to use it for tutoring and self-learning purposes to understand the methodology of the exercise. However, copying the exact steps onto your submission sheet without being able to recreate them or understand the concepts violates the objectives of the subject.'
        },
        'grammar': {
            title: 'Grammar & Spelling Correction',
            verdict: 'ALLOWED',
            badgeClass: 'bg-emerald-100/75 text-emerald-800 border-emerald-200/80',
            description: 'Submitting your previously written essay to request style suggestions, improve technical vocabulary, polish coherence, and correct spelling or punctuation errors.',
            advice: 'Encourages polish and rigor in assignments. Try to actively analyze each modification proposed by the system to learn lessons and write better in the future.'
        },
        'code': {
            title: 'Debugging Code Errors',
            verdict: 'CAUTION',
            badgeClass: 'bg-amber-100/75 text-amber-800 border-amber-200/80',
            description: 'Uploading code snippets of your own creation to identify logical bugs, syntax errors, or outdated libraries in technology projects.',
            advice: 'Very enriching programming support. However, do not delegate the entire creation of the algorithm or the main software structure, as you need to consolidate basic programming logic.'
        }
    };

    function selectCase(caseKey) {
        // Remover clase activa de todos los botones
        document.querySelectorAll('.case-item').forEach(btn => {
            btn.classList.remove('active');
        });

        // Activar el botón correspondiente
        const activeBtn = document.getElementById('btn-' + caseKey);
        if (activeBtn) {
            activeBtn.classList.add('active');
        }

        // Obtener datos
        const data = casesData[caseKey];
        if (data) {
            // Mostrar display, ocultar placeholder
            document.getElementById('verdict-placeholder').classList.add('hidden');
            const display = document.getElementById('verdict-display');
            display.classList.remove('hidden');

            // Actualizar textos
            document.getElementById('case-title').textContent = data.title;
            document.getElementById('case-description').textContent = data.description;
            document.getElementById('case-advice').textContent = data.advice;
            
            // Actualizar badge
            const badge = document.getElementById('case-verdict-badge');
            badge.textContent = data.verdict;
            badge.className = `px-3.5 py-1 rounded-full text-xs font-extrabold uppercase border ${data.badgeClass}`;
        }
    }

    // Manejo del acordeón FAQ
    function toggleFaq(button) {
        const item = button.closest('.faq-item');
        const isActive = item.classList.contains('active');

        // Cerrar todos
        document.querySelectorAll('.faq-item').forEach(fItem => {
            fItem.classList.remove('active');
        });

        // Si no estaba activo, abrirlo
        if (!isActive) {
            item.classList.add('active');
        }
    }
</script>

@endsection
