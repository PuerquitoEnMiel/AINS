<?php

namespace Database\Seeders;

use App\Models\Tool;
use App\Models\User;
use App\Models\TaskForceMember;
use App\Models\PromptTip;
use App\Models\Badge;
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
                'name' => 'Test Teacher',
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
                'name' => 'Test Student',
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
                'role' => 'Director of Innovation',
                'email' => 'roberto.silva@ans.edu.ni',
                'description' => 'General committee coordinator. Specialist in curriculum integration of technologies and EdTech evaluation frameworks.',
                'initials' => 'RS',
                'avatar_color' => '#007934',
                'sort_order' => 1,
            ],
            [
                'name' => 'Ms. Diana Blandón',
                'role' => 'Curriculum Coordinator',
                'email' => 'diana.blandon@ans.edu.ni',
                'description' => 'Pedagogical liaison for middle and elementary school. Expert in instructional design and active AI-assisted assessment methodologies.',
                'initials' => 'DB',
                'avatar_color' => '#FF8300',
                'sort_order' => 2,
            ],
            [
                'name' => 'Mr. Sergio Torres',
                'role' => 'Director of IT',
                'email' => 'sergio.torres@ans.edu.ni',
                'description' => 'Technical lead. Leads data privacy analysis for external AI platforms and school IT security.',
                'initials' => 'ST',
                'avatar_color' => '#2563EB',
                'sort_order' => 3,
            ],
            [
                'name' => 'Dr. Elena Rostrán',
                'role' => 'Ethics Advisor',
                'email' => 'elena.rostran@ans.edu.ni',
                'description' => 'Pedagogical and humanities advisor. Responsible for structuring ethical guidelines and promoting critical thinking in the digital era.',
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
                'title' => 'Structured Lesson Plan',
                'target_role' => 'docentes',
                'category' => 'Planning',
                'complexity' => 'Media',
                'description' => 'Generates a detailed 60-minute lesson with clear Bloom\'s Taxonomy objectives, formative assessment closure, and differentiation instructions.',
                'prompt_text' => 'Act a an expert instructional designer. Design a 60-minute lesson plan for [Level/Grade] students on the topic: [Topic]. Include a clear learning objective using Bloom\'s Taxonomy, a 10-minute introductory activity, a 20-minute guided lesson, 20 minutes of practical team work, and a 10-minute formative assessment closure. Also suggest instructional resources and how to adapt the lesson for students with diverse learning needs.',
                'sort_order' => 1,
            ],
            [
                'title' => 'Analytical Rubric Creation',
                'target_role' => 'docentes',
                'category' => 'Planning',
                'complexity' => 'Alta',
                'description' => 'Creates a structured rubric in table format with achievement levels and detailed descriptors for any type of school project.',
                'prompt_text' => 'Generate an analytical assessment rubric in table format for a [Type of Project] project in the subject of [Subject] for [Level] students. The rubric must assess 4 key criteria: [Criterion 1, Criterion 2, Criterion 3, Criterion 4]. Use 4 achievement levels: Excellent, Satisfactory, Developing, and Insufficient. Accurately describe performance descriptors for each level and assign clear percentage weights to each criterion.',
                'sort_order' => 2,
            ],
            [
                'title' => 'Design Icebreaker Activities',
                'target_role' => 'docentes',
                'category' => 'Planning',
                'complexity' => 'Baja',
                'description' => 'Creates short 5-10 minute lesson starters related to a specific topic to capture students\' attention.',
                'prompt_text' => 'Provide 3 creative 5 to 10-minute icebreaker activity options to start a class on [Topic] with [Level] students. Activities should be interactive, require minimal materials, and engage students\' interest in the concept of the day.',
                'sort_order' => 3,
            ],
            [
                'title' => 'Pedagogical Sandwich Feedback',
                'target_role' => 'docentes',
                'category' => 'Feedback',
                'complexity' => 'Media',
                'description' => 'Creates empathetic and constructive critiques and suggestions for improvement from student drafts or responses.',
                'prompt_text' => 'Here is a student\'s written draft on [Topic]: \'[Insert student draft]\'. Act as an empathetic tutor and provide detailed formative feedback. Use the \'feedback sandwich\' strategy (highlight a strength, point out specific areas for improvement with correct examples, and end with a motivating growth message). Do not rewrite the draft; only guide the student on how to improve their own writing.',
                'sort_order' => 4,
            ],
            [
                'title' => 'Create Reading Scaffolds',
                'target_role' => 'docentes',
                'category' => 'Feedback',
                'complexity' => 'Alta',
                'description' => 'Simplifies a complex academic text and generates scaffolding questions to help students struggling with reading comprehension.',
                'prompt_text' => 'Analyze the following complex text: \'[Insert text]\'. Generate: 1) A simplified version of a paragraph for introductory level students, 2) A list of the 3 key technical terms defined very simply, and 3) Two reading comprehension questions with model answers as guides to scaffold their reading.',
                'sort_order' => 5,
            ],
            [
                'title' => 'Supportive Socratic Tutor',
                'target_role' => 'estudiantes',
                'category' => 'Tutoring',
                'complexity' => 'Media',
                'description' => 'Sets up an interactive study bot that asks you questions instead of giving you direct solutions to your math homework.',
                'prompt_text' => 'I want you to act as a Socratic math tutor. Do not give me direct answers to my questions. Instead, ask me short, simple guiding questions that help me discover the answer myself logically. Let\'s start with the following exercise that I do not understand well: [Write your equation or math problem here].',
                'sort_order' => 6,
            ],
            [
                'title' => 'Conceptual Analogies',
                'target_role' => 'estudiantes',
                'category' => 'Tutoring',
                'complexity' => 'Baja',
                'description' => 'Get analogies and illustrative explanations of scientific or abstract concepts that you find difficult to understand.',
                'prompt_text' => 'I am studying the concept of [Complex concept (e.g. Cell mitosis, economic inflation)] in the subject of [Subject] and I am having trouble fully understanding it. Please explain this concept to me using a vivid, everyday real-life analogy. Break down the explanation into 3 comparative analogies and conclude with a super simplified single-sentence summary for easy memorization.',
                'sort_order' => 7,
            ],
            [
                'title' => 'Interactive Quiz Generator',
                'target_role' => 'estudiantes',
                'category' => 'Review',
                'complexity' => 'Alta',
                'description' => 'Simulates a rigorous assessor who asks you multiple choice questions one by one, giving feedback after each answer.',
                'prompt_text' => 'Act as a rigorous evaluator for the subject [Subject]. Generate a short practice quiz of 5 single-answer multiple-choice questions on the topic: [Exam topic]. Do not reveal the answers to me right away. Show me the questions one by one, wait for me to write my answer option (A, B, C, D), and then tell me if my answer was correct or incorrect along with a 2-sentence explanation before moving on to the next question.',
                'sort_order' => 8,
            ],
            [
                'title' => 'Thematic Debate Simulation',
                'target_role' => 'estudiantes',
                'category' => 'Review',
                'complexity' => 'Media',
                'description' => 'Train for debates or presentations by asking the AI to assume an opposing stance to evaluate your critical argumentation.',
                'prompt_text' => 'I want to constructively debate with you on the following academic topic: [Debate Topic]. Assume the opposing stance to mine and present solid arguments to challenge my views. Start the debate by presenting me with a one-paragraph opposing stance and ask me a critical follow-up question for me to answer.',
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

        // ── Learning Hub Resources ─────────────────────────────────────
        $this->call(ResourceSeeder::class);
    }
}