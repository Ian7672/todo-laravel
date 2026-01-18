<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\logincon;
use Illuminate\Support\Facades\Auth;

// ========== Halaman Auth (Guest only) ==========
Route::middleware(['guest', 'redirect.if.auth'])->group(function () {
    Route::get('/login', fn () => view('tasks.signin'))->name('login');
    Route::get('/signin', fn () => view('tasks.signin'))->name('signin');
    Route::get('/signup', fn () => view('tasks.signup'))->name('signup.form');
    Route::post('/signup', [logincon::class, 'signup'])->name('signup');
    Route::post('/login', [logincon::class, 'login'])->name('login');
});

// ========== Logout ==========
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('signin');
})->name('logout')->middleware('auth');

// ========== Route Autentikasi ==========
Route::middleware('auth')->group(function () {
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::patch('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::patch('/tasks/{task}/toggle', [TaskController::class, 'toggle'])->name('tasks.toggle');
    Route::delete('/tasks/{id}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::get('/tasks/{id}', [TaskController::class, 'show']); // Untuk modal detail
    Route::put('/tasks/{id}', [TaskController::class, 'update']); // Untuk form edit via PUT jika diperlukan
    
});

// ========== Fallback ==========
Route::fallback(function () {
    return redirect()->route('tasks.index');
});

