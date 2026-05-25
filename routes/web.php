<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TaskForceMemberController;
use App\Http\Controllers\Admin\PromptTipController;
use App\Http\Controllers\Admin\ToolInsightController;
use App\Models\TaskForceMember;
use App\Models\PromptTip;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\LessonPlanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ToolController;
use App\Http\Controllers\ToolRequestController;
use App\Models\Category;
use App\Models\Tool;
use App\Models\User;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\SlidesController;
use App\Http\Controllers\CalendarController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

// ═════════════════════════════════════════════════════════════════════════
//  PUBLIC ROUTES
// ═════════════════════════════════════════════════════════════════════════

Route::get('/lang/{locale}', [App\Http\Controllers\LanguageController::class, 'switch'])->name('lang.switch');

Route::get('/', function () {
    $tools = Cache::rememberForever('welcome_tools', function () {
        return Tool::approved()
            ->with('categoryRelation')
            ->get();
    });

    if ($tools instanceof \__PHP_Incomplete_Class || !is_iterable($tools)) {
        Cache::forget('welcome_tools');
        $tools = Tool::approved()
            ->with('categoryRelation')
            ->get();
    }

    $categories = Cache::rememberForever('welcome_categories', function () {
        return Category::withCount('approvedTools')
            ->orderBy('sort_order')
            ->get();
    });

    if ($categories instanceof \__PHP_Incomplete_Class || !is_iterable($categories)) {
        Cache::forget('welcome_categories');
        $categories = Category::withCount('approvedTools')
            ->orderBy('sort_order')
            ->get();
    }

    // Hide AI Detection category/tools from students and public guests
    $canSeeDetection = Auth::check() && (Auth::user()->isTeacher() || Auth::user()->isAdmin());
    if (! $canSeeDetection) {
        $categories = $categories->filter(fn($c) => $c->slug !== 'ai-detection')->values();
        $tools = $tools->filter(fn($t) => $t->categoryRelation?->slug !== 'ai-detection')->values();
    }

    $latestTool = $tools->sortByDesc('created_at')->first();
    $trendingTools = $tools->sortByDesc('click_count')->take(4);

    return view('welcome', compact('tools', 'categories', 'latestTool', 'trendingTools'));
});

// Tool detail page (public, tracks views)
Route::get('/tools/{tool}', [ToolController::class, 'show'])->name('tools.show');

// Educational Platform Static Pages
Route::get('/policy', function () {
    return view('policy');
})->name('policy');

Route::get('/task-force', function () {
    $members = TaskForceMember::orderBy('sort_order')->get();
    return view('task_force', compact('members'));
})->name('task-force');

Route::get('/tips', function () {
    $userId = auth()->id();
    $isAdmin = auth()->check() && auth()->user()->isAdmin();

    $prompts = PromptTip::with(['author', 'comments.user', 'votes'])
        ->when(!$isAdmin, function ($q) use ($userId) {
            $q->where(function ($sub) use ($userId) {
                $sub->where('is_approved', true);
                if ($userId) {
                    $sub->orWhere('user_id', $userId);
                }
            });
        })
        ->orderByDesc('is_approved') // Show pending at top for admin
        ->orderBy('sort_order')
        ->latest()
        ->get();

    return view('tips', compact('prompts'));
})->name('tips');

// ═════════════════════════════════════════════════════════════════════════
//  GOOGLE SSO
// ═════════════════════════════════════════════════════════════════════════

Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('login');
Route::get('/auth/google/export-authorize', [GoogleController::class, 'exportAuthorize'])->name('auth.google.export-authorize');
Route::get('/auth/google/callback', [GoogleController::class, 'callback']);
Route::post('/logout', [GoogleController::class, 'logout'])->name('logout');

// ═════════════════════════════════════════════════════════════════════════
//  AUTHENTICATED ROUTES (any role: student, teacher, admin)
// ═════════════════════════════════════════════════════════════════════════

Route::middleware('auth')->group(function () {

    // ── Tool Requests (suggest a tool) ──────────────────────────
    Route::get('/requests/new', [ToolRequestController::class, 'create'])->name('requests.create');
    Route::post('/requests', [ToolRequestController::class, 'store'])->name('requests.store');

    // ── Favorites ───────────────────────────────────────────────
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/tools/{tool}/favorite', [FavoriteController::class, 'toggle'])->name('favorites.toggle');

    // ── Reviews ─────────────────────────────────────────────────
    Route::post('/tools/{tool}/reviews', [ReviewController::class, 'store'])->name('reviews.store');

    // ── Profile ─────────────────────────────────────────────────
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // ── Chat Conversations (JSON API) ───────────────────────────
    Route::get('/api/conversations', [ConversationController::class, 'index']);
    Route::post('/api/conversations', [ConversationController::class, 'store']);
    Route::get('/api/conversations/{conversation}', [ConversationController::class, 'show']);
    Route::put('/api/conversations/{conversation}', [ConversationController::class, 'update']);
    Route::delete('/api/conversations/{conversation}', [ConversationController::class, 'destroy']);

    // ── AI Chatbot (requires auth now) ──────────────────────────
    Route::post('/api/chat', [ChatController::class, 'chat'])->name('api.chat');

    // ── AI Lesson Planner ──────────────────────────────────────
    // (Moved to teacher/admin middleware group below)

    // ── Prompt Library Colaborativo ──────────────────────────────
    Route::post('/tips/{prompt}/vote', [App\Http\Controllers\PromptLibraryController::class, 'vote'])->name('tips.vote');
    Route::post('/tips/{prompt}/comment', [App\Http\Controllers\PromptLibraryController::class, 'comment'])->name('tips.comment');
    Route::post('/tips/submit', [App\Http\Controllers\PromptLibraryController::class, 'submit'])->name('tips.submit');
    Route::post('/tips/{prompt}/copy', [App\Http\Controllers\PromptLibraryController::class, 'trackCopy'])->name('tips.copy');

    // ── EdTech Badges & Quizzes ─────────────────────────────────
    Route::get('/badges', [App\Http\Controllers\BadgeController::class, 'index'])->name('badges.index');
    Route::get('/badges/{slug}', [App\Http\Controllers\BadgeController::class, 'show'])->name('badges.show');
    Route::get('/quizzes/{quiz}', [App\Http\Controllers\QuizController::class, 'show'])->name('quizzes.show');
    Route::post('/quizzes/{quiz}/submit', [App\Http\Controllers\QuizController::class, 'submit'])->name('quizzes.submit');

    // ── Badge Evidence (teacher submits proof for manual badges) ─
    Route::get('/badges/{badge}/evidence', [App\Http\Controllers\BadgeEvidenceController::class, 'create'])->name('badge-evidence.create');
    Route::post('/badges/{badge}/evidence', [App\Http\Controllers\BadgeEvidenceController::class, 'store'])->name('badge-evidence.store');
});

// ── Teacher/Admin Routes ───────────────────────────────────────
Route::middleware(['auth', 'is_teacher_or_admin'])->group(function () {
    // AI Lesson Planner
    Route::post('/lesson-plans/generate', [LessonPlanController::class, 'generate'])->name('lesson-plans.generate');
    Route::post('/lesson-plans/refine', [LessonPlanController::class, 'refine'])->name('lesson-plans.refine');
    Route::get('/lesson-plans/{lessonPlan}/export', [LessonPlanController::class, 'export'])->name('lesson-plans.export');
    Route::resource('lesson-plans', LessonPlanController::class);

    // Google Workspace Integrations
    Route::get('/classroom/courses', [ClassroomController::class, 'courses'])->name('classroom.courses');
    Route::post('/lesson-plans/{lessonPlan}/classroom-share', [ClassroomController::class, 'share'])->name('lesson-plans.classroom-share');
    Route::post('/lesson-plans/{lessonPlan}/slides', [SlidesController::class, 'generate'])->name('lesson-plans.slides');
    Route::post('/lesson-plans/{lessonPlan}/calendar', [CalendarController::class, 'schedule'])->name('lesson-plans.calendar');

    // AI Detection Hub
    Route::get('/ai-detection', [ToolController::class, 'aiDetection'])->name('ai-detection.index');
});

// ═════════════════════════════════════════════════════════════════════════
//  ADMIN ROUTES (requires auth + admin role)
// ═════════════════════════════════════════════════════════════════════════

Route::middleware(['auth', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {

    // ── Dashboard ───────────────────────────────────────────────
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Tool Requests ───────────────────────────────────────────
    Route::get('/requests', [App\Http\Controllers\Admin\ToolRequestController::class, 'index'])->name('requests.index');
    Route::post('/requests/{toolRequest}/approve', [App\Http\Controllers\Admin\ToolRequestController::class, 'approve'])->name('requests.approve');
    Route::post('/requests/{toolRequest}/reject', [App\Http\Controllers\Admin\ToolRequestController::class, 'reject'])->name('requests.reject');

    // ── Tools CRUD ──────────────────────────────────────────────
    Route::get('/tools', [App\Http\Controllers\Admin\ToolController::class, 'index'])->name('tools.index');
    Route::get('/tools/create', [App\Http\Controllers\Admin\ToolController::class, 'create'])->name('tools.create');
    Route::post('/tools', [App\Http\Controllers\Admin\ToolController::class, 'store'])->name('tools.store');
    Route::get('/tools/{tool}/edit', [App\Http\Controllers\Admin\ToolController::class, 'edit'])->name('tools.edit');
    Route::put('/tools/{tool}', [App\Http\Controllers\Admin\ToolController::class, 'update'])->name('tools.update');
    Route::patch('/tools/{tool}/toggle-status', [App\Http\Controllers\Admin\ToolController::class, 'toggleStatus'])->name('tools.toggleStatus');
    Route::post('/tools/{tool}/generate-insight', [ToolInsightController::class, 'generate'])->name('tools.generateInsight');
    Route::delete('/tools/{tool}', [App\Http\Controllers\Admin\ToolController::class, 'destroy'])->name('tools.destroy');

    // ── Categories CRUD ─────────────────────────────────────────
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // ── Users Management ────────────────────────────────────────
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.updateRole');

    // ── Task Force Members CRUD ─────────────────────────────────
    Route::resource('task-force', TaskForceMemberController::class)->except(['show']);

    // ── Prompt Tips CRUD ────────────────────────────────────────
    Route::resource('prompt-tips', PromptTipController::class)->except(['show']);
    Route::post('/prompt-tips/{prompt}/toggle-approval', [App\Http\Controllers\PromptLibraryController::class, 'toggleApproval'])->name('prompt-tips.toggleApproval');

    // ── Badges CRUD & AI Quiz Generation ────────────────────────
    Route::post('/badges/{badge}/generate-quiz', [App\Http\Controllers\Admin\BadgeController::class, 'generateQuizWithAI'])->name('badges.generateQuiz');
    Route::resource('badges', App\Http\Controllers\Admin\BadgeController::class);

    // ── Badge Evidence Review ────────────────────────────────────
    Route::get('/badge-evidence', [App\Http\Controllers\Admin\BadgeController::class, 'evidenceQueue'])->name('badge-evidence.index');
    Route::post('/badge-evidence/{evidence}/approve', [App\Http\Controllers\Admin\BadgeController::class, 'approveEvidence'])->name('badge-evidence.approve');
    Route::post('/badge-evidence/{evidence}/reject', [App\Http\Controllers\Admin\BadgeController::class, 'rejectEvidence'])->name('badge-evidence.reject');
});

// ═════════════════════════════════════════════════════════════════════════
//  DEVELOPMENT BYPASS
// ═════════════════════════════════════════════════════════════════════════
if (app()->environment('local')) {
    Route::get('/dev/login/{role}', function ($role) {
        $email = match ($role) {
            'admin' => 'edwin.lopez@ans.edu.ni',
            'teacher' => 'teacher@ans.edu.ni',
            'student' => 'student@ans.edu.ni',
            default => null
        };

        if (! $email) {
            return response('Invalid role', 400);
        }

        $user = User::where('email', $email)->first();
        if ($user) {
            Auth::login($user);

            return redirect('/')->with('success', "Logged in as {$role} locally!");
        }

        return response('User not found in DB. Run php artisan db:seed.', 404);
    })->name('dev.login');
}
