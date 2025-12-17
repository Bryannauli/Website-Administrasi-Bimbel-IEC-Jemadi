<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Admin Controllers
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminClassController;
use App\Http\Controllers\Admin\AdminStudentController;
use App\Http\Controllers\Admin\AdminTeacherController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminAssessmentController;
use App\Http\Controllers\Admin\AdminLogController;
use App\Http\Controllers\Admin\AdminTrashController;

// Teacher Controllers
use App\Http\Controllers\Teacher\TeacherClassController;
use App\Http\Controllers\Teacher\TeacherDashboardController;
use App\Http\Controllers\Teacher\TeacherAssessmentController;
use App\Http\Controllers\Teacher\TeacherAttendanceController;

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
    } elseif ($user->is_teacher) {
        return redirect()->route('teacher.dashboard');
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
            
            // Teacher Assignment
            Route::patch('/classes/{id}/assign-teacher', [AdminClassController::class, 'assignTeacher'])->name('assignTeacher');
            Route::patch('/classes/{class}/unassign-teacher/{type}', [AdminClassController::class, 'unassignTeacher'])->name('unassignTeacher');
            
            // Student Assignment
            Route::post('/{id}/assign-student', [AdminClassController::class, 'assignStudent'])->name('assignStudent');
            Route::patch('/students/{studentId}/unassign', [AdminClassController::class, 'unassignStudent'])->name('unassignStudent');
            
            // Assessment & Recap
            Route::get('/{classId}/assessment/{type}', [AdminAssessmentController::class, 'detail'])->name('assessment.detail');
            Route::post('/{classId}/assessment/{type}/save', [AdminAssessmentController::class, 'storeOrUpdateGrades'])->name('assessment.storeOrUpdateGrades');
            Route::get('/daily-recap', [AdminClassController::class, 'dailyRecap'])->name('daily-recap');
        });

        /* TEACHER LIST */
        Route::prefix('teachers')->name('teacher.')->group(function () {
            Route::get('/', [AdminTeacherController::class, 'index'])->name('index');
            Route::get('/add', [AdminTeacherController::class, 'create'])->name('add');
            Route::post('/', [AdminTeacherController::class, 'store'])->name('store');
            Route::patch('/{id}/toggle-role', [AdminTeacherController::class, 'toggleRole'])->name('toggleRole');
            Route::get('/{id}', [AdminTeacherController::class, 'detail'])->name('detail');
            Route::put('/{teacher}', [AdminTeacherController::class, 'update'])->name('update');
            Route::put('/{teacher}/toggle-status', [AdminTeacherController::class, 'toggleStatus'])->name('toggle-status');
            Route::delete('/{id}', [AdminTeacherController::class, 'delete'])->name('delete');
        });

        /* =====================================================================
        | TRASH MANAGEMENT (UNIFIED TRASH)
        ===================================================================== */
        Route::prefix('trash')->name('trash.')->group(function () {
            // Halaman Utama Trash
            Route::get('/', [AdminTrashController::class, 'index'])->name('index');
            Route::get('/student/{id}', [AdminTrashController::class, 'detailTrashedStudent'])->name('student.detail');
            Route::get('/teacher/{id}', [AdminTrashController::class, 'detailTrashedTeacher'])->name('teacher.detail');
            Route::get('/class/{id}', [AdminTrashController::class, 'detailTrashedClass'])->name('class.detail');
            
            // Restore: /admin/trash/{type}/{id}/restore
            Route::post('/{type}/{id}/restore', [AdminTrashController::class, 'restore'])->name('restore');
            
            // Force Delete: /admin/trash/{type}/{id}/force-delete
            Route::delete('/force-delete/{type}/{id}', [AdminTrashController::class, 'forceDelete'])->name('force_delete');
        });

        /* ACTIVITY LOG */
        Route::resource('activity-log', AdminLogController::class)->only(['index', 'show']);

        /* ASSESSMENT (Global) */
        Route::prefix('assessment')->name('assessment.')->group(function () {
            Route::get('/', [AdminAssessmentController::class, 'index'])->name('index');
        });
        
        // --- ROUTE TES TAMPILAN (HAPUS JIKA SUDAH TIDAK PERLU) ---
        Route::get('/cek-tampilan', function () {    
            $class = (object) ['name' => 'STEP 4 - ENGLISH CONVERSATION', 'term' => 'JUL - DEC ' . date('Y'), 'times' => '17:00 - 18:30', 'days' => 'Mon & Wed'];
            $teacherName = 'Mr. Richard'; $localTeacher = 'Ms. Sarah';
            $teachingLogs = collect([]); 
            $startDate = Carbon::create(2025, 7, 1);
            for ($i = 0; $i < 16; $i++) $teachingLogs->push((object)['session_id' => $i + 1, 'date' => $startDate->copy()->addDays($i * 3)->format('Y-m-d')]);
            $studentNames = ['Ferdinand', 'Evelyn', 'Dally Sta', 'Erlina', 'Joceline', 'Bryan', 'Michael'];
            $studentStats = collect([]); $attendanceMatrix = []; 
            foreach ($studentNames as $index => $name) {
                $studentId = $index + 1;
                $studentStats->push((object)['student_id' => $studentId, 'name' => $name, 'student_number' => 'ST-' . (202500 + $studentId), 'percentage' => rand(70, 100)]);
                foreach ($teachingLogs as $log) { $rand = rand(1, 10); $attendanceMatrix[$studentId][$log->session_id] = ($rand <= 7) ? 'present' : (($rand == 8) ? 'late' : (($rand == 9) ? 'absent' : 'permission')); }
            }
            return view('admin.classes.partials.attendance-report', ['class' => $class, 'teacherName' => $teacherName, 'localTeacher' => $localTeacher, 'teachingLogs' => $teachingLogs, 'studentStats' => $studentStats, 'attendanceMatrix' => $attendanceMatrix]);
        });
        
        Route::get('/test-assessment-print', function () {
            $headerInfo = (object) ['month' => 'July - December ' . date('Y'), 'form_teacher' => 'Mr. Richard', 'other_teacher' => 'Mr. Jimmy', 'class_name' => 'STEP 3', 'class_time' => '7 - 9 pm', 'class_days' => 'Tuesday & Thursday'];
            $subjects = ['Vocabulary', 'Grammar', 'Reading', 'Spelling', 'Listening', 'Speaking'];
            $studentNames = ['Charlene Alycia Chen', 'Felix Horatio', 'Reagan Immanuel', 'Xaviera Cleosa Shielder', 'Livia Melosa Shielder', 'Wira', 'Jozio Notal Ezer'];
            $students = collect([]);
            foreach ($studentNames as $index => $name) {
                $marks = []; $totalAve = 0;
                foreach ($subjects as $subj) { $mid = rand(70, 95); $final = rand(70, 95); $ave = round(($mid + $final) / 2); $marks[$subj] = (object) ['mid' => $mid, 'final' => $final, 'ave' => $ave]; $totalAve += $ave; }
                $grandAve = round($totalAve / count($subjects));
                $students->push((object)['no' => $index + 1, 'student_number' => '041' . rand(1000, 9999), 'name' => $name, 'marks' => $marks, 'total_ave' => $grandAve, 'rank' => 0, 'at' => '']);
            }
            $students = $students->sortByDesc('total_ave')->values();
            foreach ($students as $idx => $s) { $s->rank = $idx + 1; }
            return view('admin.classes.partials.assessment-report', ['header' => $headerInfo, 'subjects' => $subjects, 'students' => $students]);
        });
    });

/* ============================================================================
|  TEACHER ROUTES (USER GURU)
============================================================================ */
Route::prefix('teacher')->name('teacher.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics', [TeacherDashboardController::class, 'analytics'])->name('analytics');
    
    Route::prefix('schedule')->name('schedule.')->group(function () {
        Route::get('/my', [TeacherDashboardController::class, 'mySchedule'])->name('my');
    });

    Route::prefix('classes')->name('classes.')->group(function () {
        Route::get('/', [TeacherClassController::class, 'index'])->name('index');
        Route::get('/{id}/detail', [TeacherClassController::class, 'detail'])->name('detail');
        
        Route::post('/{id}/session/store', [TeacherAttendanceController::class, 'storeSession'])->name('session.store');
        Route::get('/{classId}/session/{sessionId}', [TeacherAttendanceController::class, 'sessionDetail'])->name('session.detail');
        Route::put('/{classId}/session/{sessionId}', [TeacherAttendanceController::class, 'updateSession'])->name('session.update');

        Route::post('/{id}/assessment', [TeacherAssessmentController::class, 'storeAssessment'])->name('assessment.store');
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