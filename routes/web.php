<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\DashboardAdminController; 

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('landing');
})->name('landing');

// --- ROUTE PENGALIH (ROUTER) ---
// Ini logika redirect user ke dashboard yang sesuai role-nya
Route::get('/dashboard', function () {
    if (!Auth::check()) {
        return redirect('/login');
    }
    
    $user = Auth::user();

    // Jika Admin -> Ke Admin Dashboard
    if ($user->role == 'admin') {
        return redirect()->route('admin.dashboard');
    }
    
    // Jika Teacher -> Ke Teacher Dashboard (Nanti)
    // if ($user->is_teacher) {
    //     return redirect()->route('teacher.dashboard');
    // }

    Auth::logout();
    return redirect('/login')->with('error', 'You do not have access.');

})->middleware(['auth', 'verified'])->name('dashboard');


// RUTE ADMIN
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // 1. DASHBOARD UTAMA & CHART API (Pakai DashboardAdminController)
    Route::get('/dashboard', [DashboardAdminController::class, 'index'])->name('dashboard');
    Route::get('/attendance-stats', [DashboardAdminController::class, 'getAttendanceStats']);
    Route::get('/weekly-absence', [DashboardAdminController::class, 'getWeeklyAbsenceReport']);

    // 2. PROFILE
    Route::get('/profile', [ProfileController::class, 'editAdmin'])->name('profile');

    // 3. MODULE STUDENTS
    Route::prefix('student')->name('student.')->group(function () {
        Route::get('/', [StudentController::class, 'index'])->name('index');
        Route::get('/add', [StudentController::class, 'add'])->name('add');
        Route::post('/', [StudentController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [StudentController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [StudentController::class, 'update'])->name('update');
        Route::patch('/{id}/toggle-status', [StudentController::class, 'toggleStatus'])->name('toggleStatus');
        Route::delete('/{id}', [StudentController::class, 'delete'])->name('delete');
        Route::get('/detail/{id}', [StudentController::class, 'detail'])->name('detail');
    });

    // 4. MODULE CLASSES
    Route::get('/classes', [ClassController::class, 'index'])->name('classes.index');
    Route::post('/classes/store', [ClassController::class, 'store'])->name('classes.store');
    Route::put('/classes/{id}', [ClassController::class, 'update'])->name('classes.update');
    Route::get('/classes/detail/{id}', [ClassController::class, 'detailClass'])->name('classes.detailclass');
    Route::get('/classes/{id}', [ClassController::class, 'class'])->name('classes.class');
    Route::get('/classes/{id}/students', [ClassController::class, 'students'])->name('classes.students');

    // 5. MODULE ASSESSMENT
    Route::get('/assessment', [AssessmentController::class, 'index'])->name('assessment.index');
    Route::get('/assessment/show', [AssessmentController::class, 'show'])->name('assessment.show');
    Route::post('/assessment/create', [AssessmentController::class, 'create'])->name('assessment.create');

});

// RUTE TEACHER (Placeholder)
Route::get('/teacher/dashboard', function () {
    return view('teacher.dashboard'); 
})->middleware(['auth', 'verified', 'teacher'])->name('teacher.dashboard');

// RUTE PROFILE UMUM
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';