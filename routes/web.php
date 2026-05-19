<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $tools = \App\Models\Tool::where('approval_status', 'approved')->get();
    return view('welcome', compact('tools'));
});

// Google SSO
Route::get('/auth/google', [\App\Http\Controllers\Auth\GoogleController::class, 'redirect'])->name('login');
Route::get('/auth/google/callback', [\App\Http\Controllers\Auth\GoogleController::class, 'callback']);
Route::post('/logout', [\App\Http\Controllers\Auth\GoogleController::class, 'logout'])->name('logout');

// Teacher: Suggest a tool (Requires Auth)
Route::middleware('auth')->group(function () {
    Route::get('/solicitudes/nueva', [\App\Http\Controllers\ToolRequestController::class, 'create']);
    Route::post('/solicitudes', [\App\Http\Controllers\ToolRequestController::class, 'store']);
});

// Admin panel (Requires Auth and Admin role)
Route::middleware(['auth', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
    // Tool Requests
    Route::get('/solicitudes', [\App\Http\Controllers\Admin\ToolRequestController::class, 'index'])->name('requests.index');
    Route::post('/solicitudes/{toolRequest}/approve', [\App\Http\Controllers\Admin\ToolRequestController::class, 'approve'])->name('requests.approve');
    Route::post('/solicitudes/{toolRequest}/reject', [\App\Http\Controllers\Admin\ToolRequestController::class, 'reject'])->name('requests.reject');

    // Tools CRUD
    Route::get('/tools', [\App\Http\Controllers\Admin\ToolController::class, 'index'])->name('tools.index');
    Route::get('/tools/create', [\App\Http\Controllers\Admin\ToolController::class, 'create'])->name('tools.create');
    Route::post('/tools', [\App\Http\Controllers\Admin\ToolController::class, 'store'])->name('tools.store');
    Route::get('/tools/{tool}/edit', [\App\Http\Controllers\Admin\ToolController::class, 'edit'])->name('tools.edit');
    Route::put('/tools/{tool}', [\App\Http\Controllers\Admin\ToolController::class, 'update'])->name('tools.update');
    Route::patch('/tools/{tool}/toggle-status', [\App\Http\Controllers\Admin\ToolController::class, 'toggleStatus'])->name('tools.toggleStatus');
    Route::delete('/tools/{tool}', [\App\Http\Controllers\Admin\ToolController::class, 'destroy'])->name('tools.destroy');
});

// AI Chatbot Route
Route::post('/api/chat', [\App\Http\Controllers\ChatController::class, 'chat'])->name('api.chat');

