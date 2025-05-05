<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\InternController;
use App\Http\Controllers\Admin\TaskController;
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Admin\AdminChatController;
use App\Http\Controllers\Intern\InternChatController;

require __DIR__ . '/user.php';

// Chat Routes
Route::middleware(['auth:admin'])->prefix('admin')->name('admin.chat.')->group(function () {
    Route::get('/chat', [AdminChatController::class, 'index'])->name('index');
    Route::get('/chat/users', [AdminChatController::class, 'getUsers'])->name('users');
    Route::get('/chat/{id}', [AdminChatController::class, 'show'])->name('show');
    Route::post('/chat/{id}', [AdminChatController::class, 'store'])->name('store');
});

// Admin Routes
Route::prefix('admin')->middleware('guest:admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::get('/register', [AdminAuthController::class, 'showRegisterForm'])->name('admin.register');
    Route::post('/register', [AdminAuthController::class, 'register']);
});
Route::prefix('admin')->middleware('auth:admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
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

    // Admin Management Routes
    Route::resource('admins', AdminController::class);
    Route::resource('roles', RoleController::class);
});
