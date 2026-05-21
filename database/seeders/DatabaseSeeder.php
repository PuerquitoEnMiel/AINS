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
            ['name' => 'AI Detection',     'slug' => 'ai-detection',     'icon' => '🔍', 'color' => '#EF4444', 'sort_order' => 9],
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
                'is_official' => true,
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
                'is_official' => true,
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
                'is_official' => false,
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
                'is_official' => true,
                'approval_status' => 'approved',
                'featured' => false,
                'created_by' => $admin->id,
            ],
            [
                'name' => 'Flow',
                'description' => 'Collaborative tool for planning, organizing, and tracking educational workflows and school tasks at ANS.',
                'url' => 'https://flow.ans.edu.ni',
                'category' => 'Productivity',
                'category_id' => $catId('productivity'),
                'is_google_workspace' => false,
                'is_official' => true,
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
                'is_official' => false,
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
                'is_official' => false,
                'approval_status' => 'approved',
                'featured' => false,
                'created_by' => $admin->id,
            ],
            // ── AI Detection Tools (teacher/admin only) ──────────────
            [
                'name' => 'GPTZero',
                'description' => 'AI detection tool that identifies AI-generated text in student submissions. Analyzes perplexity and burstiness patterns to flag AI-written content.',
                'url' => 'https://gptzero.me',
                'category' => 'AI Detection',
                'category_id' => $catId('ai-detection'),
                'is_google_workspace' => false,
                'is_official' => false,
                'approval_status' => 'approved',
                'featured' => false,
                'created_by' => $admin->id,
            ],
            [
                'name' => 'Turnitin AI',
                'description' => 'Industry-standard academic integrity platform with AI writing detection. Integrates directly with LMS platforms and provides detailed originality reports.',
                'url' => 'https://www.turnitin.com/solutions/ai-writing',
                'category' => 'AI Detection',
                'category_id' => $catId('ai-detection'),
                'is_google_workspace' => false,
                'is_official' => false,
                'approval_status' => 'approved',
                'featured' => false,
                'created_by' => $admin->id,
            ],
            [
                'name' => 'Copyleaks',
                'description' => 'AI-powered plagiarism and AI content detection platform supporting 100+ languages. Detects ChatGPT, Gemini, and other generative AI outputs in student work.',
                'url' => 'https://copyleaks.com',
                'category' => 'AI Detection',
                'category_id' => $catId('ai-detection'),
                'is_google_workspace' => false,
                'is_official' => false,
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

        // ── Badges (admin-managed via CRUD panel) ─────────────────────
        // No seed data. Admin creates certifications from /admin/badges with:
        //   - requires_evidence: true/false
        //   - certification_url: link to official cert program
        //   - evidence_instructions: instructions for teachers
        //   - expires_in_days: null=permanent, 365=1yr, 730=2yr, etc.
        //   - Optional AI quiz via "Generate Quiz with AI" button
    }
}