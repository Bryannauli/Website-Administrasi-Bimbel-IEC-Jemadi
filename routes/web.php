<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    // Cek login status
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('landing');
})->name('landing');

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
    // if ($user->is_teacher) {
    //     return redirect()->route('teacher.dashboard');
    // }

    // Jika tidak memiliki role/status yang diizinkan
    Auth::logout();
    return redirect('/login')->with('error', 'You do not have access to this application. Please contact the administrator.');

})->middleware(['auth', 'verified'])->name('dashboard');

// --- Rute Admin ---
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // Grouping Route untuk Student
    Route::prefix('student')->name('student.')->group(function () {
        // Menampilkan daftar siswa
        Route::get('/', [StudentController::class, 'index'])->name('index');
        
        // Menampilkan form tambah siswa
        Route::get('/add', [StudentController::class, 'add'])->name('add');
        
        // Proses simpan data (bisa ditambahkan nanti)
        Route::post('/', [StudentController::class, 'store'])->name('store');
        
        // Menampilkan detail siswa
        // {id} adalah placeholder untuk ID siswa
        Route::get('/{id}', [StudentController::class, 'detail'])->name('detail');
    });

});

// --- Rute Teacher  ---
Route::get('/teacher/dashboard', function () {
    return view('teacher.dashboard'); 
})->middleware(['auth', 'verified', 'teacher'])->name('teacher.dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
