<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
// use App\Http\Controllers\AssessmentFormController; // Tidak Terpakai

// Admin Controllers
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminClassController;
use App\Http\Controllers\Admin\AdminStudentController;
use App\Http\Controllers\Admin\AdminTeacherController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminAssessmentController;
use App\Http\Controllers\Admin\AdminLogController;


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

// Ini rute untuk ngecek tampilannya doang, kalau nanti dah disambung ke db 
// hapus aja rutenya yaa
// soalnya belum nyambung ke db
// cara ceknya buka di browser: 127.0..../admin/cek-tampilan
Route::get('/cek-tampilan', function () {    
    // 1. DATA KELAS (Lengkap dengan Time & Days)
    $class = (object) [
        'name' => 'STEP 4 - ENGLISH CONVERSATION',
        'term' => 'JUL - DEC ' . date('Y'),
        'times' => '17:00 - 18:30', // <--- Dummy Time
        'days' => 'Mon & Wed',
    ];

    // 2. DATA GURU
    $teacherName = 'Mr. Richard';      // Form Teacher
    $localTeacher = 'Ms. Sarah';       // Dummy Local Teacher

    // 3. DUMMY SESI (16 Pertemuan)
    $teachingLogs = collect([]);
    $startDate = Carbon::create(2025, 7, 1);
    
    for ($i = 0; $i < 16; $i++) {
        $teachingLogs->push((object)[
            'session_id' => $i + 1,
            'date' => $startDate->copy()->addDays($i * 3)->format('Y-m-d'),
        ]);
    }

    // 4. DUMMY SISWA & ABSENSI
    $studentNames = ['Ferdinand', 'Evelyn', 'Dally Sta', 'Erlina', 'Joceline', 'Bryan', 'Michael'];
    $studentStats = collect([]);
    $attendanceMatrix = []; 

    foreach ($studentNames as $index => $name) {
        $studentId = $index + 1;
        $studentStats->push((object)[
            'student_id' => $studentId,
            'name' => $name,
            'student_number' => 'ST-' . (202500 + $studentId),
            'percentage' => rand(70, 100),
        ]);

        foreach ($teachingLogs as $log) {
            $rand = rand(1, 10);
            if ($rand <= 7) $status = 'present';     
            elseif ($rand == 8) $status = 'late';    
            elseif ($rand == 9) $status = 'absent';   
            else $status = 'permission';             
            $attendanceMatrix[$studentId][$log->session_id] = $status;
        }
    }

    // Return ke View
    return view('admin.classes.partials.attendance-report', [ 
        'class' => $class,
        'teacherName' => $teacherName,
        'localTeacher' => $localTeacher, // <--- Kirim variable baru
        'teachingLogs' => $teachingLogs,
        'studentStats' => $studentStats,
        'attendanceMatrix' => $attendanceMatrix
    ]);
});

// rute untuk cek tampilan assessment
Route::get('/test-assessment-print', function () {
    
    // 1. INFO KELAS (HEADER)
    $headerInfo = (object) [
        'month' => 'July - December ' . date('Y'),
        'form_teacher' => 'Mr. Richard',
        'other_teacher' => 'Mr. Jimmy',
        'class_name' => 'STEP 3',
        'class_time' => '7 - 9 pm',
        'class_days' => 'Tuesday & Thursday'
    ];

    // 2. MATA PELAJARAN (DIGABUNG JADI 1)
    // Urutan kolom sesuai logika: Vocab, Grammar, Reading, Spelling, Listening, Speaking
    $subjects = ['Vocabulary', 'Grammar', 'Reading', 'Spelling', 'Listening', 'Speaking'];

    // 3. DATA SISWA & NILAI
    $studentNames = [
        'Charlene Alycia Chen', 'Felix Horatio', 'Reagan Immanuel', 
        'Xaviera Cleosa Shielder', 'Livia Melosa Shielder', 'Wira', 
        'Jozio Notal Ezer'
    ];

    $students = collect([]);

    foreach ($studentNames as $index => $name) {
        $marks = [];
        $totalAve = 0;

        foreach ($subjects as $subj) {
            // Generate nilai random
            $mid = rand(70, 95);
            $final = rand(70, 95);
            $ave = round(($mid + $final) / 2);
            
            $marks[$subj] = (object) [
                'mid' => $mid,
                'final' => $final,
                'ave' => $ave
            ];
            $totalAve += $ave;
        }

        // Hitung rata-rata total
        $grandAve = round($totalAve / count($subjects));

        $students->push((object)[
            'no' => $index + 1,
            'student_number' => '041' . rand(1000, 9999),
            'name' => $name,
            'marks' => $marks, // Array nilai per mapel
            'total_ave' => $grandAve,
            'rank' => 0, // Nanti dihitung
            'at' => '',  // Attendance/Remarks
        ]);
    }

    // Hitung Ranking Sederhana
    $students = $students->sortByDesc('total_ave')->values();
    foreach ($students as $idx => $s) {
        $s->rank = $idx + 1;
    }
    // Balikkan ke urutan nama/no asli jika perlu, atau biarkan urut ranking
    // $students = $students->sortBy('no'); 

    return view('admin.classes.partials.assessment-report', [
        'header' => $headerInfo,
        'subjects' => $subjects,
        'students' => $students
    ]);
});

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
            Route::get('/daily-recap', [AdminClassController::class, 'dailyRecap'])->name('daily-recap');
        });

        /* TEACHER LIST */
        Route::prefix('teachers')->name('teacher.')->group(function () {
            // 1. Index List Guru
            Route::get('/', [AdminTeacherController::class, 'index'])->name('index');

            // 2. Tambah Guru
            Route::get('/add', [AdminTeacherController::class, 'create'])->name('add');
            Route::post('/', [AdminTeacherController::class, 'store'])->name('store');

            // 3. Toggle Role Guru / Admin
            Route::patch('/{id}/toggle-role', [AdminTeacherController::class, 'toggleRole'])->name('toggleRole');
            
            // 4. Detail, Update, Delete (Parameter ID)
            Route::get('/{id}', [AdminTeacherController::class, 'detail'])->name('detail');
            Route::put('/{teacher}', [AdminTeacherController::class, 'update'])->name('update');
            Route::put('/{teacher}/toggle-status', [AdminTeacherController::class, 'toggleStatus'])->name('toggle-status');
            Route::delete('/{id}', [AdminTeacherController::class, 'delete'])->name('delete');
        });

        /* ACTIVITY LOG */
        Route::resource('activity-log', AdminLogController::class)->only(['index', 'show']);

        /* =====================================================================
        | ASSESSMENT (Global Index/Recap)
        ===================================================================== */
        Route::prefix('assessment')->name('assessment.')->group(function () {
            Route::get('/', [AdminAssessmentController::class, 'index'])->name('index');
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