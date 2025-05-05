<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Intern\InternAuthController;
use App\Http\Controllers\Admin\InternController;
use App\Http\Controllers\Admin\TaskController;
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Intern\TaskController as InternTaskController;

// Admin Routes
Route::prefix('admin')->middleware('guest:admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::get('/register', [AdminAuthController::class, 'showRegisterForm'])->name('admin.register');
    Route::post('/register', [AdminAuthController::class, 'register']);
});
Route::prefix('admin')->middleware('auth:admin')->name('admin.')->group(function () {
    Route::get('/dashboard', fn() => view('admin.dashboard'))->name('dashboard');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    Route::prefix('interns')->name('interns.')->group(function () {
        Route::get('/', [InternController::class, 'index'])->name('index');
        Route::get('/create', [InternController::class, 'create'])->name('create');
        Route::post('/', [InternController::class, 'store'])->name('store');
        Route::get('/{intern}/edit', [InternController::class, 'edit'])->name('edit');
        Route::put('/{intern}', [InternController::class, 'update'])->name('update');
        Route::delete('/{intern}', [InternController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('tasks')->name('tasks.')->group(function () {
        Route::get('/', [TaskController::class, 'index'])->name('index');
        Route::get('/create', [TaskController::class, 'create'])->name('create');
        Route::post('/', [TaskController::class, 'store'])->name('store');
        Route::get('/{task}', [TaskController::class, 'show'])->name('show');
        Route::get('/{task}/edit', [TaskController::class, 'edit'])->name('edit');
        Route::put('/{task}', [TaskController::class, 'update'])->name('update');
        Route::delete('/{task}', [TaskController::class, 'destroy'])->name('destroy');

        // Task comments
        Route::post('/{task}/comments', [CommentController::class, 'store'])->name('comments.store');
        Route::delete('/{task}/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    });
});

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

    // Task routes
    Route::get('/tasks', [InternTaskController::class, 'index'])->name('intern.tasks.index');
    Route::get('/tasks/{task}', [InternTaskController::class, 'show'])->name('intern.tasks.show');
    Route::post('/tasks/{task}/comments', [InternTaskController::class, 'addComment'])->name('intern.tasks.comments.store');
});
