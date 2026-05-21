// Visual error boundary for easier debugging
    window.onerror = function(message, source, lineno, colno, error) {
        const errorBanner = document.createElement('div');
        errorBanner.style = 'position:fixed;top:0;left:0;right:0;background:#ef4444;color:white;padding:16px;z-index:99999;font-family:monospace;font-size:13px;font-weight:bold;text-align:center;box-shadow:0 4px 12px rgba(0,0,0,0.15);';
        errorBanner.innerHTML = '⚠️ Error de JavaScript: ' + message + ' en ' + source.split('/').pop() + ':' + lineno;
        document.body.appendChild(errorBanner);
        console.error(error);
        return false;
    };

    // State management
    let generatedMarkdown = '';
    let detectedToolIds = [];
    let allAvailableTools = [];

    // Inject tools array from PHP to JS
    allAvailableTools = [{"id":2,"name":"ChatGPT","category":"AI Assistants"},{"id":3,"name":"NotebookLM","category":"Research"},{"id":4,"name":"Canva AI","category":"Content Creation"},{"id":5,"name":"Perplexity","category":"Research"},{"id":6,"name":"Suno AI","category":"Music & Audio"},{"id":7,"name":"Stitch","category":"Lesson Planning"},{"id":8,"name":"Pomelo","category":"Productivity"},{"id":9,"name":"Google Slides + Gemini","category":"Presentations"},{"id":10,"name":"Antigravity","category":"AI Assistants"},{"id":11,"name":"Gamma","category":"Presentations"},{"id":1,"name":"Gemini","category":"AI Assistants"},{"id":12,"name":"Claude","category":"AI Assistants"}];

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
            if (select.value === 'OTRO') {
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
        "Analizando el catálogo de tecnología aprobada en AINS...",
        "Integrando marcos SAMR y TPACK en las fases de tu clase...",
        "Diseñando la sección de inicio / warm-up...",
        "Estructurando las actividades del desarrollo...",
        "Generando opciones de evaluación formativa...",
        "Redactando ejemplos prácticos de prompts para docentes..."
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
            if (txt) txt.textContent = 'Generando...';
            if (icon) icon.classList.add('hidden');
            if (spinner) spinner.classList.remove('hidden');
        } else {
            btn.disabled = false;
            btn.classList.remove('opacity-75', 'cursor-not-allowed');
            if (txt) txt.textContent = 'Generar con IA';
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
            const subjectVal = subjectSelect.value === 'OTRO' ? document.getElementById('subject_custom').value : subjectSelect.value;
            
            const gradeSelect = document.getElementById('grade_level');
            const gradeVal = gradeSelect.value === 'OTRO' ? document.getElementById('grade_level_custom').value : gradeSelect.value;
            
            const durationSelect = document.getElementById('duration');
            const durationVal = durationSelect.value === 'OTRO' ? document.getElementById('duration_custom').value : durationSelect.value;
            
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

            // API AJAX request
            fetch('http://:/lesson-plans/generate#', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': 'as4YFYf6u41t6UnJsU6NoFPBxVellUzr4V223hcD'
                },
                body: JSON.stringify({
                    title: titleVal,
                    subject: subjectVal,
                    grade_level: gradeVal,
                    duration: durationVal,
                    objectives: objectivesVal
                })
            })
            .then(res => {
                console.log('[AINS] Generate response status:', res.status);
                if (!res.ok) {
                    return res.json().then(data => { throw new Error(data.error || 'Server error ' + res.status) });
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
                showInlineError('Error al generar: ' + err.message);
            });
        } catch (e) {
            console.error('[AINS] JS exception in startGeneration:', e);
            setBtnLoading(false);
            setStep(1);
            showInlineError('Error de JavaScript: ' + e.message);
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
                stepTitle.textContent = "Paso 1: Configurar detalles";
                stepDesc.textContent = "Ingresa la información básica de tu clase";
                progressBar.style.width = "33%";
                step1.classList.remove('hidden');
            } else if (stepNum === 2) {
                stepBadge.textContent = "2";
                stepBadge.className = "w-8 h-8 rounded-xl bg-ans-orange text-white flex items-center justify-center font-bold text-sm";
                stepTitle.textContent = "Paso 2: Generando planificación";
                stepDesc.textContent = "Nuestra IA está elaborando tu planeación pedagógica";
                progressBar.style.width = "66%";
                loading.classList.remove('hidden');
            } else if (stepNum === 3) {
                stepBadge.textContent = "3";
                stepBadge.className = "w-8 h-8 rounded-xl bg-green-600 text-white flex items-center justify-center font-bold text-sm";
                stepTitle.textContent = "Paso 3: Previsualizar y guardar";
                stepDesc.textContent = "Revisa el plan elaborado y confirma las herramientas asociadas";
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
                badgeList.innerHTML = '<span class="text-xs text-gray-400">No hay herramientas disponibles</span>';
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
            fetch('http://:/lesson-plans#', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': 'as4YFYf6u41t6UnJsU6NoFPBxVellUzr4V223hcD'
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
                alert('Error al guardar la planeación: ' + err.message);
            });
        } catch (e) {
            console.error(e);
            alert('Error en Javascript al guardar: ' + e.message);
        }
    }