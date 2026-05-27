@extends('layouts.app')

@section('header-title', 'AI Lesson Planner')
@section('header-subtitle', 'Design an interactive and structured class planning with Artificial Intelligence')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

<div class="max-w-4xl mx-auto space-y-8 animate-fade-in">
    <!-- Step Tracker Header -->
    <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm flex items-center justify-between">
        <div class="flex items-center gap-3">
            <span id="step-badge" class="w-8 h-8 rounded-xl bg-ans-dark-green text-white flex items-center justify-center font-bold text-sm">1</span>
            <div>
                <h3 id="step-title" class="font-extrabold text-gray-800 text-base">Step 1: Configure details</h3>
                <p id="step-desc" class="text-xs text-gray-500">Enter the basic information of your class</p>
            </div>
        </div>
        <!-- Progress Bar Indicator -->
        <div class="w-32 bg-gray-100 h-2 rounded-full overflow-hidden hidden sm:block">
            <div id="progress-bar" class="bg-ans-dark-green h-full w-1/3 transition-all duration-500"></div>
        </div>
    </div>

    <!-- MAIN CARD CONTAINER -->
    <div class="bg-white border border-gray-100 rounded-3xl p-8 shadow-sm relative overflow-hidden">
        
        <!-- STEP 1: FORM -->
        <div id="panel-step-1" class="space-y-6">
            <form id="form-generator" onsubmit="event.preventDefault();">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Tema/Título -->
                    <div class="md:col-span-2">
                        <label for="title" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Topic or Class Title</label>
                        <input type="text" id="title" placeholder="e.g. Introduction to photosynthesis in natural sciences"
                               class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-ans-light-green/20 focus:border-ans-light-green focus:bg-white transition-all placeholder:text-gray-400">
                        <p id="title-error" class="hidden mt-1.5 text-xs font-semibold text-red-500 flex items-center gap-1">⚠️ This field is required.</p>
                    </div>

                    <!-- Asignatura -->
                    <div>
                        <label for="subject" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Subject</label>
                        <select id="subject" onchange="toggleCustomField('subject')" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-ans-light-green/20 focus:border-ans-light-green focus:bg-white transition-all appearance-none cursor-pointer">
                            <option value="Natural Sciences">Natural Sciences</option>
                            <option value="Mathematics">Mathematics</option>
                            <option value="Spanish">Spanish</option>
                            <option value="English / Literature">English / Literature</option>
                            <option value="Social Studies">Social Studies</option>
                            <option value="Technology / STEM">Technology / STEM</option>
                            <option value="Art & Music">Art & Music</option>
                            <option value="OTHER">Other (Specify)</option>
                        </select>
                        <input type="text" id="subject_custom" placeholder="Specify subject" class="w-full bg-gray-50 border border-gray-200 rounded-xl mt-3 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-ans-light-green/20 focus:border-ans-light-green focus:bg-white transition-all placeholder:text-gray-400 hidden">
                    </div>

                    <!-- Grado/Nivel -->
                    <div>
                        <label for="grade_level" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Grade or School Level</label>
                        <select id="grade_level" onchange="toggleCustomField('grade_level')" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-ans-light-green/20 focus:border-ans-light-green focus:bg-white transition-all appearance-none cursor-pointer">
                            <option value="Pre-K / Kindergarten">Pre-K / Kindergarten</option>
                            <option value="1st - 3rd Grade (Elementary)">1st - 3rd Grade (Elementary)</option>
                            <option value="4th - 5th Grade (Elementary)">4th - 5th Grade (Elementary)</option>
                            <option value="6th - 8th Grade (Middle School)">6th - 8th Grade (Middle School)</option>
                            <option value="9th - 12th Grade (High School)">9th - 12th Grade (High School)</option>
                            <option value="OTHER">Other (Specify)</option>
                        </select>
                        <input type="text" id="grade_level_custom" placeholder="Specify grade" class="w-full bg-gray-50 border border-gray-200 rounded-xl mt-3 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-ans-light-green/20 focus:border-ans-light-green focus:bg-white transition-all placeholder:text-gray-400 hidden">
                    </div>

                    <!-- Duración -->
                    <div>
                        <label for="duration" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Estimated Duration</label>
                        <select id="duration" onchange="toggleCustomField('duration')" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-ans-light-green/20 focus:border-ans-light-green focus:bg-white transition-all appearance-none cursor-pointer">
                            <option value="45 Minutes">45 Minutes</option>
                            <option value="60 Minutes">60 Minutes</option>
                            <option value="90 Minutes">90 Minutes</option>
                            <option value="120 Minutes">120 Minutes</option>
                            <option value="OTHER">Other (Specify)</option>
                        </select>
                        <input type="text" id="duration_custom" placeholder="Specify duration (e.g. 3 hours)" class="w-full bg-gray-50 border border-gray-200 rounded-xl mt-3 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-ans-light-green/20 focus:border-ans-light-green focus:bg-white transition-all placeholder:text-gray-400 hidden">
                    </div>
                </div>

                <!-- Objetivos -->
                <div class="mt-6">
                    <label for="objectives" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Learning Objectives (What will students achieve?)</label>
                    <textarea id="objectives" rows="4" placeholder="e.g. Understand the process of photosynthesis, identify the chemical elements involved (water, carbon dioxide, and light), and the product generated (oxygen and glucose)."
                              class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-ans-light-green/20 focus:border-ans-light-green focus:bg-white transition-all placeholder:text-gray-400 leading-relaxed resize-none"></textarea>
                    <p id="objectives-error" class="hidden mt-1.5 text-xs font-semibold text-red-500 flex items-center gap-1">⚠️ This field is required.</p>
                </div>

                <!-- Attachment (Multimodal Input) -->
                <div class="mt-6">
                    <label for="attachment" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Reference Attachment (Optional PDF, Office Docs, Image, Audio, Video)</label>
                    <input type="file" id="attachment" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,image/*,audio/*,video/*" 
                           class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-ans-light-green/20 focus:border-ans-light-green focus:bg-white transition-all file:mr-4 file:py-1.5 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-ans-dark-green/10 file:text-ans-dark-green hover:file:bg-ans-dark-green/20">
                    <p class="text-[10px] text-gray-400 mt-1">Upload a syllabus, textbook chapter, worksheet, audio clip, or reference video (Max 50MB).</p>
                </div>

                <!-- Button -->
                <div class="mt-8 flex justify-end">
                    <button id="btn-generate" type="button" onclick="startGeneration()" class="w-full sm:w-auto px-8 py-3.5 bg-ans-orange hover:bg-[#e67600] text-white font-bold rounded-xl shadow-md shadow-ans-orange/20 transition-all hover:-translate-y-0.5 flex items-center justify-center gap-2">
                        <span id="btn-generate-text">Generate with AI</span>
                        <svg id="btn-generate-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        <svg id="btn-generate-spinner" class="w-5 h-5 animate-spin hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        <!-- STEP 2: LOADING SCREEN -->
        <div id="panel-loading" class="hidden py-16 flex flex-col items-center justify-center text-center space-y-6">
            <div class="relative w-24 h-24">
                <!-- Dual pulsing rings -->
                <div class="absolute inset-0 rounded-full border-4 border-ans-dark-green/25 animate-ping"></div>
                <div class="absolute inset-2 rounded-full border-4 border-ans-orange/20 animate-pulse"></div>
                <div class="absolute inset-4 rounded-2xl bg-gradient-to-tr from-ans-dark-green to-ans-light-green shadow-xl flex items-center justify-center text-white">
                    <svg class="w-8 h-8 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>
            <div>
                <h4 id="loading-header" class="text-xl font-bold text-gray-800">Generating your lesson plan...</h4>
                <p id="loading-tip" class="text-sm text-ans-dark-green/70 mt-2 font-medium italic transition-all duration-300">
                    "Structuring the plan under the SAMR framework..."
                </p>
            </div>
        </div>

        <!-- STEP 3: PREVIEW & CONFIRMATION -->
        <div id="panel-preview" class="hidden space-y-6">
            <div class="border border-gray-100 bg-gray-50/50 rounded-2xl p-6 md:p-8">
                <!-- Lesson plan content wrapper -->
                <article id="markdown-container" class="prose max-w-none text-gray-700 text-sm leading-relaxed prose-headings:text-gray-800 prose-headings:font-heading prose-strong:text-ans-dark-green prose-code:bg-white prose-code:px-2 prose-code:py-1 prose-code:rounded prose-code:border prose-code:border-gray-100 prose-a:text-ans-orange prose-a:underline"></article>
            </div>

            <!-- AI Refinement Panel (Punto 2 - Part A) -->
            <div class="bg-gradient-to-r from-ans-dark-green/5 to-ans-orange/5 border border-ans-dark-green/10 rounded-2xl p-6 space-y-4">
                <div class="flex items-center gap-2">
                    <span class="p-1.5 rounded-lg bg-ans-dark-green text-white">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </svg>
                    </span>
                    <div>
                        <h4 class="text-sm font-bold text-gray-800">Refine Lesson Plan with AI</h4>
                        <p class="text-xs text-gray-500">Want to adjust the planning? Ask the AI to make specific changes.</p>
                    </div>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1">
                        <textarea id="refine-instructions" rows="2" placeholder="e.g. Add a 10-minute icebreaker at the start, or adapt the activities for a student with ADHD."
                                  class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-ans-dark-green/20 focus:border-ans-dark-green transition-all placeholder:text-gray-400 text-sm leading-relaxed"></textarea>
                        <p id="refine-error" class="hidden mt-1 text-xs font-semibold text-red-500">⚠️ Please enter refinement instructions.</p>
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

            <!-- Tool selector/Detected tags -->
            <div id="detected-tools-area" class="border-t border-gray-100 pt-6">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Recommended EdTech Tools</p>
                <div id="detected-tools-list" class="flex flex-wrap gap-2">
                    <!-- Dynamic checkable badges -->
                </div>
            </div>

            <!-- Footer controls -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-t border-gray-100 pt-6">
                <button type="button" onclick="goBack()" class="px-6 py-3 border border-gray-200 text-gray-500 font-bold rounded-xl hover:bg-gray-50 transition-all text-sm">
                    Modify Input
                </button>
                <button type="button" onclick="savePlan()" class="px-8 py-3.5 bg-ans-dark-green hover:bg-ans-seal-green text-white font-bold rounded-xl shadow-md shadow-ans-dark-green/20 transition-all hover:-translate-y-0.5 flex items-center justify-center gap-2 text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                    </svg>
                    <span>Save Lesson Plan</span>
                </button>
            </div>
        </div>

    </div>
</div>

<script>
    // Visual error boundary for easier debugging
    window.onerror = function(message, source, lineno, colno, error) {
        const errorBanner = document.createElement('div');
        errorBanner.style = 'position:fixed;top:0;left:0;right:0;background:#ef4444;color:white;padding:16px;z-index:99999;font-family:monospace;font-size:13px;font-weight:bold;text-align:center;box-shadow:0 4px 12px rgba(0,0,0,0.15);';
        errorBanner.innerHTML = '⚠️ JavaScript Error: ' + message + ' in ' + source.split('/').pop() + ':' + lineno;
        document.body.appendChild(errorBanner);
        console.error(error);
        return false;
    };

    // State management
    let generatedMarkdown = '';
    let detectedToolIds = [];
    let allAvailableTools = [];

    // Inject tools array from PHP to JS
    allAvailableTools = @json(\App\Models\Tool::approved()->get(['id', 'name', 'category']));

    function showInlineError(msg) {
        let banner = document.getElementById('inline-error-banner');
        if (!banner) {
            banner = document.createElement('div');
            banner.id = 'inline-error-banner';
            banner.className = 'mt-4 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700 font-semibold flex items-center gap-2';
            const form = document.getElementById('form-generator');
            if (form) form.appendChild(banner);
        }
        banner.innerHTML = '&#9888;&#65039; ' + msg;
        banner.classList.remove('hidden');
        setTimeout(() => banner.classList.add('hidden'), 8000);
    }

    function toggleCustomField(fieldId) {
        try {
            const select = document.getElementById(fieldId);
            const customInput = document.getElementById(fieldId + '_custom');
            if (select.value === 'OTHER') {
                customInput.classList.remove('hidden');
                customInput.required = true;
            } else {
                customInput.classList.add('hidden');
                customInput.required = false;
            }
        } catch (e) {
            console.error(e);
        }
    }

    const cyclingMessages = [
        "Analyzing the approved technology catalog in AINS...",
        "Integrating SAMR and TPACK frameworks into lesson phases...",
        "Designing the introduction / warm-up section...",
        "Structuring development activities...",
        "Generating formative assessment options...",
        "Drafting practical prompt examples for teachers..."
    ];
    let intervalId = null;

    function showFieldError(fieldId, errorId) {
        const field = document.getElementById(fieldId);
        const errorEl = document.getElementById(errorId);
        if (field) {
            field.classList.add('border-red-400', 'ring-2', 'ring-red-200');
            field.classList.remove('border-gray-200');
        }
        if (errorEl) errorEl.classList.remove('hidden');
    }

    function clearFieldError(fieldId, errorId) {
        const field = document.getElementById(fieldId);
        const errorEl = document.getElementById(errorId);
        if (field) {
            field.classList.remove('border-red-400', 'ring-2', 'ring-red-200');
            field.classList.add('border-gray-200');
        }
        if (errorEl) errorEl.classList.add('hidden');
    }

    function setBtnLoading(loading) {
        const btn = document.getElementById('btn-generate');
        const txt = document.getElementById('btn-generate-text');
        const icon = document.getElementById('btn-generate-icon');
        const spinner = document.getElementById('btn-generate-spinner');
        if (!btn) return;
        if (loading) {
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            if (txt) txt.textContent = 'Generating...';
            if (icon) icon.classList.add('hidden');
            if (spinner) spinner.classList.remove('hidden');
        } else {
            btn.disabled = false;
            btn.classList.remove('opacity-75', 'cursor-not-allowed');
            if (txt) txt.textContent = 'Generate with AI';
            if (icon) icon.classList.remove('hidden');
            if (spinner) spinner.classList.add('hidden');
        }
    }

    function startGeneration() {
        console.log('[AINS] startGeneration() called');
        try {
            // Collect and validate form data
            const titleEl = document.getElementById('title');
            const objectivesEl = document.getElementById('objectives');
            let hasError = false;

            clearFieldError('title', 'title-error');
            clearFieldError('objectives', 'objectives-error');

            if (!titleEl || !titleEl.value.trim()) {
                showFieldError('title', 'title-error');
                if (titleEl) titleEl.focus();
                hasError = true;
            }
            if (!objectivesEl || !objectivesEl.value.trim()) {
                showFieldError('objectives', 'objectives-error');
                if (!hasError && objectivesEl) objectivesEl.focus();
                hasError = true;
            }
            if (hasError) {
                console.log('[AINS] Validation failed — missing required fields');
                return;
            }

            const titleVal = titleEl.value.trim();
            
            const subjectSelect = document.getElementById('subject');
            const subjectVal = subjectSelect.value === 'OTHER' ? document.getElementById('subject_custom').value : subjectSelect.value;
            
            const gradeSelect = document.getElementById('grade_level');
            const gradeVal = gradeSelect.value === 'OTHER' ? document.getElementById('grade_level_custom').value : gradeSelect.value;
            
            const durationSelect = document.getElementById('duration');
            const durationVal = durationSelect.value === 'OTHER' ? document.getElementById('duration_custom').value : durationSelect.value;
            
            const objectivesVal = objectivesEl.value.trim();

            console.log('[AINS] Sending generate request:', { title: titleVal, subject: subjectVal, grade_level: gradeVal });

            // Show button loading state
            setBtnLoading(true);

            // Transition UI to Step 2
            setStep(2);

            // Start cycling tips
            let msgIndex = 0;
            const tipEl = document.getElementById('loading-tip');
            if (tipEl) tipEl.textContent = cyclingMessages[0];
            intervalId = setInterval(() => {
                msgIndex = (msgIndex + 1) % cyclingMessages.length;
                if (tipEl) {
                    tipEl.style.opacity = 0;
                    setTimeout(() => {
                        tipEl.textContent = cyclingMessages[msgIndex];
                        tipEl.style.opacity = 1;
                    }, 300);
                }
            }, 3500);

            // Setup FormData for file upload support
            const formData = new FormData();
            formData.append('title', titleVal);
            formData.append('subject', subjectVal);
            formData.append('grade_level', gradeVal);
            formData.append('duration', durationVal);
            formData.append('objectives', objectivesVal);
            
            const fileInput = document.getElementById('attachment');
            if (fileInput && fileInput.files[0]) {
                formData.append('attachment', fileInput.files[0]);
            }

            // API AJAX request
            fetch('{{ route("lesson-plans.generate") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(res => {
                console.log('[AINS] Generate response status:', res.status);
                if (!res.ok) {
                    return res.json().then(data => {
                        let errMsg = data.error;
                        if (data.errors) {
                            errMsg = Object.values(data.errors).flat().join(' ');
                        }
                        throw new Error(errMsg || 'Server error ' + res.status);
                    });
                }
                return res.json();
            })
            .then(data => {
                console.log('[AINS] Generate success, markdown length:', data.markdown?.length);
                clearInterval(intervalId);
                generatedMarkdown = data.markdown;
                detectedToolIds = data.tool_ids || [];
                setBtnLoading(false);
                renderPreview();
                setStep(3);
            })
            .catch(err => {
                clearInterval(intervalId);
                setBtnLoading(false);
                setStep(1);
                console.error('[AINS] Generate error:', err);
                showInlineError('Generation error: ' + err.message);
            });
        } catch (e) {
            console.error('[AINS] JS exception in startGeneration:', e);
            setBtnLoading(false);
            setStep(1);
            showInlineError('JavaScript error: ' + e.message);
        }
    }

    function setStep(stepNum) {
        try {
            const stepBadge = document.getElementById('step-badge');
            const stepTitle = document.getElementById('step-title');
            const stepDesc = document.getElementById('step-desc');
            const progressBar = document.getElementById('progress-bar');

            const step1 = document.getElementById('panel-step-1');
            const loading = document.getElementById('panel-loading');
            const preview = document.getElementById('panel-preview');

            // Hide all
            step1.classList.add('hidden');
            loading.classList.add('hidden');
            preview.classList.add('hidden');

            if (stepNum === 1) {
                stepBadge.textContent = "1";
                stepBadge.className = "w-8 h-8 rounded-xl bg-ans-dark-green text-white flex items-center justify-center font-bold text-sm";
                stepTitle.textContent = "Step 1: Configure details";
                stepDesc.textContent = "Enter the basic information of your class";
                progressBar.style.width = "33%";
                step1.classList.remove('hidden');
            } else if (stepNum === 2) {
                stepBadge.textContent = "2";
                stepBadge.className = "w-8 h-8 rounded-xl bg-ans-orange text-white flex items-center justify-center font-bold text-sm";
                stepTitle.textContent = "Step 2: Generating lesson plan";
                stepDesc.textContent = "Our AI is crafting your pedagogical plan";
                progressBar.style.width = "66%";
                loading.classList.remove('hidden');
            } else if (stepNum === 3) {
                stepBadge.textContent = "3";
                stepBadge.className = "w-8 h-8 rounded-xl bg-green-600 text-white flex items-center justify-center font-bold text-sm";
                stepTitle.textContent = "Step 3: Preview and save";
                stepDesc.textContent = "Review the crafted plan and confirm the associated tools";
                progressBar.style.width = "100%";
                preview.classList.remove('hidden');
            }
        } catch (e) {
            console.error(e);
        }
    }

    function renderPreview() {
        try {
            // Render Markdown
            const container = document.getElementById('markdown-container');
            if (typeof marked !== 'undefined') {
                container.innerHTML = marked.parse(generatedMarkdown);
            } else {
                container.innerHTML = '<pre style="white-space: pre-wrap;">' + generatedMarkdown + '</pre>';
            }

            // Build tools checkboxes
            const badgeList = document.getElementById('detected-tools-list');
            badgeList.innerHTML = '';

            if (allAvailableTools.length === 0) {
                badgeList.innerHTML = '<span class="text-xs text-gray-400">No tools available</span>';
                return;
            }

            allAvailableTools.forEach(tool => {
                const isChecked = detectedToolIds.includes(tool.id);
                const label = document.createElement('label');
                label.className = `flex items-center gap-2 px-3 py-1.5 rounded-lg border text-xs cursor-pointer select-none transition-all ${
                    isChecked 
                    ? 'bg-ans-dark-green/10 border-ans-dark-green/30 text-ans-dark-green font-bold shadow-sm' 
                    : 'bg-white border-gray-200 text-gray-500 hover:bg-gray-50'
                }`;
                label.innerHTML = `
                    <input type="checkbox" value="${tool.id}" ${isChecked ? 'checked' : ''} onchange="toggleToolBadge(this)" class="accent-ans-dark-green w-3.5 h-3.5 rounded border-gray-300">
                    <span>${tool.name}</span>
                `;
                badgeList.appendChild(label);
            });
        } catch (e) {
            console.error(e);
        }
    }

    function toggleToolBadge(checkbox) {
        try {
            const label = checkbox.closest('label');
            const val = parseInt(checkbox.value);
            if (checkbox.checked) {
                label.className = 'flex items-center gap-2 px-3 py-1.5 rounded-lg border text-xs cursor-pointer select-none transition-all bg-ans-dark-green/10 border-ans-dark-green/30 text-ans-dark-green font-bold shadow-sm';
                if (!detectedToolIds.includes(val)) {
                    detectedToolIds.push(val);
                }
            } else {
                label.className = 'flex items-center gap-2 px-3 py-1.5 rounded-lg border text-xs cursor-pointer select-none transition-all bg-white border-gray-200 text-gray-500 hover:bg-gray-50';
                detectedToolIds = detectedToolIds.filter(id => id !== val);
            }
        } catch (e) {
            console.error(e);
        }
    }

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
                    content: generatedMarkdown,
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
                generatedMarkdown = data.markdown;
                renderPreview();
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

    function goBack() {
        if (intervalId) clearInterval(intervalId);
        setStep(1);
    }

    function savePlan() {
        try {
            // Collect form input data
            const titleVal = document.getElementById('title').value;
            
            const subjectSelect = document.getElementById('subject');
            const subjectVal = subjectSelect.value === 'OTRO' ? document.getElementById('subject_custom').value : subjectSelect.value;
            
            const gradeSelect = document.getElementById('grade_level');
            const gradeVal = gradeSelect.value === 'OTRO' ? document.getElementById('grade_level_custom').value : gradeSelect.value;
            
            const durationSelect = document.getElementById('duration');
            const durationVal = durationSelect.value === 'OTRO' ? document.getElementById('duration_custom').value : durationSelect.value;
            
            const objectivesVal = document.getElementById('objectives').value;

            // Save call via AJAX
            fetch('{{ route("lesson-plans.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    title: titleVal,
                    subject: subjectVal,
                    grade_level: gradeVal,
                    duration: durationVal,
                    objectives: objectivesVal,
                    content: generatedMarkdown,
                    selected_tools: detectedToolIds
                })
            })
            .then(res => {
                if (!res.ok) {
                    return res.json().then(data => { throw new Error(data.error || 'Server error') });
                }
                return res.json();
            })
            .then(data => {
                window.location.href = data.url;
            })
            .catch(err => {
                alert('Error saving lesson plan: ' + err.message);
            });
        } catch (e) {
            console.error(e);
            alert('JavaScript error while saving: ' + e.message);
        }
    }
</script>
@endsection
