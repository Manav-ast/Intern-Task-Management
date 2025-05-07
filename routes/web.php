<?php

use App\Http\Controllers\Admin\{
    AdminAuthController,
    AdminChatController,
    AdminController,
    AdminDashboardController,
    CommentController,
    InternController,
    RoleController,
    TaskController
};
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Route};

require __DIR__ . '/user.php';

Route::prefix('admin')->name('admin.')->group(function () {
    // Guest routes
    Route::middleware('guest:admin')->group(function () {
        Route::controller(AdminAuthController::class)->group(function () {
            Route::get('/login', 'showLoginForm')->name('login');
            Route::post('/login', 'login');
            Route::get('/register', 'showRegisterForm')->name('register');
            Route::post('/register', 'register');
        });
    });

    // Authenticated routes
    Route::middleware('auth:admin')->group(function () {
        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

        // Chat
        Route::controller(AdminChatController::class)->prefix('chat')->name('chat.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/users', 'getUsers')->name('users');
            Route::get('/{id}', 'show')->name('show');
            Route::post('/{id}', 'store')->name('store');
        });

        // Interns Management
        Route::controller(InternController::class)->prefix('interns')->name('interns.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{intern}/edit', 'edit')->name('edit');
            Route::put('/{intern}', 'update')->name('update');
            Route::delete('/{intern}', 'destroy')->name('destroy');
        });

        // Tasks Management
        Route::prefix('tasks')->name('tasks.')->group(function () {
            Route::controller(TaskController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{task}', 'show')->name('show');
                Route::get('/{task}/edit', 'edit')->name('edit');
                Route::put('/{task}', 'update')->name('update');
                Route::delete('/{task}', 'destroy')->name('destroy');
            });

            // Task Comments
            Route::controller(CommentController::class)->group(function () {
                Route::post('/{task}/comments', 'store')->name('comments.store');
                Route::delete('/{task}/comments/{comment}', 'destroy')->name('comments.destroy');
            });
        });

        // Admin & Role Management
        Route::resource('admins', AdminController::class);
        Route::resource('roles', RoleController::class);
    });
});

// Global Message Routes
Route::middleware('auth')->group(function () {
    Route::post('/messages/mark-as-read', function (Request $request) {
        $user = Auth::user();
        Message::where('receiver_id', $user->id)
            ->where('receiver_type', get_class($user))
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['status' => 'success']);
    });
});
