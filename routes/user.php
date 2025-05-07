<?php

use App\Http\Controllers\Intern\{
    InternAuthController,
    InternTaskController,
    TaskController,
    InternChatController,
    ProfileController
};
use Illuminate\Support\Facades\Route;

Route::prefix('intern')->name('intern.')->group(function () {
    // Guest routes
    Route::middleware('guest:intern')->group(function () {
        Route::get('/login', [InternAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [InternAuthController::class, 'login']);
        Route::get('/register', [InternAuthController::class, 'showRegisterForm'])->name('register');
        Route::post('/register', [InternAuthController::class, 'register']);
    });

    // Authenticated routes
    Route::middleware('auth:intern')->group(function () {
        // Dashboard
        Route::view('/dashboard', 'intern.dashboard')->name('dashboard');
        Route::post('/logout', [InternAuthController::class, 'logout'])->name('logout');

        // Profile
        Route::controller(ProfileController::class)->prefix('profile')->name('profile.')->group(function () {
            Route::get('/', 'edit')->name('edit');
            Route::put('/', 'update')->name('update');
        });

        // Tasks
        Route::controller(TaskController::class)->prefix('tasks')->name('tasks.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{task}', 'show')->name('show');
            Route::post('/{task}/comments', 'addComment')->name('comments.store');
        });

        // Chat
        Route::controller(InternChatController::class)->prefix('chat')->name('chat.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/users', 'getUsers')->name('users');
            Route::get('/{id}', 'show')->name('show');
            Route::post('/{id}', 'store')->name('store');
        });
    });
});
