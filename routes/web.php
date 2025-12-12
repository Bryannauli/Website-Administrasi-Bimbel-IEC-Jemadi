<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\AssessmentFormController;

// Admin Controllers
use App\Http\Controllers\Admin\DashboardAdminController;
use App\Http\Controllers\Admin\TeacherAdminController;
use App\Http\Controllers\TeacherAttendanceRecordController;

// Teacher Controllers
use App\Http\Controllers\Teacher\DashboardTeacherController;
use App\Http\Controllers\Teacher\ClassTeacherController;
use App\Http\Controllers\Teacher\StudentTeacherController;
use App\Http\Controllers\Teacher\AttendanceController;
use App\Http\Controllers\Teacher\TeacherController;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


/* ============================================================================
 |  ROOT DAN DASHBOARD
 ============================================================================ */

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
        Route::get('/dashboard', [DashboardAdminController::class, 'index'])->name('dashboard');
        Route::get('/attendance-stats', [DashboardAdminController::class, 'getAttendanceStats']);
        Route::get('/weekly-absence', [DashboardAdminController::class, 'getWeeklyAbsenceReport']);
        Route::get('/today-schedule', [DashboardAdminController::class, 'getTodaySchedule']);

        /* ADMIN PROFILE */
        Route::get('/profile', [ProfileController::class, 'editAdmin'])->name('profile');


        /* STUDENT */
        Route::prefix('student')->name('student.')->group(function () {
            Route::get('/', [StudentController::class, 'index'])->name('index');
            Route::post('/', [StudentController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [StudentController::class, 'edit'])->name('edit');
            Route::put('/update/{id}', [StudentController::class, 'update'])->name('update');
            Route::patch('/{id}/toggle-status', [StudentController::class, 'toggleStatus'])->name('toggleStatus');
            Route::delete('/{id}', [StudentController::class, 'delete'])->name('delete');
            Route::get('/detail/{id}', [StudentController::class, 'detail'])->name('detail');
        });


        /* CLASS */
        Route::prefix('classes')->name('classes.')->group(function () {
            Route::get('/', [ClassController::class, 'index'])->name('index');
            Route::post('/store', [ClassController::class, 'store'])->name('store');
            Route::put('/{id}', [ClassController::class, 'update'])->name('update');
            Route::patch('/{id}/toggle-status', [ClassController::class, 'toggleStatus'])->name('toggleStatus');
            Route::delete('/{id}', [ClassController::class, 'delete'])->name('delete');
            Route::get('/detail/{id}', [ClassController::class, 'detailClass'])->name('detailclass');
            Route::post('/{id}/assign-student', [ClassController::class, 'assignStudent'])->name('assignStudent');
            Route::patch('/students/{studentId}/unassign', [ClassController::class, 'unassignStudent'])->name('unassignStudent');
        });


        /* =====================================================================
         | TEACHER LIST (versi admin)
         ===================================================================== */
        Route::get('/teachers', [TeacherAdminController::class, 'index'])->name('teacher.index');
        Route::get('/teachers/add', [TeacherAdminController::class, 'create'])->name('teacher.add');
        Route::post('/teachers', [TeacherAdminController::class, 'store'])->name('teacher.store');
        Route::get('/teachers/{id}', [TeacherAdminController::class, 'show'])->name('teacher.show');


        /* =====================================================================
         | TEACHER ATTENDANCE RECORD
         ===================================================================== */
        Route::get('/teacher-attendance', [TeacherAttendanceRecordController::class, 'index'])->name('teacher.attendance');
        Route::get('/teacher-attendance/{teacherId}', [TeacherAttendanceRecordController::class, 'detail'])->name('teacher.detail');


        /* =====================================================================
         | ASSESSMENT
         ===================================================================== */
        Route::get('/assessment', [AssessmentController::class, 'index'])->name('assessment.index');
        Route::get('/assessment/show', [AssessmentController::class, 'show'])->name('assessment.show');
        Route::post('/assessment/create', [AssessmentController::class, 'create'])->name('assessment.create');
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
