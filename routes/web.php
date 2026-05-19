<?php

use Illuminate\Support\Facades\Route;

// ═════════════════════════════════════════════════════════════════════════
//  PUBLIC ROUTES
// ═════════════════════════════════════════════════════════════════════════

Route::get('/', function () {
    $tools = \App\Models\Tool::approved()
        ->with('categoryRelation')
        ->get();
    $categories = \App\Models\Category::withCount('approvedTools')
        ->orderBy('sort_order')
        ->get();
    return view('welcome', compact('tools', 'categories'));
});

// Tool detail page (public, tracks views)
Route::get('/tools/{tool}', [\App\Http\Controllers\ToolController::class, 'show'])->name('tools.show');

// ═════════════════════════════════════════════════════════════════════════
//  GOOGLE SSO
// ═════════════════════════════════════════════════════════════════════════

Route::get('/auth/google', [\App\Http\Controllers\Auth\GoogleController::class, 'redirect'])->name('login');
Route::get('/auth/google/callback', [\App\Http\Controllers\Auth\GoogleController::class, 'callback']);
Route::post('/logout', [\App\Http\Controllers\Auth\GoogleController::class, 'logout'])->name('logout');

// ═════════════════════════════════════════════════════════════════════════
//  AUTHENTICATED ROUTES (any role: student, teacher, admin)
// ═════════════════════════════════════════════════════════════════════════

Route::middleware('auth')->group(function () {

    // ── Tool Requests (suggest a tool) ──────────────────────────
    Route::get('/solicitudes/nueva', [\App\Http\Controllers\ToolRequestController::class, 'create']);
    Route::post('/solicitudes', [\App\Http\Controllers\ToolRequestController::class, 'store']);

    // ── Favorites ───────────────────────────────────────────────
    Route::get('/favorites', [\App\Http\Controllers\FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/tools/{tool}/favorite', [\App\Http\Controllers\FavoriteController::class, 'toggle'])->name('favorites.toggle');

    // ── Reviews ─────────────────────────────────────────────────
    Route::post('/tools/{tool}/reviews', [\App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');

    // ── Profile ─────────────────────────────────────────────────
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    // ── Chat Conversations (JSON API) ───────────────────────────
    Route::get('/api/conversations', [\App\Http\Controllers\ConversationController::class, 'index']);
    Route::post('/api/conversations', [\App\Http\Controllers\ConversationController::class, 'store']);
    Route::get('/api/conversations/{conversation}', [\App\Http\Controllers\ConversationController::class, 'show']);
    Route::put('/api/conversations/{conversation}', [\App\Http\Controllers\ConversationController::class, 'update']);
    Route::delete('/api/conversations/{conversation}', [\App\Http\Controllers\ConversationController::class, 'destroy']);

    // ── AI Chatbot (requires auth now) ──────────────────────────
    Route::post('/api/chat', [\App\Http\Controllers\ChatController::class, 'chat'])->name('api.chat');
});

// ═════════════════════════════════════════════════════════════════════════
//  ADMIN ROUTES (requires auth + admin role)
// ═════════════════════════════════════════════════════════════════════════

Route::middleware(['auth', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {

    // ── Dashboard ───────────────────────────────────────────────
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // ── Tool Requests ───────────────────────────────────────────
    Route::get('/solicitudes', [\App\Http\Controllers\Admin\ToolRequestController::class, 'index'])->name('requests.index');
    Route::post('/solicitudes/{toolRequest}/approve', [\App\Http\Controllers\Admin\ToolRequestController::class, 'approve'])->name('requests.approve');
    Route::post('/solicitudes/{toolRequest}/reject', [\App\Http\Controllers\Admin\ToolRequestController::class, 'reject'])->name('requests.reject');

    // ── Tools CRUD ──────────────────────────────────────────────
    Route::get('/tools', [\App\Http\Controllers\Admin\ToolController::class, 'index'])->name('tools.index');
    Route::get('/tools/create', [\App\Http\Controllers\Admin\ToolController::class, 'create'])->name('tools.create');
    Route::post('/tools', [\App\Http\Controllers\Admin\ToolController::class, 'store'])->name('tools.store');
    Route::get('/tools/{tool}/edit', [\App\Http\Controllers\Admin\ToolController::class, 'edit'])->name('tools.edit');
    Route::put('/tools/{tool}', [\App\Http\Controllers\Admin\ToolController::class, 'update'])->name('tools.update');
    Route::patch('/tools/{tool}/toggle-status', [\App\Http\Controllers\Admin\ToolController::class, 'toggleStatus'])->name('tools.toggleStatus');
    Route::delete('/tools/{tool}', [\App\Http\Controllers\Admin\ToolController::class, 'destroy'])->name('tools.destroy');

    // ── Categories CRUD ─────────────────────────────────────────
    Route::get('/categories', [\App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [\App\Http\Controllers\Admin\CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [\App\Http\Controllers\Admin\CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [\App\Http\Controllers\Admin\CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [\App\Http\Controllers\Admin\CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [\App\Http\Controllers\Admin\CategoryController::class, 'destroy'])->name('categories.destroy');

    // ── Users Management ────────────────────────────────────────
    Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}/role', [\App\Http\Controllers\Admin\UserController::class, 'updateRole'])->name('users.updateRole');
});

// ═════════════════════════════════════════════════════════════════════════
//  DEVELOPMENT BYPASS
// ═════════════════════════════════════════════════════════════════════════
if (app()->environment('local')) {
    Route::get('/dev/login/{role}', function ($role) {
        $email = match($role) {
            'admin' => 'edwin.lopez@ans.edu.ni',
            'teacher' => 'teacher@ans.edu.ni',
            'student' => 'student@ans.edu.ni',
            default => null
        };

        if (!$email) {
            return response('Invalid role', 400);
        }

        $user = \App\Models\User::where('email', $email)->first();
        if ($user) {
            Auth::login($user);
            return redirect('/')->with('success', "Logged in as {$role} locally!");
        }
        return response('User not found in DB. Run php artisan db:seed.', 404);
    })->name('dev.login');
}

