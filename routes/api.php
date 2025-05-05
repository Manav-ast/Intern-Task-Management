<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\InternController;
use App\Http\Controllers\Admin\TaskAssignmentController;

// Admin routes for managing interns
Route::middleware(['auth:admin'])->group(function () {
    // Intern management routes
    Route::apiResource('interns', InternController::class);

    // Task assignment routes
    Route::post('/tasks/assign', [TaskAssignmentController::class, 'assign']);
    Route::post('/tasks/unassign', [TaskAssignmentController::class, 'unassign']);
    Route::get('/tasks/{task}/interns', [TaskAssignmentController::class, 'getTaskInterns']);
    Route::get('/interns/{intern}/tasks', [TaskAssignmentController::class, 'getInternTasks']);
});
