<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TeacherAttendanceRecord; // <--- Pastikan Model ini di-import
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class TeacherAttendanceRecordController extends Controller
{
    /**
     * Halaman Rekap Absensi (Index)
     */
    public function index()
    {
        // Contoh ambil semua data absensi dengan paginasi
        $records = TeacherAttendanceRecord::with(['teacher', 'session'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);

        return view('admin.attendance.index', compact('records'));
    }

    /**
     * Halaman Detail Absensi per Guru
     */
    public function detail($teacherId)
    {
        // 1. Cek Data Guru (TETAP menggunakan Eloquent)
        $teacher = User::findOrFail($teacherId);

        // 2. Ambil Data Absensi menggunakan View
        // View v_teacher_attendance sudah melakukan JOIN ke attendance_sessions
        $attendance = DB::table('v_teacher_attendance')
            ->where('teacher_id', $teacherId)
            ->orderBy('record_id', 'desc')
            ->get();
            // Hasilnya adalah koleksi standar (stdClass) dari Query Builder

        return view('admin.attendance.detail', compact('teacher', 'attendance'));
    }

public function teacher(Request $request)
    {
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));
        
        // --- DATA STATISTIK DUMMY ---
        $totalPresent = 18;
        $totalLate    = 4;
        $totalSick    = 1;
        $totalAbsent  = 2;

        // --- DATA TABEL DUMMY (Tanpa NIP, Class Level/Step) ---
        $dummyRecords = collect([]);

        // Data 1: Richard Lim
        $dummyRecords->push((object)[
            'id' => 1,
            'class_name' => 'Level 5',     // <--- Ubah jadi Level/Step
            'schedule_time' => '08:00 - 10:00',
            'teacher' => (object)[ 'id' => 101, 'name' => 'Richard Lim' ], // <--- NIP Dihapus
            'status' => 'present',
            'check_in' => '07:55',
            'check_out' => '10:05',
        ]);

        // Data 2: Sarah Connor
        $dummyRecords->push((object)[
            'id' => 2,
            'class_name' => 'Step 2',      // <--- Ubah jadi Level/Step
            'schedule_time' => '13:00 - 15:00',
            'teacher' => (object)[ 'id' => 102, 'name' => 'Sarah Connor' ],
            'status' => 'late',
            'check_in' => '13:15',
            'check_out' => '15:00',
        ]);

        // Data 3: Dr. Emmett
        $dummyRecords->push((object)[
            'id' => 3,
            'class_name' => 'Level 2',     // <--- Ubah jadi Level/Step
            'schedule_time' => '16:00 - 18:00',
            'teacher' => (object)[ 'id' => 103, 'name' => 'Dr. Emmett Brown' ],
            'status' => 'absent',
            'check_in' => '-',
            'check_out' => '-',
        ]);

        // Generate Data Random Tambahan
        $classTypes = ['Level 1', 'Level 2', 'Level 3', 'Level 4', 'Level 5', 'Step 1', 'Step 2', 'Step 3'];
        $teacherNames = ['Jessica Jane', 'Budi Santoso', 'John Doe', 'Emily Watson', 'Robert Downey', 'Chris Evans', 'Scarlett Jo'];

        foreach($teacherNames as $index => $name) {
            $cls = $classTypes[array_rand($classTypes)];
            $start = rand(8, 16);
            $end = $start + 2;
            
            $status = ['present', 'present', 'sick', 'present'][rand(0, 3)];

            $dummyRecords->push((object)[
                'id' => $index + 4,
                'class_name' => $cls,
                'schedule_time' => sprintf("%02d:00 - %02d:00", $start, $end),
                'teacher' => (object)[ 'id' => 104+$index, 'name' => $name ], // <--- NIP Dihapus
                'status' => $status,
                'check_in' => $status == 'present' ? sprintf("%02d:00", $start) : '-',
                'check_out' => $status == 'present' ? sprintf("%02d:00", $end) : '-',
            ]);
        }

        // Pagination
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $records = new LengthAwarePaginator($dummyRecords, 10, 10, $currentPage, ['path' => $request->url()]);
        
        return view('admin.attendance.teacher', compact('records', 'date', 'totalPresent', 'totalLate', 'totalAbsent', 'totalSick'));
    }
    /**
     * LOGIKA GENERATE DARI JADWAL (SIMULASI)
     */
    public function store(Request $request)
    {
        $date = Carbon::parse($request->date);
        $dayName = $date->format('l'); // Monday, Tuesday, etc.

        // LOGIKA ASLI NANTINYA:
        // 1. Cari di tabel 'schedules' where day = $dayName
        // 2. Loop jadwal tersebut
        // 3. Create TeacherAttendanceRecord berdasarkan jadwal guru tersebut

        return back()->with('success', "Attendance generated based on Active Class Schedules for $dayName, " . $date->format('d M Y'));
    }
}