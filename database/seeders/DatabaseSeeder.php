<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tool;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use \Illuminate\Database\Console\Seeds\WithoutModelEvents;

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
        $catId = fn(string $slug) => DB::table('categories')->where('slug', $slug)->value('id');

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
    }
}
