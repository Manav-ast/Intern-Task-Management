<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Intern\InternAuthController;
use App\Http\Controllers\Intern\InternTaskController;
use App\Http\Controllers\Intern\TaskController;
use App\Http\Controllers\Intern\InternChatController;
use App\Http\Controllers\Intern\ProfileController;

// Intern Routes
Route::prefix('intern')->middleware('guest:intern')->group(function () {
    Route::get('/login', [InternAuthController::class, 'showLoginForm'])->name('intern.login');
    Route::post('/login', [InternAuthController::class, 'login']);
    Route::get('/register', [InternAuthController::class, 'showRegisterForm'])->name('intern.register');
    Route::post('/register', [InternAuthController::class, 'register']);
});

Route::prefix('intern')->middleware('auth:intern')->group(function () {
    Route::get('/dashboard', fn() => view('intern.dashboard'))->name('intern.dashboard');
    Route::post('/logout', [InternAuthController::class, 'logout'])->name('intern.logout');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('intern.profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('intern.profile.update');

    // Task routes
    Route::get('/tasks', [TaskController::class, 'index'])->name('intern.tasks.index');
    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('intern.tasks.show');
    Route::post('/tasks/{task}/comments', [TaskController::class, 'addComment'])->name('intern.tasks.comments.store');

    // Chat routes
    Route::prefix('chat')->name('intern.chat.')->group(function () {
        Route::get('/', [InternChatController::class, 'index'])->name('index');
        Route::get('/users', [InternChatController::class, 'getUsers'])->name('users');
        Route::get('/{id}', [InternChatController::class, 'show'])->name('show');
        Route::post('/{id}', [InternChatController::class, 'store'])->name('store');
    });
});
