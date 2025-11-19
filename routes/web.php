<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('landing');
});

Route::get('/dashboard', function () {
    if (!Auth::check()) {
        return redirect('/login');
    }
    
    $user = Auth::user();

    // Prioritaskan Admin: Admin memiliki akses tertinggi
    if ($user->role == 'admin') {
        return redirect()->route('admin.dashboard');
    }
    
    // Cek status Teacher
    if ($user->is_teacher) {
        return redirect()->route('teacher.dashboard');
    }

    // Jika tidak memiliki role/status yang diizinkan
    Auth::logout();
    return redirect('/login')->with('error', 'Anda tidak memiliki akses.');

})->middleware(['auth', 'verified'])->name('dashboard');

// --- Rute Admin ---
Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->middleware(['auth', 'verified', 'role:admin'])->name('admin.dashboard');
Route::get('/admin/student', [StudentController::class, 'index'])
     ->name('admin.student');

// --- Rute Teacher  ---
Route::get('/teacher/dashboard', function () {
    return view('admin.dashboard'); 
})->middleware(['auth', 'verified', 'teacher'])->name('teacher.dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
