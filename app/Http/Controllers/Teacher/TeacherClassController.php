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
        
        // 2. [UPDATE] Ambil Stats Siswa dari Stored Procedure (Sama seperti Admin)
        // Procedure ini sudah mengembalikan: id, name, student_number, total_sessions, total_present, percentage
        $studentStatsRaw = DB::select('CALL p_get_class_attendance_stats(?)', [$id]);
        
        // Ubah array of objects menjadi Collection agar mudah diolah di Blade (optional, tapi disarankan)
        $studentStats = collect($studentStatsRaw);

        // 3. [UPDATE] Ambil History Sesi dari View (Sama seperti Admin)
        // View ini sudah menghitung 'present_count' dan 'attendance_percentage' per sesi
        $classSessions = DB::table('v_class_activity_logs')
            ->where('class_id', $id)
            ->orderBy('date', 'desc')
            ->paginate(5, ['*'], 'session_page'); // Gunakan pagination agar halaman tidak berat

        // 4. Sesi Hari Ini (Untuk tombol Quick Action)
        $sessionToday = ClassSession::where('class_id', $id)
                                    ->where('date', Carbon::today()->format('Y-m-d'))
                                    ->first();

        // 5. Assessment Pagination
        $assessments = AssessmentSession::where('class_id', $id)
            ->orderBy('date', 'desc')
            ->paginate(5, ['*'], 'assessment_page');

        // ====================================================
        // 6. DATA UNTUK MODAL MATRIX (VISUAL ONLY)
        // ====================================================
        // Kita tetap butuh raw data untuk membuat kotak-kotak (Matrix), 
        // tapi kita TIDAK lagi menghitung persentase di sini.
        
        $allSessions = ClassSession::where('class_id', $id)
                        ->with(['records:id,class_session_id,student_id,status', 'teacher'])
                        ->orderBy('date', 'desc') // Sesuai request: Newest first untuk list, tapi view modal mungkin butuh sort by date asc
                        ->get();

        $attendanceMatrix = [];
        
        // Build Matrix: [student_id][session_id] = status
        foreach ($allSessions as $session) {
            foreach ($session->records as $record) {
                $attendanceMatrix[$record->student_id][$session->id] = $record->status;
            }
        }

        // Untuk keperluan Modal Matrix yang menampilkan nama siswa, kita bisa reuse $studentStats
        // Karena $studentStats dari procedure sudah memuat semua siswa di kelas tersebut.

        return view('teacher.classes.detail', compact(
            'class', 
            'classSessions', // Sekarang isinya dari View v_class_activity_logs
            'assessments',
            'sessionToday',
            'allSessions',
            'attendanceMatrix',
            'studentStats'   // Sekarang isinya dari Procedure p_get_class_attendance_stats
        ));
    }
}