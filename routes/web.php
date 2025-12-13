<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\AssessmentFormController; // Tidak Terpakai

// Admin Controllers
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminAssessmentController; // Controller Baru (Manage Grades per Class)
use App\Http\Controllers\Admin\AdminClassController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminStudentController;
use App\Http\Controllers\Admin\AdminTeacherController;

use App\Http\Controllers\TeacherAttendanceRecordController;

// Teacher Controllers
use App\Http\Controllers\Teacher\TeacherAttendanceController;
use App\Http\Controllers\Teacher\TeacherAssessmentController;
use App\Http\Controllers\Teacher\TeacherClassController;
use App\Http\Controllers\Teacher\TeacherDashboardController;


/* ROOT DAN DASHBOARD */
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('landing');
})->name('landing');

Route::get('/dashboard', function () {
    if (!Auth::check()) return redirect('/login');

    $user = Auth::user();

    if ($user->role == 'admin') {
        return redirect()->route('admin.dashboard');
    }

    Auth::logout();
    return redirect('/login')->with('error', 'You do not have access.');

})->middleware(['auth', 'verified'])->name('dashboard');

/* ============================================================================
|  ADMIN ROUTES
============================================================================ */
Route::middleware(['auth', 'verified', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        /* DASHBOARD */
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/attendance-stats', [AdminDashboardController::class, 'getAttendanceStats']);
        Route::get('/weekly-absence', [AdminDashboardController::class, 'getWeeklyAbsenceReport']);
        Route::get('/today-schedule', [AdminDashboardController::class, 'getTodaySchedule']);

        /* ADMIN PROFILE */
        Route::get('/profile', [ProfileController::class, 'editAdmin'])->name('profile');

        /* STUDENT */
        Route::prefix('student')->name('student.')->group(function () {
            Route::get('/', [AdminStudentController::class, 'index'])->name('index');
            Route::post('/', [AdminStudentController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [AdminStudentController::class, 'edit'])->name('edit');
            Route::put('/update/{id}', [AdminStudentController::class, 'update'])->name('update');
            Route::patch('/{id}/toggle-status', [AdminStudentController::class, 'toggleStatus'])->name('toggleStatus');
            Route::delete('/{id}', [AdminStudentController::class, 'delete'])->name('delete');
            Route::get('/detail/{id}', [AdminStudentController::class, 'detail'])->name('detail');
        });

        /* CLASS */
        Route::prefix('classes')->name('classes.')->group(function () {
            Route::get('/', [AdminClassController::class, 'index'])->name('index');
            Route::post('/store', [AdminClassController::class, 'store'])->name('store');
            Route::put('/{id}', [AdminClassController::class, 'update'])->name('update');
            Route::patch('/{id}/toggle-status', [AdminClassController::class, 'toggleStatus'])->name('toggleStatus');
            Route::delete('/{id}', [AdminClassController::class, 'delete'])->name('delete');
            Route::get('/detail/{id}', [AdminClassController::class, 'detailClass'])->name('detailclass');
            Route::patch('/classes/{id}/assign-teacher', [AdminClassController::class, 'assignTeacher'])->name('assignTeacher');
            Route::patch('/classes/{class}/unassign-teacher/{type}', [AdminClassController::class, 'unassignTeacher'])->name('unassignTeacher');
            Route::post('/{id}/assign-student', [AdminClassController::class, 'assignStudent'])->name('assignStudent');
            Route::patch('/students/{studentId}/unassign', [AdminClassController::class, 'unassignStudent'])->name('unassignStudent');
            
            // Assessment Routes (Management per Class)
            Route::get('/{classId}/assessment/{type}', [AdminAssessmentController::class, 'detail'])->name('assessment.detail');
            Route::post('/{classId}/assessment/{type}/save', [AdminAssessmentController::class, 'storeOrUpdateGrades'])->name('assessment.storeOrUpdateGrades');
        });

        /* TEACHER LIST */
        Route::get('/teachers', [AdminTeacherController::class, 'index'])->name('teacher.index');
        Route::get('/teachers/add', [AdminTeacherController::class, 'create'])->name('teacher.add');
        Route::post('/teachers', [AdminTeacherController::class, 'store'])->name('teacher.store');
        Route::get('/teachers/{id}', [AdminTeacherController::class, 'show'])->name('teacher.show');
        Route::put('/teachers/{teacher}', [AdminTeacherController::class, 'update'])->name('teacher.update');
        Route::put('teachers/{teacher}/toggle-status', [AdminTeacherController::class, 'toggleStatus'])->name('teachers.toggle-status');

        /* =====================================================================
        | TEACHER ATTENDANCE RECORD
        ===================================================================== */
        Route::get('/teacher-attendance', [TeacherAttendanceRecordController::class, 'teacher'])->name('teacher.attendance');
        Route::get('/teacher-attendance/{teacherId}', [TeacherAttendanceRecordController::class, 'detail'])->name('teacher.detail');
        Route::post('/teacher-attendance/store', [TeacherAttendanceRecordController::class, 'store'])->name('teacher.attendance.store');


        /* =====================================================================
        | ASSESSMENT (Global Index/Recap)
        ===================================================================== */
        Route::prefix('assessment')->name('assessment.')->group(function () {
             // Route ini akan menampilkan daftar semua sesi penilaian (Index Global)
            Route::get('/', [AdminAssessmentController::class, 'index'])->name('index');
             // Route::get('/assessment/show', [AdminAssessmentController::class, 'show'])->name('assessment.show'); // Diabaikan
             // Route::post('/assessment/create', [AdminAssessmentController::class, 'create'])->name('assessment.create'); // Diabaikan
        });
    });

/* ============================================================================
|  TEACHER ROUTES (USER GURU)
============================================================================ */
Route::prefix('teacher')->name('teacher.')->middleware(['auth'])->group(function () {

    /* Dashboard */
    Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics', [TeacherDashboardController::class, 'analytics'])->name('analytics');
    
    /* Schedule */
    Route::prefix('schedule')->name('schedule.')->group(function () {
        Route::get('/my', [TeacherDashboardController::class, 'mySchedule'])->name('my');
    });

    /* 3. Classes Management */
    Route::prefix('classes')->name('classes.')->group(function () {
        
        // --- A. Class List & Detail (TeacherClassController) ---
        Route::get('/', [TeacherClassController::class, 'index'])->name('index');
        Route::get('/{id}/detail', [TeacherClassController::class, 'detail'])->name('detail');
        // Route::get('/{id}', [TeacherClassController::class, 'show'])->name('show'); // Opsional: jika detail dan show beda halaman


        // --- B. Attendance / Absensi (TeacherAttendanceController) ---
        // Membuat sesi absen baru (URL: /teacher/classes/{id}/session/store)
        Route::post('/{id}/session/store', [TeacherAttendanceController::class, 'storeSession'])->name('session.store');
        
        // Detail & Update Absen (URL: /teacher/classes/{classId}/session/{sessionId})
        Route::get('/{classId}/session/{sessionId}', [TeacherAttendanceController::class, 'sessionDetail'])->name('session.detail');
        Route::put('/{classId}/session/{sessionId}', [TeacherAttendanceController::class, 'updateSession'])->name('session.update');


        // --- C. Assessment / Penilaian (TeacherAssessmentController) ---
        // Membuat sesi nilai baru (Jika ada fiturnya)
        Route::post('/{id}/assessment', [TeacherAssessmentController::class, 'storeAssessment'])->name('assessment.store');

        // Input & Update Nilai (URL: /teacher/classes/{classId}/assessment/{assessmentId})
        Route::get('/{classId}/assessment/{assessmentId}', [TeacherAssessmentController::class, 'assessmentDetail'])->name('assessment.detail');
        Route::put('/{classId}/assessment/{assessmentId}', [TeacherAssessmentController::class, 'updateAssessmentMarks'])->name('assessment.update');

    });

});

/* ============================================================================
|  PROFILE (SEMUA USER)
============================================================================ */

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';