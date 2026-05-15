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
Route::middleware(['auth', 'is_admin'])->prefix('admin')->group(function () {
    Route::get('/solicitudes', [\App\Http\Controllers\Admin\ToolRequestController::class, 'index']);
    Route::post('/solicitudes/{toolRequest}/approve', [\App\Http\Controllers\Admin\ToolRequestController::class, 'approve']);
    Route::post('/solicitudes/{toolRequest}/reject', [\App\Http\Controllers\Admin\ToolRequestController::class, 'reject']);
});
