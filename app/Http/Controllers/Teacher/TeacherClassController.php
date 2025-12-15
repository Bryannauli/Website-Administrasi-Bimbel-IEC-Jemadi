<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\ClassSession;
use App\Models\AssessmentSession;
use Carbon\Carbon;

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
        $class = ClassModel::with(['schedules', 'formTeacher', 'localTeacher'])->findOrFail($id);
        
        // 2. Query Siswa (Tanpa Pagination, Urut Student Number)
        $students = Student::where('class_id', $id)
            ->where('is_active', true)
            ->orderBy('student_number', 'asc') // <--- URUTKAN BERDASARKAN ID
            ->get();

        // 3. Ambil Sesi Hari Ini (PENTING untuk tombol Create/Edit)
        $sessionToday = ClassSession::where('class_id', $id)
                                    ->where('date', Carbon::today()->format('Y-m-d'))
                                    ->first();

        // 4. Pagination Sesi & Assessment (Untuk Widget Sidebar/Card)
        $classSessions = ClassSession::where('class_id', $id)
            ->with('teacher') 
            ->orderBy('date', 'desc')
            ->get();

        $assessments = AssessmentSession::where('class_id', $id)
            ->orderBy('date', 'desc')
            ->paginate(5, ['*'], 'assessment_page');

        // ====================================================
        // 5. DATA UNTUK MODAL MATRIX & STATS (FULL REPORT)
        // ====================================================
        
        // A. Ambil SEMUA sesi (Tanpa pagination) untuk kolom Matrix
        $allSessions = ClassSession::where('class_id', $id)
                        ->with(['records', 'teacher'])
                        ->orderBy('date', 'desc')
                        ->get();

        // B. Ambil SEMUA siswa aktif untuk baris Matrix (Konsisten dengan tabel utama)
        $allStudents = Student::where('class_id', $id)
                        ->where('is_active', true)
                        ->orderBy('student_number', 'asc') 
                        ->get();

        $attendanceMatrix = [];
        $studentStats = [];

        // C. Inisialisasi Struktur Stats
        foreach($allStudents as $student) {
            $studentStats[$student->id] = [
                'student_id' => $student->id,
                'name' => $student->name,
                'student_number' => $student->student_number,
                'is_active' => $student->is_active,
                'total' => 0,   // Total pertemuan
                'present' => 0, // Hadir/Late
                'percentage' => 0
            ];
        }

        // D. Loop Sesi & Record untuk Mengisi Matrix & Menghitung Stats
        foreach ($allSessions as $session) {
            foreach ($session->records as $record) {
                // 1. Isi Matrix: [student_id][session_id] = status
                $attendanceMatrix[$record->student_id][$session->id] = $record->status;

                // 2. Hitung Stats (Hanya jika siswa masih terdaftar di array stats)
                if (isset($studentStats[$record->student_id])) {
                    $studentStats[$record->student_id]['total']++;
                    
                    // Asumsi: 'present' dan 'late' dihitung sebagai kehadiran
                    if (in_array($record->status, ['present', 'late'])) {
                        $studentStats[$record->student_id]['present']++;
                    }
                }
            }
        }

        // E. Hitung Persentase Final
        foreach ($studentStats as &$stat) {
            $stat['percentage'] = $stat['total'] > 0 
                ? round(($stat['present'] / $stat['total']) * 100) 
                : 0;
        }
        
        // F. Ubah array Stats ke Collection Object
        $studentStats = collect($studentStats)->map(fn($item) => (object) $item);

        return view('teacher.classes.detail', compact(
            'class', 
            'students', 
            'classSessions', 
            'assessments',
            // Data Tambahan untuk Modal:
            'allSessions',
            'attendanceMatrix',
            'studentStats',
            'sessionToday' // <--- KIRIM DATA SESI HARI INI
        ));
    }
}