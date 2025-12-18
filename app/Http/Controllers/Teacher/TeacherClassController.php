<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use App\Models\ClassModel;
use App\Models\ClassSession;
use App\Models\AssessmentSession;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TeacherClassController extends Controller
{
    /**
     * Menampilkan daftar kelas
     */
    public function index(Request $request)
    {
        $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        
        // Tentukan Hari Filter
        if ($request->has('day')) {
            $currentDay = $request->input('day');
        } else {
            $currentDay = Carbon::now()->format('l');
        }

        // 1. Query untuk Daftar Kelas (Dropdown Filter Class Name)
        $filterClassQuery = ClassModel::where('is_active', true)
            ->orderBy('name', 'asc');
            
        if ($request->filled('category')) {
            $filterClassQuery->where('category', $request->category);
        }

        $classesForFilter = $filterClassQuery->get();

        // 2. Query Utama (Data Tabel)
        $query = ClassModel::with(['schedules' => function ($q) use ($currentDay) {
            // Tetap filter jadwal berdasarkan hari yang dipilih (agar tampilan rapi)
            if (!empty($currentDay)) {
                $q->where('day_of_week', $currentDay);
            }
        }])
        ->with(['formTeacher', 'localTeacher']) // Eager load teacher names
        ->where('is_active', true);

        // --- FILTER LOGIC ---
        
        // A. Search
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('classroom', 'LIKE', "%{$searchTerm}%");
            });
        }
        
        // B. Category Filter
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        // C. Class Name Filter
        if ($request->filled('class_id')) {
            $query->where('id', $request->class_id);
        }
        
        // D. Day Filter
        if (!empty($currentDay)) {
            $query->whereHas('schedules', function ($q) use ($currentDay) {
                $q->where('day_of_week', $currentDay);
            });
        }

        $classes = $query->paginate(10)
                        ->appends($request->except('page'));

        return view('teacher.classes.index', [
            'classes' => $classes,
            'classesForFilter' => $classesForFilter,
            'daysOfWeek' => $daysOfWeek, 
            'currentDay' => $currentDay, 
        ]);
    }

    public function detail(Request $request, $id)
    {
        // 1. Load Data Kelas Utama
        $class = ClassModel::with(['formTeacher', 'localTeacher'])->findOrFail($id);
        
        // 2. Ambil Stats Siswa dari Stored Procedure (Raw Data termasuk Deleted)
        $studentStatsRaw = DB::select('CALL p_get_class_attendance_stats(?)', [$id]);
        $studentStats = collect($studentStatsRaw);

        // [FIX] Filter khusus untuk tampilan "Enrolled Students List"
        // Kita hanya ambil siswa yang deleted_at-nya NULL (Sembunyikan Deleted Student)
        $enrolledStudents = $studentStats->filter(function($student) {
            return is_null($student->deleted_at);
        });

        // 3. Ambil History Sesi dari View
        $classSessions = DB::table('v_class_activity_logs')
            ->where('class_id', $id)
            ->orderBy('date', 'desc')
            ->get(); 

        // 4. Sesi Hari Ini
        $sessionToday = ClassSession::where('class_id', $id)
                                    ->where('date', Carbon::today()->format('Y-m-d'))
                                    ->first();

        // 5. Assessment Pagination
        $assessments = AssessmentSession::where('class_id', $id)
            ->orderBy('written_date', 'desc')
            ->paginate(5, ['*'], 'assessment_page');

        // 6. Data Matrix (Tetap pakai full data $studentStats agar history deleted student tetap ada)
        $allSessions = ClassSession::where('class_id', $id)
                        ->with(['records:id,class_session_id,student_id,status', 'teacher'])
                        ->orderBy('date', 'desc')
                        ->get();

        $attendanceMatrix = [];
        foreach ($allSessions as $session) {
            foreach ($session->records as $record) {
                $attendanceMatrix[$record->student_id][$session->id] = $record->status;
            }
        }

        return view('teacher.classes.detail', compact(
            'class', 
            'classSessions', 
            'assessments',
            'sessionToday',
            'allSessions',
            'attendanceMatrix',
            'studentStats',     // Full Data (untuk Modal History)
            'enrolledStudents'  // Filtered Data (untuk List Utama)
        ));
    }
}