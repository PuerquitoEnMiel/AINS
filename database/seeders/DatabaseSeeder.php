<?php

namespace Database\Seeders;

use App\Models\Tool;
use App\Models\User;
use App\Models\TaskForceMember;
use App\Models\PromptTip;
use App\Models\Badge;
use App\Models\Quiz;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // ── Categories ──────────────────────────────────────────────
        $categories = [
            ['name' => 'AI Assistants',    'slug' => 'ai-assistants',    'icon' => '🤖', 'color' => '#007934', 'sort_order' => 1],
            ['name' => 'Productivity',     'slug' => 'productivity',     'icon' => '⚡', 'color' => '#FF8300', 'sort_order' => 2],
            ['name' => 'Content Creation', 'slug' => 'content-creation', 'icon' => '🎨', 'color' => '#80BC00', 'sort_order' => 3],
            ['name' => 'Research',         'slug' => 'research',         'icon' => '🔬', 'color' => '#2563EB', 'sort_order' => 4],
            ['name' => 'Music & Audio',    'slug' => 'music-audio',      'icon' => '🎵', 'color' => '#9333EA', 'sort_order' => 5],
            ['name' => 'Presentations',    'slug' => 'presentations',    'icon' => '📊', 'color' => '#DC2626', 'sort_order' => 6],
            ['name' => 'Lesson Planning',  'slug' => 'lesson-planning',  'icon' => '📝', 'color' => '#0891B2', 'sort_order' => 7],
            ['name' => 'Google Workspace', 'slug' => 'google-workspace', 'icon' => '🔷', 'color' => '#4285F4', 'sort_order' => 8],
            ['name' => 'Others',           'slug' => 'others',           'icon' => '📦', 'color' => '#6B7280', 'sort_order' => 99],
        ];

        foreach ($categories as $cat) {
            DB::table('categories')->updateOrInsert(['slug' => $cat['slug']], array_merge($cat, [
                'created_at' => now(), 'updated_at' => now(),
            ]));
        }

        // ── Seed exactly 3 users ───────────────────────────────────

        // 1. Admin
        $admin = User::updateOrCreate(
            ['email' => 'edwin.lopez@ans.edu.ni'],
            [
                'name' => 'Edwin López',
                'role' => 'admin',
                'department' => 'Technology',
                'bio' => 'IT Director & EdTech Lead at ANS.',
                'password' => bcrypt('admin1234'),
            ]
        );

        // 2. Teacher
        $teacher = User::updateOrCreate(
            ['email' => 'teacher@ans.edu.ni'],
            [
                'name' => 'Docente Pruebas',
                'role' => 'teacher',
                'department' => 'Mathematics',
                'bio' => 'Teacher account for testing.',
                'password' => bcrypt('teacher1234'),
            ]
        );

        // 3. Student
        $student = User::updateOrCreate(
            ['email' => 'student@ans.edu.ni'],
            [
                'name' => 'Estudiante Pruebas',
                'role' => 'student',
                'department' => 'High School',
                'bio' => 'Student account for testing.',
                'password' => bcrypt('student1234'),
            ]
        );

        // ── Helper: get category ID by slug ─────────────────────────
        $catId = fn (string $slug) => DB::table('categories')->where('slug', $slug)->value('id');

        // ── Tools ───────────────────────────────────────────────────
        $tools = [
            [
                'name' => 'Gemini',
                'description' => 'Google\'s most capable AI model for reasoning, coding, and creative tasks.',
                'url' => 'https://gemini.google.com',
                'category' => 'AI Assistants',
                'category_id' => $catId('ai-assistants'),
                'is_google_workspace' => true,
                'approval_status' => 'approved',
                'featured' => true,
                'created_by' => $admin->id,
            ],
            [
                'name' => 'ChatGPT',
                'description' => 'OpenAI\'s conversational AI for brainstorming, writing, and problem solving.',
                'url' => 'https://chat.openai.com',
                'category' => 'AI Assistants',
                'category_id' => $catId('ai-assistants'),
                'is_google_workspace' => false,
                'approval_status' => 'approved',
                'featured' => true,
                'created_by' => $admin->id,
            ],
            [
                'name' => 'NotebookLM',
                'description' => 'Google\'s AI-powered research notebook that analyzes documents and generates insights.',
                'url' => 'https://notebooklm.google.com',
                'category' => 'Research',
                'category_id' => $catId('research'),
                'is_google_workspace' => true,
                'approval_status' => 'approved',
                'featured' => true,
                'created_by' => $admin->id,
            ],
            [
                'name' => 'Canva AI',
                'description' => 'Design platform with AI-powered tools for presentations, posters, and visual content.',
                'url' => 'https://www.canva.com',
                'category' => 'Content Creation',
                'category_id' => $catId('content-creation'),
                'is_google_workspace' => false,
                'approval_status' => 'approved',
                'featured' => false,
                'created_by' => $admin->id,
            ],
            [
                'name' => 'Perplexity',
                'description' => 'AI-powered search engine that provides sourced, research-grade answers.',
                'url' => 'https://www.perplexity.ai',
                'category' => 'Research',
                'category_id' => $catId('research'),
                'is_google_workspace' => false,
                'approval_status' => 'approved',
                'featured' => false,
                'created_by' => $admin->id,
            ],
            [
                'name' => 'Suno AI',
                'description' => 'AI music generator that creates songs from text prompts.',
                'url' => 'https://suno.com',
                'category' => 'Music & Audio',
                'category_id' => $catId('music-audio'),
                'is_google_workspace' => false,
                'approval_status' => 'approved',
                'featured' => false,
                'created_by' => $admin->id,
            ],
            [
                'name' => 'Stitch',
                'description' => 'AI-powered lesson planning tool for creating rubrics, outlines, and differentiated lessons.',
                'url' => 'https://www.stitch.ai',
                'category' => 'Lesson Planning',
                'category_id' => $catId('lesson-planning'),
                'is_google_workspace' => false,
                'approval_status' => 'approved',
                'featured' => true,
                'created_by' => $admin->id,
            ],
            [
                'name' => 'Pomelo',
                'description' => 'EdTech automation assistant for administrative workflows and grading.',
                'url' => 'https://www.pomelo.la',
                'category' => 'Productivity',
                'category_id' => $catId('productivity'),
                'is_google_workspace' => false,
                'approval_status' => 'approved',
                'featured' => false,
                'created_by' => $admin->id,
            ],
            [
                'name' => 'Google Slides + Gemini',
                'description' => 'Create AI-enhanced presentations using Gemini integration in Google Slides.',
                'url' => 'https://slides.google.com',
                'category' => 'Presentations',
                'category_id' => $catId('presentations'),
                'is_google_workspace' => true,
                'approval_status' => 'approved',
                'featured' => false,
                'created_by' => $admin->id,
            ],
            [
                'name' => 'Antigravity',
                'description' => 'Custom AI pair-programming assistant and development agent for coding tasks.',
                'url' => 'https://antigravity.dev',
                'category' => 'AI Assistants',
                'category_id' => $catId('ai-assistants'),
                'is_google_workspace' => false,
                'approval_status' => 'approved',
                'featured' => false,
                'created_by' => $admin->id,
            ],
            [
                'name' => 'Gamma',
                'description' => 'AI-powered tool for creating beautiful presentations, documents, and web pages.',
                'url' => 'https://gamma.app',
                'category' => 'Presentations',
                'category_id' => $catId('presentations'),
                'is_google_workspace' => false,
                'approval_status' => 'approved',
                'featured' => false,
                'created_by' => $admin->id,
            ],
            [
                'name' => 'Claude',
                'description' => 'Anthropic\'s AI assistant known for safety, accuracy, and detailed analysis.',
                'url' => 'https://claude.ai',
                'category' => 'AI Assistants',
                'category_id' => $catId('ai-assistants'),
                'is_google_workspace' => false,
                'approval_status' => 'approved',
                'featured' => false,
                'created_by' => $admin->id,
            ],
        ];

        foreach ($tools as $toolData) {
            Tool::updateOrCreate(['name' => $toolData['name']], $toolData);
        }

        // ── Seed Task Force Members ─────────────────────────────────
        $members = [
            [
                'name' => 'Dr. Roberto Silva',
                'role' => 'Director de Innovación',
                'email' => 'roberto.silva@ans.edu.ni',
                'description' => 'Coordinador general del comité. Especialista en integración curricular de tecnologías y marcos de evaluación EdTech.',
                'initials' => 'RS',
                'avatar_color' => '#007934',
                'sort_order' => 1,
            ],
            [
                'name' => 'Ms. Diana Blandón',
                'role' => 'Coordinadora Curricular',
                'email' => 'diana.blandon@ans.edu.ni',
                'description' => 'Enlace pedagógico para secundaria y primaria. Experta en diseño instruccional y metodologías de evaluación activa asistida por IA.',
                'initials' => 'DB',
                'avatar_color' => '#FF8300',
                'sort_order' => 2,
            ],
            [
                'name' => 'Mr. Sergio Torres',
                'role' => 'Director de TI',
                'email' => 'sergio.torres@ans.edu.ni',
                'description' => 'Responsable técnico. Lidera el análisis de privacidad de datos de las plataformas de IA externas y la seguridad informática escolar.',
                'initials' => 'ST',
                'avatar_color' => '#2563EB',
                'sort_order' => 3,
            ],
            [
                'name' => 'Dr. Elena Rostrán',
                'role' => 'Asesora de Ética',
                'email' => 'elena.rostran@ans.edu.ni',
                'description' => 'Asesora pedagógica y de humanidades. Responsable de estructurar directrices éticas y promover el pensamiento crítico en la era digital.',
                'initials' => 'ER',
                'avatar_color' => '#9333EA',
                'sort_order' => 4,
            ],
        ];

        foreach ($members as $memberData) {
            TaskForceMember::updateOrCreate(['email' => $memberData['email']], $memberData);
        }

        // ── Seed Prompt Tips ────────────────────────────────────────
        $prompts = [
            [
                'title' => 'Plan de Lección Estructurado',
                'target_role' => 'docentes',
                'category' => 'Planificación',
                'complexity' => 'Media',
                'description' => 'Genera una clase detallada de 60 minutos con objetivos claros de la Taxonomía de Bloom, cierre formativo e instrucciones de diferenciación.',
                'prompt_text' => 'Actúa como un diseñador instruccional experto. Diseña un plan de lección de 60 minutos para estudiantes de [Nivel/Grado] sobre el tema: [Tema]. Incluye un objetivo de aprendizaje claro usando la Taxonomía de Bloom, una actividad introductoria de 10 minutos, una lección guiada de 20 minutos, trabajo práctico en equipos de 20 minutos y un cierre de evaluación formativa de 10 minutos. Sugiere también recursos didácticos y cómo adaptar la clase para estudiantes con necesidades de aprendizaje diversas.',
                'sort_order' => 1,
            ],
            [
                'title' => 'Creación de Rúbrica Analítica',
                'target_role' => 'docentes',
                'category' => 'Planificación',
                'complexity' => 'Alta',
                'description' => 'Crea una rúbrica estructurada en formato de tabla con niveles de logro y descriptores detallados para cualquier tipo de proyecto escolar.',
                'prompt_text' => 'Genera una rúbrica de evaluación analítica en formato de tabla para un proyecto de [Tipo de Proyecto] en la materia de [Asignatura] para estudiantes de [Nivel]. La rúbrica debe evaluar 4 criterios clave: [Criterio 1, Criterio 2, Criterio 3, Criterio 4]. Utiliza 4 niveles de logro: Excelente, Satisfactorio, En Desarrollo e Insuficiente. Describe con precisión los descriptores de desempeño para cada nivel y asigna ponderaciones porcentuales claras a cada criterio.',
                'sort_order' => 2,
            ],
            [
                'title' => 'Diseñar Actividades Rompehielos',
                'target_role' => 'docentes',
                'category' => 'Planificación',
                'complexity' => 'Baja',
                'description' => 'Crea dinámicas cortas de inicio de clase de 5-10 minutos relacionadas con un tema específico para captar la atención de los estudiantes.',
                'prompt_text' => 'Proporciona 3 opciones de actividades rompehielos (icebreakers) creativas de 5 a 10 minutos para iniciar una clase sobre [Tema] con estudiantes de [Nivel]. Las actividades deben ser interactivas, requerir pocos materiales y motivar el interés de los alumnos por el concepto del día.',
                'sort_order' => 3,
            ],
            [
                'title' => 'Feedback del Sándwich Pedagógico',
                'target_role' => 'docentes',
                'category' => 'Feedback',
                'complexity' => 'Media',
                'description' => 'Crea críticas y sugerencias de mejora empáticas y constructivas a partir de borradores o respuestas enviadas por estudiantes.',
                'prompt_text' => 'Aquí tienes el borrador escrito por un estudiante sobre [Tema]: \'[Insertar borrador del alumno]\'. Actúa como un tutor empático y brinda retroalimentación formativa y detallada. Utiliza la estrategia del \'sándwich de retroalimentación\' (destaca una fortaleza, señala áreas específicas de mejora con ejemplos correctos y termina con un mensaje motivador de crecimiento). No reescribas el borrador, solo guía al alumno sobre cómo mejorar su propia redacción.',
                'sort_order' => 4,
            ],
            [
                'title' => 'Crear Andamiajes de Lectura',
                'target_role' => 'docentes',
                'category' => 'Feedback',
                'complexity' => 'Alta',
                'description' => 'Simplifica un texto académico complejo y genera preguntas de andamiaje para ayudar a estudiantes que tienen dificultades de comprensión de lectura.',
                'prompt_text' => 'Analiza el siguiente texto complejo: \'[Insertar texto]\'. Genera: 1) Una versión simplificada de un párrafo para estudiantes de nivel introductorio, 2) Una lista con los 3 términos técnicos clave definidos de forma muy sencilla, y 3) Dos preguntas de comprensión lectora con respuestas modelo de guía para andamiar su lectura.',
                'sort_order' => 5,
            ],
            [
                'title' => 'Tutor Socrático de Apoyo',
                'target_role' => 'estudiantes',
                'category' => 'Tutoría',
                'complexity' => 'Media',
                'description' => 'Configura un bot de estudio interactivo que te haga preguntas en lugar de darte las soluciones directas a tus tareas matemáticas.',
                'prompt_text' => 'Quiero que actúes como un tutor socrático de matemáticas. No me des las respuestas directas a mis preguntas. En su lugar, hazme preguntas guía cortas y sencillas que me ayuden a descubrir la respuesta por mí mismo de manera lógica. Comencemos con el siguiente ejercicio que no comprendo bien: [Escribir tu ecuación o problema matemático aquí].',
                'sort_order' => 6,
            ],
            [
                'title' => 'Analogías Conceptuales',
                'target_role' => 'estudiantes',
                'category' => 'Tutoría',
                'complexity' => 'Baja',
                'description' => 'Obtén analogías y explicaciones ilustrativas de conceptos científicos o abstractos que te resulten difíciles de comprender.',
                'prompt_text' => 'Estoy estudiando el concepto de [Concepto complejo (ej. Mitosis celular, inflación económica)] en la materia de [Asignatura] y me cuesta comprenderlo a fondo. Por favor, explícame este concepto utilizando una analogía vívida y cotidiana de la vida real. Divide la explicación en 3 analogías comparativas y concluye con un resumen súper simplificado de una sola oración para memorizarlo fácilmente.',
                'sort_order' => 7,
            ],
            [
                'title' => 'Generador de Quizzes Interactivos',
                'target_role' => 'estudiantes',
                'category' => 'Repaso',
                'complexity' => 'Alta',
                'description' => 'Simula un evaluador riguroso que te haga preguntas de opción múltiple una por una, dando feedback tras cada respuesta.',
                'prompt_text' => 'Actúa como un evaluador exigente de la asignatura [Asignatura]. Genera un examen corto de práctica de 5 preguntas de opción múltiple y respuesta única sobre el tema: [Tema de examen]. No me reveles las respuestas de inmediato. Muéstrame las preguntas una por una, espera a que yo te escriba mi opción de respuesta (A, B, C, D) y luego dime si mi respuesta fue correcta o incorrecta junto con una explicación de 2 oraciones, antes de pasar a la siguiente pregunta.',
                'sort_order' => 8,
            ],
            [
                'title' => 'Simulación de Debates Temáticos',
                'target_role' => 'estudiantes',
                'category' => 'Repaso',
                'complexity' => 'Media',
                'description' => 'Entrena para debates o presentaciones pidiéndole a la IA que asuma una postura contraria para evaluar tu argumentación crítica.',
                'prompt_text' => 'Quiero debatir contigo de forma constructiva sobre el siguiente tema académico: [Tema de Debate]. Asume la postura contraria a la mía y presenta argumentos sólidos para retar mis opiniones. Inicia el debate presentándome una postura contraria de un párrafo y hazme una pregunta de seguimiento crítica para que yo la responda.',
                'sort_order' => 9,
            ],
        ];

        foreach ($prompts as $promptData) {
            PromptTip::updateOrCreate(['title' => $promptData['title']], $promptData);
        }

        // ── Seed Badges and Quizzes ─────────────────────────────────
        $badgesData = [
            [
                'name' => 'Canva Classroom Pro',
                'slug' => 'canva-classroom-pro',
                'description' => 'Demuestra dominio en el uso de Canva AI para crear presentaciones interactivas y recursos de diseño educativo.',
                'icon' => '🎨',
                'color' => '#7D2AE8',
                'category' => 'tool_mastery',
                'difficulty' => 'bronze',
                'criteria_type' => 'quiz',
                'sort_order' => 1,
                'quiz' => [
                    'title' => 'Cuestionario: Canva Classroom Pro',
                    'description' => 'Demuestra tus conocimientos sobre el uso de las herramientas de inteligencia artificial integradas en Canva.',
                    'passing_score' => 80,
                    'questions' => [
                        [
                            'question' => '¿Cuál es el propósito principal de la herramienta "Texto a Imagen" (Text to Image) en Canva?',
                            'options' => [
                                'a' => 'Traducir texto a otros idiomas automáticamente.',
                                'b' => 'Generar imágenes personalizadas a partir de una descripción escrita.',
                                'c' => 'Corregir errores ortográficos en los diseños.',
                                'd' => 'Convertir un documento PDF en una presentación.'
                            ],
                            'correct' => 'b',
                            'explanation' => 'La herramienta "Texto a Imagen" permite a los usuarios escribir una descripción de lo que quieren ver y la IA genera imágenes basadas en esa descripción.'
                        ],
                        [
                            'question' => 'Para mantener la consistencia visual en un diseño de clase utilizando Canva, ¿qué herramienta de IA es la más adecuada?',
                            'options' => [
                                'a' => 'Traducir',
                                'b' => 'Borrador Mágico',
                                'c' => 'Kit de Marca (Brand Kit)',
                                'd' => 'Rediseño Mágico'
                            ],
                            'correct' => 'c',
                            'explanation' => 'El Kit de Marca permite predefinir colores, fuentes y logotipos institucionales para asegurar la consistencia.'
                        ],
                        [
                            'question' => '¿Qué funcionalidad realiza "Escritura Mágica" (Magic Write) en Canva?',
                            'options' => [
                                'a' => 'Transcribir grabaciones de voz a texto.',
                                'b' => 'Generar o reescribir copys, ideas y contenidos de texto mediante IA.',
                                'c' => 'Crear firmas digitales animadas.',
                                'd' => 'Traducir texto automáticamente a lenguaje de señas.'
                            ],
                            'correct' => 'b',
                            'explanation' => 'Escritura Mágica es el asistente de redacción con IA de Canva que ayuda a generar ideas, resumir o redactar textos.'
                        ],
                        [
                            'question' => 'Si desea eliminar un elemento no deseado (como un objeto de fondo) de una foto en Canva, ¿qué herramienta debe usar?',
                            'options' => [
                                'a' => 'Borrador Mágico',
                                'b' => 'Edición Mágica',
                                'c' => 'Filtro Fotográfico',
                                'd' => 'Recortar Imagen'
                            ],
                            'correct' => 'a',
                            'explanation' => 'El Borrador Mágico de Canva utiliza IA para rellenar de forma coherente el espacio de un objeto de fondo eliminado.'
                        ],
                        [
                            'question' => 'Al usar "Rediseño Mágico" (Magic Switch), ¿qué transformación NO es posible realizar directamente?',
                            'options' => [
                                'a' => 'Traducir la presentación completa a otro idioma.',
                                'b' => 'Transformar la presentación en un documento de resumen de texto.',
                                'c' => 'Cambiar las dimensiones del diseño a formato de folleto o red social.',
                                'd' => 'Exportar el diseño como un video musical en formato MP3 de audio únicamente.'
                            ],
                            'correct' => 'd',
                            'explanation' => 'Rediseño Mágico traduce, cambia dimensiones y convierte formatos de documento, pero no puede exportar videos como archivos puramente de música MP3.'
                        ]
                    ]
                ]
            ],
            [
                'name' => 'AI Safety Champion',
                'slug' => 'ai-safety-champion',
                'description' => 'Domina las pautas de privacidad de datos, el consentimiento estudiantil y el uso seguro y ético de la inteligencia artificial.',
                'icon' => '🛡️',
                'color' => '#007934',
                'category' => 'ai_safety',
                'difficulty' => 'silver',
                'criteria_type' => 'quiz',
                'sort_order' => 2,
                'quiz' => [
                    'title' => 'Cuestionario: AI Safety Champion',
                    'description' => 'Demuestra tus conocimientos sobre ética, privacidad de datos y seguridad en el uso de IA generativa.',
                    'passing_score' => 80,
                    'questions' => [
                        [
                            'question' => 'Según las directrices éticas de AINS, ¿cuál es la regla de oro sobre los datos personales de los estudiantes?',
                            'options' => [
                                'a' => 'Se pueden ingresar libremente en cualquier IA si el correo es institucional.',
                                'b' => 'NUNCA se deben introducir nombres completos, ID escolares o datos confidenciales de estudiantes en IAs de uso público.',
                                'c' => 'Se pueden compartir solo si la IA tiene versión de pago.',
                                'd' => 'Los docentes pueden compartirlos si los estudiantes firman un descargo de responsabilidad verbal.'
                            ],
                            'correct' => 'b',
                            'explanation' => 'Para proteger la privacidad y cumplir con regulaciones como FERPA/COPPA, no se deben subir datos identificables de estudiantes a modelos públicos.'
                        ],
                        [
                            'question' => '¿Qué significa que un modelo de IA "alucine"?',
                            'options' => [
                                'a' => 'Que genera respuestas extremadamente rápidas.',
                                'b' => 'Que presenta información falsa o incorrecta de manera convincente y segura.',
                                'c' => 'Que tiene animaciones coloridas en su interfaz.',
                                'd' => 'Que requiere reiniciar la conexión a internet.'
                            ],
                            'correct' => 'b',
                            'explanation' => 'Las alucinaciones ocurren cuando la IA inventa datos o hechos falsos que parecen lógicos y correctos.'
                        ],
                        [
                            'question' => 'Al usar herramientas de IA generativa para calificar tareas, ¿cuál es el rol ético del docente?',
                            'options' => [
                                'a' => 'Copiar y pegar directamente el puntaje de la IA como calificación definitiva.',
                                'b' => 'Usar la IA solo como asistencia o segundo par de ojos, pero el docente siempre debe revisar y validar la calificación final.',
                                'c' => 'Dejar que los alumnos califiquen con su propia IA y promediar.',
                                'd' => 'Prohibir el uso de IA en la evaluación por completo sin excepciones.'
                            ],
                            'correct' => 'b',
                            'explanation' => 'El criterio humano del docente es insustituible para asegurar la equidad y evitar sesgos o errores cometidos por la IA.'
                        ],
                        [
                            'question' => '¿Cuál es la forma correcta de abordar el uso no autorizado de IA por parte de un estudiante en un ensayo?',
                            'options' => [
                                'a' => 'Asignar automáticamente una calificación de cero y suspender al estudiante sin diálogo.',
                                'b' => 'Usar un detector de IA y confiar 100% en su porcentaje de detección.',
                                'c' => 'Conversar con el estudiante sobre el proceso de escritura de su ensayo, sus ideas y la discrepancia detectada.',
                                'd' => 'Ignorar el caso para evitar confrontaciones.'
                            ],
                            'correct' => 'c',
                            'explanation' => 'Los detectores de IA tienen altas tasas de falsos positivos. La mejor validación es una conversación directa y socrática con el estudiante sobre su trabajo.'
                        ],
                        [
                            'question' => '¿Qué tipo de información se considera segura para compartir con una IA generativa?',
                            'options' => [
                                'a' => 'Reportes de notas individuales con nombres de los alumnos.',
                                'b' => 'Planes de estudio generales, objetivos de clase y textos de dominio público.',
                                'c' => 'Actas de reuniones de la junta directiva de la escuela.',
                                'd' => 'Correos electrónicos privados de padres de familia.'
                            ],
                            'correct' => 'b',
                            'explanation' => 'Los contenidos curriculares generales y no confidenciales son completamente seguros para ser andamiados o analizados con IA.'
                        ]
                    ]
                ]
            ],
            [
                'name' => 'Prompt Engineer',
                'slug' => 'prompt-engineer',
                'description' => 'Diseña instrucciones avanzadas usando técnicas de rol, andamiaje y estructuración para guiar efectivamente a los modelos de lenguaje.',
                'icon' => '⚡',
                'color' => '#FF8300',
                'category' => 'pedagogy',
                'difficulty' => 'gold',
                'criteria_type' => 'quiz',
                'sort_order' => 3,
                'quiz' => [
                    'title' => 'Cuestionario: Prompt Engineer',
                    'description' => 'Evalúa tu habilidad para formular y estructurar prompts altamente efectivos y precisos.',
                    'passing_score' => 80,
                    'questions' => [
                        [
                            'question' => '¿En qué consiste la técnica de "Few-Shot Prompting" (Prompting de pocos ejemplos)?',
                            'options' => [
                                'a' => 'Escribir el prompt lo más corto posible.',
                                'b' => 'Darle a la IA uno o más ejemplos del formato o respuesta deseada antes de pedirle el resultado final.',
                                'c' => 'Intentar varias veces el mismo prompt en diferentes chats.',
                                'd' => 'Limitar la respuesta de la IA a pocas oraciones.'
                            ],
                            'correct' => 'b',
                            'explanation' => 'Proporcionar ejemplos (shots) le permite a la IA entender el patrón y la estructura del output que se espera de ella.'
                        ],
                        [
                            'question' => '¿Cuál es el beneficio de asignar un "Rol" (por ejemplo: "Actúa como un profesor de física de secundaria") en un prompt?',
                            'options' => [
                                'a' => 'Hace que la respuesta sea más informal y graciosa.',
                                'b' => 'Limita la base de conocimiento y enfoca el tono, vocabulario y perspectiva de la respuesta del modelo.',
                                'c' => 'Aumenta la velocidad de generación del texto.',
                                'd' => 'Permite saltarse los filtros de seguridad del modelo.'
                            ],
                            'correct' => 'b',
                            'explanation' => 'Definir un rol ayuda al modelo a contextualizar la respuesta en un dominio de conocimiento y estilo comunicativo específico.'
                        ],
                        [
                            'question' => '¿Qué instrucción en un prompt ayuda a reducir las alucinaciones al pedir información histórica o de investigación?',
                            'options' => [
                                'a' => '"Sé creativo y añade detalles ficticios interesantes".',
                                'b' => '"Si no sabes la respuesta o no tienes fuentes confiables, indícalo claramente y di -No sé-".',
                                'c' => '"Escribe la respuesta en menos de 10 palabras".',
                                'd' => '"Traduce la respuesta al inglés antes de mostrarla".'
                            ],
                            'correct' => 'b',
                            'explanation' => 'Explicitar la instrucción de no inventar y admitir la falta de información disminuye la propensión del modelo a alucinar.'
                        ],
                        [
                            'question' => '¿Cuál es la estructura recomendada para un prompt efectivo según las mejores prácticas?',
                            'options' => [
                                'a' => 'Título del tema únicamente.',
                                'b' => 'Rol + Contexto + Tarea + Restricciones + Formato de salida deseado.',
                                'c' => 'Una sola palabra clave.',
                                'd' => 'Una pregunta de sí o no sin detalles.'
                            ],
                            'correct' => 'b',
                            'explanation' => 'Darle rol, contexto, tarea clara, límites y formato esperado maximiza la precisión del resultado obtenido.'
                        ],
                        [
                            'question' => 'Al usar "Chain-of-Thought" (Cadena de pensamiento) en un prompt, le pedimos al modelo que:',
                            'options' => [
                                'a' => 'Muestre su respuesta paso a paso explicando su razonamiento antes del resultado final.',
                                'b' => 'Envíe la respuesta en formato de lista desordenada.',
                                'c' => 'Enlace diferentes chats entre sí.',
                                'd' => 'Genere ideas de forma aleatoria.'
                            ],
                            'correct' => 'a',
                            'explanation' => 'Pedir al modelo que razone paso a paso mejora drásticamente su precisión en tareas lógicas, matemáticas y analíticas complejas.'
                        ]
                    ]
                ]
            ],
            [
                'name' => 'SAMR Navigator',
                'slug' => 'samr-navigator',
                'description' => 'Aplica el modelo SAMR para redefinir y transformar tareas de aprendizaje tradicionales mediante tecnología de manera efectiva.',
                'icon' => '🧭',
                'color' => '#2563EB',
                'category' => 'pedagogy',
                'difficulty' => 'silver',
                'criteria_type' => 'quiz',
                'sort_order' => 4,
                'quiz' => [
                    'title' => 'Cuestionario: SAMR Navigator',
                    'description' => 'Demuestra tu comprensión de los cuatro niveles del modelo SAMR aplicados al diseño instruccional.',
                    'passing_score' => 80,
                    'questions' => [
                        [
                            'question' => '¿Qué significan las siglas del modelo SAMR?',
                            'options' => [
                                'a' => 'Seguridad, Adaptación, Mediación, Redefinición.',
                                'b' => 'Sustitución, Aumento, Modificación, Redefinición.',
                                'c' => 'Software, Aula, Métodos, Resultados.',
                                'd' => 'Soporte, Aprendizaje, Motivación, Razón.'
                            ],
                            'correct' => 'b',
                            'explanation' => 'El modelo SAMR, creado por Ruben Puentedura, se compone de Sustitución, Aumento, Modificación y Redefinición.'
                        ],
                        [
                            'question' => 'Si un estudiante escribe un ensayo en Google Docs en lugar de usar papel y lápiz sin agregar ninguna otra funcionalidad digital, ¿en qué nivel de SAMR está?',
                            'options' => [
                                'a' => 'Sustitución',
                                'b' => 'Aumento',
                                'c' => 'Modificación',
                                'd' => 'Redefinición'
                            ],
                            'correct' => 'a',
                            'explanation' => 'Es Sustitución porque la tecnología actúa como un sustituto directo de la herramienta analógica, sin ningún cambio funcional.'
                        ],
                        [
                            'question' => '¿Cuál es la diferencia clave entre la fase de "Mejora" y la de "Transformación" en el modelo SAMR?',
                            'options' => [
                                'a' => 'La mejora no requiere internet y la transformación sí.',
                                'b' => 'La mejora incluye Sustitución y Aumento; la transformación incluye Modificación y Redefinición, cambiando la naturaleza misma de la tarea.',
                                'c' => 'La mejora es solo para los profesores y la transformación para los estudiantes.',
                                'd' => 'No hay diferencia real, son sinónimos.'
                            ],
                            'correct' => 'b',
                            'explanation' => 'Modificación y Redefinición (Transformación) rediseñan significativamente o crean nuevas tareas de aprendizaje previamente inconcebibles.'
                        ],
                        [
                            'question' => 'Hacer que los alumnos colaboren en tiempo real en una presentación compartida y utilicen comentarios integrados para co-evaluarse corresponde a:',
                            'options' => [
                                'a' => 'Sustitución',
                                'b' => 'Aumento',
                                'c' => 'Modificación',
                                'd' => 'Redefinición'
                            ],
                            'correct' => 'c',
                            'explanation' => 'Es Modificación porque el trabajo cooperativo y el feedback síncrono cambian funcionalmente la dinámica y el alcance de la tarea.'
                        ],
                        [
                            'question' => '¿Cuál de los siguientes escenarios representa el nivel de "Redefinición"?',
                            'options' => [
                                'a' => 'Los estudiantes leen un PDF en lugar de un libro impreso.',
                                'b' => 'Los estudiantes graban un podcast entrevistando a expertos globales y lo publican para recibir comentarios del mundo real.',
                                'c' => 'Los estudiantes hacen una prueba de opción múltiple digital en Google Forms.',
                                'd' => 'Los estudiantes buscan definiciones en Google Diccionario.'
                            ],
                            'correct' => 'b',
                            'explanation' => 'Es Redefinición porque publicar un producto multimedia interactivo y colaborar a escala global crea una tarea de aprendizaje imposible de replicar sin tecnología.'
                        ]
                    ]
                ]
            ],
            [
                'name' => 'Google Workspace Guru',
                'slug' => 'google-workspace-guru',
                'description' => 'Maximiza el potencial educativo de Documentos, Presentaciones y Hojas de cálculo integrando Gemini de forma nativa.',
                'icon' => '🔷',
                'color' => '#4285F4',
                'category' => 'tool_mastery',
                'difficulty' => 'gold',
                'criteria_type' => 'quiz',
                'sort_order' => 5,
                'quiz' => [
                    'title' => 'Cuestionario: Google Workspace Guru',
                    'description' => 'Demuestra tus destrezas para integrar la IA de Google en la suite de productividad escolar.',
                    'passing_score' => 80,
                    'questions' => [
                        [
                            'question' => '¿Qué herramienta de Google Workspace permite escribir correos electrónicos redactados por IA directamente desde la interfaz?',
                            'options' => [
                                'a' => 'Google Keep',
                                'b' => 'Gmail (Ayúdame a escribir)',
                                'c' => 'Google Sites',
                                'd' => 'Google Drive'
                            ],
                            'correct' => 'b',
                            'explanation' => '"Help me write" (Ayúdame a escribir) es la función integrada en Gmail y Google Docs para redactar borradores automáticos con IA.'
                        ],
                        [
                            'question' => 'Al usar la integración de Gemini en Google Sheets, ¿cuál es una función principal de IA disponible?',
                            'options' => [
                                'a' => 'Generar música de fondo para las hojas.',
                                'b' => 'Ayudar a organizar datos creando planes de proyectos y listas personalizadas.',
                                'c' => 'Traducir fórmulas de Excel al español automáticamente.',
                                'd' => 'Hacer streaming de video desde las celdas.'
                            ],
                            'correct' => 'b',
                            'explanation' => 'Gemini en Sheets ayuda a estructurar datos, generar plantillas de tablas e identificar patrones o resúmenes de información.'
                        ],
                        [
                            'question' => '¿Cómo puede ayudar Gemini en Google Slides a un docente que prepara material visual?',
                            'options' => [
                                'a' => 'Creando narraciones en audio en múltiples acentos.',
                                'b' => 'Generando imágenes personalizadas para las diapositivas a partir de indicaciones descriptivas.',
                                'c' => 'Calificando las presentaciones de los estudiantes.',
                                'd' => 'Imprimiendo folletos físicos automáticamente.'
                            ],
                            'correct' => 'b',
                            'explanation' => 'La integración de Gemini en Slides permite crear imágenes temáticas para ilustrar y enriquecer las presentaciones educativas.'
                        ],
                        [
                            'question' => 'Para resumir un documento largo de Google Docs con la ayuda de la IA integrada, ¿qué debe hacer?',
                            'options' => [
                                'a' => 'Copiar todo el texto y abrir una pestaña externa de Google Translate.',
                                'b' => 'Usar el panel lateral de Gemini en Google Docs y seleccionar la opción "Resumir este documento".',
                                'c' => 'Convertir el archivo a PDF y enviarlo por correo.',
                                'd' => 'Dictarle por voz el documento a Google Keep.'
                            ],
                            'correct' => 'b',
                            'explanation' => 'El panel lateral de Gemini en Google Docs ofrece resúmenes en un solo clic del archivo activo que se está visualizando.'
                        ],
                        [
                            'question' => '¿Cuál de las siguientes acciones NO cumple con las pautas de uso seguro en Google Workspace para Educación?',
                            'options' => [
                                'a' => 'Generar ejemplos de problemas de física en Docs con IA.',
                                'b' => 'Ingresar las notas acumuladas y el historial médico de un estudiante en un prompt público de Gemini.',
                                'c' => 'Escribir una plantilla de correo a padres de familia pidiendo disculpas por un retraso.',
                                'd' => 'Traducir un texto escolar del español al inglés usando la barra lateral.'
                            ],
                            'correct' => 'b',
                            'explanation' => 'Subir información confidencial e identificable de salud o notas de los alumnos vulnera las políticas de protección escolar.'
                        ]
                    ]
                ]
            ],
            [
                'name' => 'EdTech Explorer',
                'slug' => 'edtech-explorer',
                'description' => 'Otorgado al explorar la plataforma AINS, guardando tus herramientas favoritas e interactuando con la biblioteca de prompts.',
                'icon' => '🚀',
                'color' => '#9333EA',
                'category' => 'platform',
                'difficulty' => 'bronze',
                'criteria_type' => 'quiz',
                'sort_order' => 6,
                'quiz' => [
                    'title' => 'Cuestionario: EdTech Explorer',
                    'description' => 'Una trivia rápida sobre las características principales y utilidades del portal AINS.',
                    'passing_score' => 80,
                    'questions' => [
                        [
                            'question' => '¿Cuál es el objetivo principal del portal AINS?',
                            'options' => [
                                'a' => 'Ser un catálogo de juegos para estudiantes.',
                                'b' => 'Centralizar, evaluar y promover el uso ético y efectivo de herramientas de Inteligencia Artificial para docentes de ANS.',
                                'c' => 'Vender licencias de software comercial.',
                                'd' => 'Subplantar las clases presenciales por clases 100% virtuales.'
                            ],
                            'correct' => 'b',
                            'explanation' => 'AINS es el portal oficial del American Nicaraguan School para guiar e inspirar la integración pedagógica de la Inteligencia Artificial.'
                        ],
                        [
                            'question' => '¿Qué sección de AINS permite guardar herramientas para verlas rápidamente más tarde?',
                            'options' => [
                                'a' => 'AI School Policy',
                                'b' => 'My Favorites (Favoritos)',
                                'c' => 'AI Task Force',
                                'd' => 'Suggest Tool'
                            ],
                            'correct' => 'b',
                            'explanation' => 'Los usuarios pueden marcar cualquier herramienta con la estrella para agregarla a su sección personal de Favoritos.'
                        ],
                        [
                            'question' => '¿Qué se puede hacer en la sección "EdTech Prompt Tips"?',
                            'options' => [
                                'a' => 'Descargar plantillas de prompts de IA probadas y enviar prompts propios para compartirlos con la comunidad.',
                                'b' => 'Comprar hardware educativo.',
                                'c' => 'Solicitar vacaciones o permisos escolares.',
                                'd' => 'Chatear de forma anónima con otros estudiantes.'
                            ],
                            'correct' => 'a',
                            'explanation' => 'La biblioteca colaborativa recopila prompts educativos efectivos categorizados por complejidad y público objetivo.'
                        ],
                        [
                            'question' => '¿Quiénes componen el "AI Task Force" en ANS?',
                            'options' => [
                                'a' => 'Un grupo de estudiantes de primaria.',
                                'b' => 'Un comité de líderes educativos y tecnólogos de ANS encargados de formular directrices de IA y evaluar herramientas.',
                                'c' => 'Una empresa externa de consultoría de software.',
                                'd' => 'Los desarrolladores del navegador web.'
                            ],
                            'correct' => 'b',
                            'explanation' => 'El AI Task Force es el comité interno que dirige la estrategia y gobernanza de la IA en el colegio.'
                        ],
                        [
                            'question' => 'Si un docente no encuentra una herramienta de IA útil en el catálogo de AINS, ¿qué opción tiene?',
                            'options' => [
                                'a' => 'No puede hacer nada, el catálogo es cerrado.',
                                'b' => 'Utilizar el botón "Suggest Tool" para proponer la herramienta y que el Task Force la evalúe.',
                                'c' => 'Hackear el servidor para agregarla.',
                                'd' => 'Crear una petición en Change.org.'
                            ],
                            'correct' => 'b',
                            'explanation' => 'AINS fomenta la participación docente a través de sugerencias de herramientas ("Suggest Tool"), las cuales son aprobadas por el administrador.'
                        ]
                    ]
                ]
            ]
        ];

        foreach ($badgesData as $bData) {
            $quizData = $bData['quiz'] ?? null;
            unset($bData['quiz']);

            $badge = Badge::updateOrCreate(['slug' => $bData['slug']], $bData);

            if ($quizData && $bData['criteria_type'] === 'quiz') {
                Quiz::updateOrCreate(
                    ['badge_id' => $badge->id],
                    [
                        'title' => $quizData['title'],
                        'description' => $quizData['description'],
                        'questions' => $quizData['questions'],
                        'passing_score' => $quizData['passing_score'],
                    ]
                );
            }
        }
    }
}
