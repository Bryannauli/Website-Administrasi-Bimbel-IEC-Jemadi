<?php

namespace App\Http\Controllers\Teacher;

use Illuminate\Http\Request;
use App\Models\AttendanceRecord;
use App\Http\Controllers\Controller;
use App\Models\Student; // Model untuk siswa
use App\Models\ClassModel; // Penting: panggil Model
use App\Models\AttendanceSession; // Model untuk sesi absen
use Illuminate\Support\Facades\Auth; // Penting: untuk Auth::id()

class ClassTeacherController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Ambil kelas dimana user adalah form_teacher atau local_teacher
        // Eager load 'schedules' untuk menghindari N+1 query problem
        $classes = ClassModel::with('schedules')
            ->where('form_teacher_id', $user->id)
            ->orWhere('local_teacher_id', $user->id)
            ->paginate(10); // Pagination 10 per halaman

        return view('teacher.classes.index', compact('classes'));
    }

    public function detail(Request $request, $id)
    {
        // Ambil data kelas
        $class = ClassModel::with('schedules')->findOrFail($id);

        // --- Logic Pagination Siswa ---
        // Default 5, atau ambil dari dropdown 'per_page'
        $perPage = $request->input('per_page', 5);     
        $students = Student::where('class_id', $id)
            ->where('is_active', true)
            ->paginate($perPage, ['*'], 'student_page') // 'student_page' = nama parameter page khusus siswa
            ->appends(request()->except('student_page')); // Agar filter per_page tidak hilang saat klik next

        // --- Logic Pagination Riwayat Absensi ---
        $attendanceSessions = AttendanceSession::where('class_id', $id)
            ->orderBy('date', 'desc')
            ->paginate(5, ['*'], 'attendance_page');

        return view('teacher.classes.detail', compact('class', 'students', 'attendanceSessions'));
    }

    // public function detail($id)
    // {
    //     // Redirect ke show atau gunakan view terpisah
    //     return view('teacher.classes.detail');
    // }

    public function storeSession(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date',
            // 'start_time' => 'required', // Aktifkan jika tabel attendance_sessions sudah punya kolom start_time
            // 'end_time' => 'required',   // Aktifkan jika tabel attendance_sessions sudah punya kolom end_time
        ]);

        // Simpan ke database
        $session = AttendanceSession::create([
            'class_id' => $id,
            'date' => $request->date,
            // Tambahkan start_time & end_time di sini jika Anda sudah menambahkan kolomnya di migration
        ]);

        // Redirect ke halaman absen untuk sesi yang baru dibuat
        return redirect()->route('teacher.classes.session.detail', [$id, $session->id])
            ->with('success', 'New attendance session created!');
    }

    public function sessionDetail($classId, $sessionId)
    {
        $class = ClassModel::findOrFail($classId);
        
        // Ambil Session beserta record absensinya (jika sudah pernah diisi)
        $session = AttendanceSession::with('records')->findOrFail($sessionId);

        // Ambil Siswa Aktif di Kelas Tersebut
        $students = Student::where('class_id', $classId)
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();

        // Map status kehadiran existing ke setiap siswa (untuk logic 'checked' di radio button)
        // Jika belum ada record, defaultnya null (atau bisa kita set 'present')
        foreach($students as $student) {
            $existingRecord = $session->records->where('student_id', $student->id)->first();
            $student->current_status = $existingRecord ? $existingRecord->status : null; 
        }

        return view('teacher.classes.session-attandance', compact('class', 'session', 'students'));
    }

    public function updateSession(Request $request, $classId, $sessionId)
    {
        // Validasi input array
        $request->validate([
            'attendance' => 'required|array',
            'attendance.*' => 'in:present,absent,permitted,sick,late',
        ]);

        // Looping setiap data yang dikirim (key = student_id, value = status)
        foreach ($request->attendance as $studentId => $status) {
            AttendanceRecord::updateOrCreate(
                [
                    'attendance_session_id' => $sessionId,
                    'student_id' => $studentId,
                ],
                [
                    'status' => $status
                ]
            );
        }

        // Redirect kembali ke detail kelas dengan pesan sukses
        return redirect()->route('teacher.classes.detail', $classId)
                         ->with('success', 'Attendance recorded successfully!');
    }

}