@extends('layouts.app')

@section('header-title', 'AI School Policy')
@section('header-subtitle', 'Normas y lineamientos para el uso ético, íntegro y responsable de la inteligencia artificial en ANS.')

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
                Pilares Fundamentales
            </h3>
            <p class="text-gray-500 mt-1">Los cuatro principios centrales del uso de la IA en la comunidad educativa.</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Pilar 1: Integridad -->
            <div class="policy-card bg-white border border-gray-200/60 rounded-2xl p-6 shadow-sm flex flex-col justify-between relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1.5 h-full bg-ans-dark-green"></div>
                <div class="space-y-4">
                    <div class="w-12 h-12 bg-ans-dark-green/10 rounded-xl flex items-center justify-center text-ans-dark-green">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <h4 class="text-lg font-bold text-gray-800">Integridad Académica</h4>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        La IA es un socio para co-crear y aprender, no para reemplazar el pensamiento crítico. El trabajo entregado debe reflejar el esfuerzo genuino del estudiante.
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
                    <h4 class="text-lg font-bold text-gray-800">Atribución y Citas</h4>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        Siempre se debe declarar el uso de la IA. Cita la herramienta, el prompt utilizado y qué parte del trabajo fue generada o asistida por el software.
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
                    <h4 class="text-lg font-bold text-gray-800">Privacidad de Datos</h4>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        Nunca subas información confidencial, datos de salud, calificaciones privadas o fotos identificables del colegio o personas a plataformas de IA públicas.
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
                    <h4 class="text-lg font-bold text-gray-800">Desarrollo Profesional</h4>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        Usar la tecnología para potenciar tus destrezas, aprender a programar, depurar ideas y prepararte activamente para un ecosistema profesional y laboral con IA.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Probador de Casos Interactivo (Dos columnas) -->
    <div class="space-y-6">
        <div class="border-b border-gray-200 pb-4">
            <h3 class="text-2xl font-heading font-extrabold text-gray-800 tracking-tight">
                Probador de Casos Interactivo
            </h3>
            <p class="text-gray-500 mt-1">Selecciona una situación cotidiana para verificar si está permitida por nuestra política escolar.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
            <!-- Columna Izquierda: Botones de situaciones (5 cols) -->
            <div class="lg:col-span-5 flex flex-col gap-3">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Situaciones del Aula</p>
                
                <button onclick="selectCase('brainstorming')" id="btn-brainstorming" class="case-item w-full text-left p-4 rounded-2xl border border-gray-200 bg-white hover:border-ans-dark-green/30 flex items-center gap-4 group">
                    <span class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center text-gray-500 group-hover:bg-ans-dark-green/10 group-hover:text-ans-dark-green transition-all">💡</span>
                    <div>
                        <p class="font-bold text-gray-800 text-sm">Lluvia de ideas para proyectos</p>
                        <p class="text-xs text-gray-500 mt-0.5">Creación de temas y enfoques</p>
                    </div>
                </button>

                <button onclick="selectCase('writing')" id="btn-writing" class="case-item w-full text-left p-4 rounded-2xl border border-gray-200 bg-white hover:border-ans-dark-green/30 flex items-center gap-4 group">
                    <span class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center text-gray-500 group-hover:bg-ans-dark-green/10 group-hover:text-ans-dark-green transition-all">📝</span>
                    <div>
                        <p class="font-bold text-gray-800 text-sm">Escribir un ensayo completo</p>
                        <p class="text-xs text-gray-500 mt-0.5">Entregar texto generado por IA</p>
                    </div>
                </button>

                <button onclick="selectCase('translation')" id="btn-translation" class="case-item w-full text-left p-4 rounded-2xl border border-gray-200 bg-white hover:border-ans-dark-green/30 flex items-center gap-4 group">
                    <span class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center text-gray-500 group-hover:bg-ans-dark-green/10 group-hover:text-ans-dark-green transition-all">🌐</span>
                    <div>
                        <p class="font-bold text-gray-800 text-sm">Traducir textos de estudio</p>
                        <p class="text-xs text-gray-500 mt-0.5">Comprensión de lectura multilingüe</p>
                    </div>
                </button>

                <button onclick="selectCase('math')" id="btn-math" class="case-item w-full text-left p-4 rounded-2xl border border-gray-200 bg-white hover:border-ans-dark-green/30 flex items-center gap-4 group">
                    <span class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center text-gray-500 group-hover:bg-ans-dark-green/10 group-hover:text-ans-dark-green transition-all">📐</span>
                    <div>
                        <p class="font-bold text-gray-800 text-sm">Resolución de matemáticas paso a paso</p>
                        <p class="text-xs text-gray-500 mt-0.5">Explicación de problemas complejos</p>
                    </div>
                </button>

                <button onclick="selectCase('grammar')" id="btn-grammar" class="case-item w-full text-left p-4 rounded-2xl border border-gray-200 bg-white hover:border-ans-dark-green/30 flex items-center gap-4 group">
                    <span class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center text-gray-500 group-hover:bg-ans-dark-green/10 group-hover:text-ans-dark-green transition-all">✍️</span>
                    <div>
                        <p class="font-bold text-gray-800 text-sm">Corrección ortográfica y gramatical</p>
                        <p class="text-xs text-gray-500 mt-0.5">Pulido de escritos y estilo</p>
                    </div>
                </button>

                <button onclick="selectCase('code')" id="btn-code" class="case-item w-full text-left p-4 rounded-2xl border border-gray-200 bg-white hover:border-ans-dark-green/30 flex items-center gap-4 group">
                    <span class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center text-gray-500 group-hover:bg-ans-dark-green/10 group-hover:text-ans-dark-green transition-all">💻</span>
                    <div>
                        <p class="font-bold text-gray-800 text-sm">Depurar y buscar errores de código</p>
                        <p class="text-xs text-gray-500 mt-0.5">Asistente en proyectos tecnológicos</p>
                    </div>
                </button>
            </div>

            <!-- Columna Derecha: Tarjeta de Veredicto (7 cols) -->
            <div class="lg:col-span-7 bg-white border border-gray-200/60 rounded-3xl p-8 flex flex-col justify-between shadow-sm relative overflow-hidden">
                <!-- Decorative background elements -->
                <div class="absolute -right-12 -top-12 w-48 h-48 bg-ans-dark-green/5 rounded-full blur-2xl"></div>
                
                <div id="verdict-placeholder" class="h-full flex flex-col items-center justify-center text-center p-8 space-y-4">
                    <div class="w-20 h-20 bg-ans-dark-green/5 text-ans-dark-green rounded-full flex items-center justify-center text-4xl animate-bounce">
                        💡
                    </div>
                    <div>
                        <h5 class="text-lg font-bold text-gray-800">¿Tienes dudas sobre un caso práctico?</h5>
                        <p class="text-sm text-gray-500 mt-2 max-w-sm">Haz clic en cualquiera de las situaciones escolares del menú izquierdo para analizar su viabilidad y veredicto bajo las normas de AINS.</p>
                    </div>
                </div>

                <div id="verdict-display" class="hidden h-full flex flex-col justify-between space-y-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Caso Seleccionado</span>
                            <span id="case-verdict-badge" class="px-3.5 py-1 rounded-full text-xs font-extrabold uppercase border">PERMITIDO</span>
                        </div>
                        <h4 id="case-title" class="text-2xl font-heading font-extrabold text-gray-900 leading-tight">Generar ideas para un proyecto</h4>
                        <p id="case-description" class="text-gray-600 text-sm leading-relaxed">Usar la IA para proponer temas, enfoques o lluvias de ideas en proyectos individuales.</p>
                    </div>

                    <div class="bg-gray-50 border border-gray-100 rounded-2xl p-5 space-y-3">
                        <p class="text-xs font-bold text-ans-dark-green uppercase tracking-wider flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Recomendación y Consejos Pedagógicos
                        </p>
                        <p id="case-advice" class="text-xs text-gray-600 leading-relaxed">Excelente uso para romper el bloqueo del escritor. Asegúrate de que las ideas finales sean tuyas y las desarrolles de manera independiente.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Acordeón FAQ (Accordion) -->
    <div class="space-y-6">
        <div class="border-b border-gray-200 pb-4">
            <h3 class="text-2xl font-heading font-extrabold text-gray-800 tracking-tight">
                Preguntas Frecuentes (FAQ)
            </h3>
            <p class="text-gray-500 mt-1">Respuestas a dudas comunes sobre el uso práctico de inteligencia artificial.</p>
        </div>

        <div class="space-y-4 max-w-4xl mx-auto">
            <!-- FAQ 1 -->
            <div class="faq-item bg-white border border-gray-200/70 rounded-2xl overflow-hidden shadow-sm transition-all duration-300">
                <button onclick="toggleFaq(this)" class="w-full px-6 py-5 text-left flex justify-between items-center gap-4">
                    <span class="font-bold text-gray-800 hover:text-ans-dark-green transition-colors text-base">¿Qué pasa si mi profesor encuentra que usé IA sin declarar?</span>
                    <span class="faq-icon w-8 h-8 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400 border border-gray-100 transition-transform duration-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </span>
                </button>
                <div class="faq-answer">
                    <div class="px-6 pb-6 text-sm text-gray-600 leading-relaxed border-t border-gray-50 pt-4">
                        El uso no declarado de herramientas de IA generativa se clasifica como una falta de integridad académica, similar al plagio tradicional. Las consecuencias se aplicarán de acuerdo con el manual de convivencia escolar y el Código de Honor de AINS, lo cual puede incluir la anulación de la calificación y reportes correspondientes.
                    </div>
                </div>
            </div>

            <!-- FAQ 2 -->
            <div class="faq-item bg-white border border-gray-200/70 rounded-2xl overflow-hidden shadow-sm transition-all duration-300">
                <button onclick="toggleFaq(this)" class="w-full px-6 py-5 text-left flex justify-between items-center gap-4">
                    <span class="font-bold text-gray-800 hover:text-ans-dark-green transition-colors text-base">¿Cómo debo citar una herramienta de IA en una tarea?</span>
                    <span class="faq-icon w-8 h-8 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400 border border-gray-100 transition-transform duration-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </span>
                </button>
                <div class="faq-answer">
                    <div class="px-6 pb-6 text-sm text-gray-600 leading-relaxed border-t border-gray-50 pt-4">
                        Debes incluir una mención al final de tu trabajo con los siguientes elementos:
                        <ul class="list-disc pl-5 mt-2 space-y-1.5 text-xs">
                            <li>Nombre del modelo (ej. ChatGPT v4o, Claude 3.5 Sonnet).</li>
                            <li>Fecha en que lo consultaste.</li>
                            <li>El prompt específico que utilizaste.</li>
                            <li>Una breve descripción de cómo te ayudó la IA (ej. "Utilizado para estructurar el bosquejo y corregir errores gramaticales en la sección de introducción").</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- FAQ 3 -->
            <div class="faq-item bg-white border border-gray-200/70 rounded-2xl overflow-hidden shadow-sm transition-all duration-300">
                <button onclick="toggleFaq(this)" class="w-full px-6 py-5 text-left flex justify-between items-center gap-4">
                    <span class="font-bold text-gray-800 hover:text-ans-dark-green transition-colors text-base">¿Los profesores pueden usar software para detectar si usé IA?</span>
                    <span class="faq-icon w-8 h-8 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400 border border-gray-100 transition-transform duration-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </span>
                </button>
                <div class="faq-answer">
                    <div class="px-6 pb-6 text-sm text-gray-600 leading-relaxed border-t border-gray-50 pt-4">
                        Sí, el colegio cuenta con herramientas de detección integradas en plataformas como Google Classroom y Turnitin. Sin embargo, reconocemos que los detectores no son infalibles. El criterio principal para evaluar la integridad de un trabajo será el diálogo directo del docente con el alumno sobre el proceso de creación y comprensión de la tarea.
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
            title: 'Lluvia de ideas para proyectos',
            verdict: 'PERMITIDO',
            badgeClass: 'bg-emerald-100/75 text-emerald-800 border-emerald-200/80',
            description: 'Usar inteligencia artificial generativa para sugerir temáticas, enfoques de investigación, estructuras de bosquejos o ideas iniciales sobre un proyecto académico.',
            advice: 'Excelente herramienta para romper el bloqueo creativo. Asegúrate de desarrollar la propuesta final por tu cuenta y que las ideas reflejen tu perspectiva personal.'
        },
        'writing': {
            title: 'Escribir un ensayo completo',
            verdict: 'NO PERMITIDO',
            badgeClass: 'bg-rose-100/75 text-rose-800 border-rose-200/80',
            description: 'Pedirle a la IA que redacte párrafos completos o la totalidad de un ensayo, reporte de laboratorio, examen o proyecto para entregarlo haciéndolo pasar como trabajo propio sin atribución.',
            advice: 'Constituye una violación directa de las normas de honestidad intelectual del colegio. La redacción, el análisis final y las conclusiones críticas deben ser desarrollados siempre por ti.'
        },
        'translation': {
            title: 'Traducir textos de estudio',
            verdict: 'PERMITIDO',
            badgeClass: 'bg-emerald-100/75 text-emerald-800 border-emerald-200/80',
            description: 'Traducir material bibliográfico técnico, lecturas académicas extensas o artículos periodísticos del inglés a español (o viceversa) para facilitar su estudio y entendimiento.',
            advice: 'Uso productivo de la IA para superar barreras lingüísticas. Si realizas tareas específicas para asignaturas de idiomas (Inglés, Español, Francés), no utilices traductores directos para generar composiciones, ya que bloqueas tu desarrollo de habilidades.'
        },
        'math': {
            title: 'Resolución de matemáticas paso a paso',
            verdict: 'CUIDADO',
            badgeClass: 'bg-amber-100/75 text-amber-800 border-amber-200/80',
            description: 'Pegar un problema de álgebra, física o química para obtener la respuesta numérica y el desarrollo lógico paso a paso resuelto por el modelo.',
            advice: 'Está permitido usarlo con fines de tutoría y autoaprendizaje para comprender la metodología del ejercicio. No obstante, copiar la secuencia de forma exacta en tu hoja de entrega sin ser capaz de recrearla o entender los conceptos viola los objetivos de la materia.'
        },
        'grammar': {
            title: 'Corrección ortográfica y gramatical',
            verdict: 'PERMITIDO',
            badgeClass: 'bg-emerald-100/75 text-emerald-800 border-emerald-200/80',
            description: 'Ingresar tu ensayo redactado previamente para solicitar sugerencias de estilo, mejorar el vocabulario técnico, pulir la coherencia y corregir faltas de ortografía o puntuación.',
            advice: 'Fomenta el pulido y rigor en las entregas. Trata de analizar activamente cada modificación propuesta por el sistema para asimilar las lecciones y escribir mejor en futuras ocasiones.'
        },
        'code': {
            title: 'Depurar y buscar errores de código',
            verdict: 'CUIDADO',
            badgeClass: 'bg-amber-100/75 text-amber-800 border-amber-200/80',
            description: 'Subir fragmentos de código de desarrollo propio para identificar bugs lógicos, falta de sintaxis o librerías desactualizadas en proyectos de tecnología.',
            advice: 'Es un soporte de programación muy enriquecedor. Sin embargo, no delegues la creación entera del algoritmo ni la estructura de software principal, ya que necesitas consolidar el pensamiento de programación básico.'
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
