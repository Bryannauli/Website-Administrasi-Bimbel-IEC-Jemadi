<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\AssessmentFormController;



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
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/admin/attendance-summary', [DashboardController::class, 'attendanceSummary']);
    Route::get('/admin/attendance-stats', [DashboardController::class, 'attendanceStats']);
    Route::get('/admin/weekly-absence', [DashboardController::class, 'weeklyAbsence']);

    Route::get('/profile', [ProfileController::class, 'editAdmin'])->name('profile');

    // Grouping Route untuk Student
    Route::prefix('student')->name('student.')->group(function () {
        // Menampilkan daftar siswa
        Route::get('/', [StudentController::class, 'index'])->name('index');
        // Menampilkan form tambah siswa
        Route::get('/add', [StudentController::class, 'add'])->name('add');
        
        // Proses simpan data
        Route::post('/', [StudentController::class, 'store'])->name('store');

        // Menampilkan form edit siswa
        Route::get('/edit/{id}', [StudentController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [StudentController::class, 'update'])->name('update');
        Route::patch('/{id}/toggle-status', [StudentController::class, 'toggleStatus'])->name('toggleStatus');
        
        // Proses hapus siswa
        Route::delete('/{id}', [StudentController::class, 'delete'])->name('delete');
        
        // Menampilkan detail siswa
        Route::get('/detail/{id}', [StudentController::class, 'detail'])->name('detail');

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

// Rute Assesment
 Route::get('/admin/assessment', [AssessmentController::class, 'index'])->name('admin.assessment.index');
 Route::get('/admin/assessment/show', [AssessmentController::class, 'show'])->name('admin.assessment.show');
    Route::post('/admin/assessment/create', [AssessmentController::class, 'create'])->name('admin.assessment.create');


// Rute Class
Route::get('/admin/classes', [ClassController::class, 'index'])->name('admin.classes.index');
 Route::post('/admin/classes/store', [ClassController::class, 'store'])->name('admin.classes.store');
Route::put('/admin/classes/{id}', [ClassController::class, 'update'])->name('admin.classes.update');
//  Route::get('/admin/classes/{id}', [ClassController::class, 'show'])->name('admin.class.show');
Route::get('/admin/classes/detail/{id}', [ClassController::class, 'detailClass'])->name('admin.classes.detailclass');
   Route::get('/admin/classes//{id}', [ClassController::class, 'class'])->name('admin.classes.class');
  Route::get('/admin/classes/{id}/students', [ClassController::class, 'students'])->name('admin.classes.students');


require __DIR__.'/auth.php';
