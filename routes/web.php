<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $tools = \App\Models\Tool::where('approval_status', 'approved')->get();
    return view('welcome', compact('tools'));
});

// Teacher: Suggest a tool
Route::get('/solicitudes/nueva', [\App\Http\Controllers\ToolRequestController::class, 'create']);
Route::post('/solicitudes', [\App\Http\Controllers\ToolRequestController::class, 'store']);

// Admin panel
Route::prefix('admin')->group(function () {
    Route::get('/solicitudes', [\App\Http\Controllers\Admin\ToolRequestController::class, 'index']);
    Route::post('/solicitudes/{toolRequest}/approve', [\App\Http\Controllers\Admin\ToolRequestController::class, 'approve']);
    Route::post('/solicitudes/{toolRequest}/reject', [\App\Http\Controllers\Admin\ToolRequestController::class, 'reject']);
});
