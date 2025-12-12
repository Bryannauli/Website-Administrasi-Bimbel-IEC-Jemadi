<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AssessmentController; // Controller Lama (Global Assessment)
// use App\Http\Controllers\AssessmentFormController; // Tidak Terpakai

// Admin Controllers
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminStudentController;
use App\Http\Controllers\Admin\AdminClassController;
use App\Http\Controllers\Admin\AdminTeacherController;
use App\Http\Controllers\Admin\AdminAssessmentController; // Controller Baru (Manage Grades per Class)
use App\Http\Controllers\TeacherAttendanceRecordController;

// Teacher Controllers
use App\Http\Controllers\Teacher\DashboardTeacherController;
use App\Http\Controllers\Teacher\ClassTeacherController;
use App\Http\Controllers\Teacher\StudentTeacherController;
use App\Http\Controllers\Teacher\AttendanceController;
use App\Http\Controllers\Teacher\TeacherController;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


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
            Route::get('/{classId}/assessment/{type}', [AdminAssessmentController::class, 'manageGrades'])->name('assessment.manage');
            Route::patch('/{sessionId}/store-grades', [AdminAssessmentController::class, 'storeOrUpdateGrades'])->name('assessment.storeOrUpdate');
        });

        /* TEACHER LIST */
        Route::get('/teachers', [AdminTeacherController::class, 'index'])->name('teacher.index');
        Route::get('/teachers/add', [AdminTeacherController::class, 'create'])->name('teacher.add');
        Route::post('/teachers', [AdminTeacherController::class, 'store'])->name('teacher.store');
        Route::get('/teachers/{id}', [AdminTeacherController::class, 'show'])->name('teacher.show');
        Route::put('/teachers/{teacher}', [AdminTeacherController::class, 'update'])->name('teacher.update');

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
    Route::get('/dashboard', [DashboardTeacherController::class, 'index'])->name('dashboard');
    Route::get('/analytics', [DashboardTeacherController::class, 'analytics'])->name('analytics');

    /* Classes */
    Route::prefix('classes')->name('classes.')->group(function () {
        Route::get('/', [ClassTeacherController::class, 'index'])->name('index');
        Route::get('/{id}', [ClassTeacherController::class, 'show'])->name('show');
        Route::get('/{id}/detail', [ClassTeacherController::class, 'detail'])->name('detail');
        Route::post('/store', [ClassTeacherController::class, 'store'])->name('store');

        /* Session */
        Route::get('/{classId}/session/{sessionId}', [ClassTeacherController::class, 'sessionDetail'])->name('session.detail');
        Route::post('/{classId}/session/store', [ClassTeacherController::class, 'storeSession'])->name('session.store');
        Route::put('/{classId}/session/{sessionId}', [ClassTeacherController::class, 'updateSession'])->name('session.update');
        Route::post('/{id}/assessment', [ClassTeacherController::class, 'storeAssessment'])->name('assessment.store');
        Route::get('/{classId}/assessment/{assessmentId}', [ClassTeacherController::class, 'assessmentDetail'])->name('assessment.detail');
        Route::put('/{classId}/assessment/{assessmentId}', [ClassTeacherController::class, 'updateAssessmentMarks'])->name('assessment.update');
    });

    /* Students */
    Route::prefix('students')->name('students.')->group(function () {
        Route::get('/', [StudentTeacherController::class, 'index'])->name('index');
        Route::get('/{id}', [StudentTeacherController::class, 'show'])->name('show');
        Route::get('/marks', [StudentTeacherController::class, 'marks'])->name('marks');
        Route::get('/attendance', [StudentTeacherController::class, 'attendance'])->name('attendance');
        Route::post('/{id}/assessment', [StudentTeacherController::class, 'storeAssessment'])->name('assessment.store');
    });

    /* Teacher (User Guru melihat sesama guru) */
    Route::prefix('teachers')->name('teachers.')->group(function () {
        Route::get('/', [TeacherController::class, 'index'])->name('index');
        Route::get('/{id}', [TeacherController::class, 'show'])->name('show');
        Route::get('/attendance', [TeacherController::class, 'attendance'])->name('attendance');
        Route::get('/attendance/{classId}', [TeacherController::class, 'classAttendance'])->name('attendance.class');
    });

    /* Attendance */
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::post('/submit', [AttendanceController::class, 'submit'])->name('submit');
        Route::put('/{id}/update', [AttendanceController::class, 'update'])->name('update');
        Route::get('/export', [AttendanceController::class, 'export'])->name('export');
    });

    /* Schedule */
    Route::prefix('schedule')->name('schedule.')->group(function () {
        Route::get('/my', [DashboardTeacherController::class, 'mySchedule'])->name('my');
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