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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Message;

require __DIR__ . '/user.php';

Route::get('/', function () {
    if (admin_guard()->check()) {
        return redirect()->route('admin.dashboard');
    } elseif (intern_guard()->check()) {
        return redirect()->route('intern.dashboard');
    }
    return redirect()->route('intern.login');
})->name('home');
// Chat Routes
Route::middleware(['auth:admin'])->prefix('admin')->name('admin.chat.')->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('index');
    Route::get('/chat/users', [ChatController::class, 'getUsers'])->name('users');
    Route::get('/chat/{id}', [ChatController::class, 'show'])->name('show');
    Route::post('/chat/{id}', [ChatController::class, 'store'])->name('store');
});

// Intern Chat Routes
Route::middleware(['auth:intern'])->prefix('intern')->name('intern.chat.')->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('index');
    Route::get('/chat/users', [ChatController::class, 'getUsers'])->name('users');
    Route::get('/chat/{id}', [ChatController::class, 'show'])->name('show');
    Route::post('/chat/{id}', [ChatController::class, 'store'])->name('store');
});

// Admin Routes
Route::prefix('admin')->middleware('guest:admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login']);
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

Route::post('/messages/mark-as-read', function (Request $request) {
    $user = Auth::user();
    Message::where('receiver_id', $user->id)
        ->where('receiver_type', get_class($user))
        ->whereNull('read_at')
        ->update(['read_at' => now()]);

    return response()->json(['status' => 'success']);
})->middleware('auth:admin,intern');

// Get unread message count
Route::get('/messages/unread-count', function (Request $request) {
    $user = Auth::user();
    $unreadCount = Message::where('receiver_id', $user->id)
        ->where('receiver_type', get_class($user))
        ->whereNull('read_at')
        ->count();

    return response()->json(['unread_count' => $unreadCount]);
})->middleware('auth:admin,intern');

// Mark messages from a specific sender as read
Route::post('/messages/mark-as-read/{userId}', function (Request $request, $userId) {
    $user = Auth::user();
    $otherUserType = $user instanceof \App\Models\Admin ? \App\Models\Intern::class : \App\Models\Admin::class;

    Message::where('receiver_id', $user->id)
        ->where('receiver_type', get_class($user))
        ->where('sender_id', $userId)
        ->where('sender_type', $otherUserType)
        ->whereNull('read_at')
        ->update(['read_at' => now()]);

    return response()->json(['status' => 'success']);
})->middleware('auth:admin,intern');
