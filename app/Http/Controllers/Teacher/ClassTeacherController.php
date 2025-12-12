<?php

namespace App\Http\Controllers\Teacher;

use Illuminate\Http\Request;
use App\Models\AssessmentForm;
use App\Models\AttendanceRecord;
use App\Http\Controllers\Controller;
use App\Models\Student; // Model untuk siswa
use App\Models\ClassModel; // Penting: panggil Model
use App\Models\AttendanceSession; // Model untuk sesi absen
use App\Models\AssessmentSession; // Model untuk sesi penilaian
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
        $class = ClassModel::with('schedules')->findOrFail($id);

        // 1. Pagination Siswa (student_page)
        $perPage = $request->input('per_page', 5);     
        $students = Student::where('class_id', $id)
            ->where('is_active', true)
            ->paginate($perPage, ['*'], 'student_page') 
            ->appends(request()->except('student_page'));

        // 2. Pagination Attendance (attendance_page)
        $attendanceSessions = AttendanceSession::where('class_id', $id)
            ->orderBy('date', 'desc')
            ->paginate(5, ['*'], 'attendance_page');

        // 3. BARU: Pagination Assessment (assessment_page)
        $assessments = AssessmentSession::where('class_id', $id)
            ->orderBy('date', 'desc')
            ->paginate(5, ['*'], 'assessment_page');

        // Tambahkan $assessments ke compact
        return view('teacher.classes.detail', compact('class', 'students', 'attendanceSessions', 'assessments'));
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

    // 1. Menampilkan Halaman Input Nilai
    public function assessmentDetail($classId, $assessmentId)
    {
        $class = ClassModel::findOrFail($classId);
        // Load assessment beserta form nilainya
        $assessment = AssessmentSession::with('forms')->findOrFail($assessmentId);

        // Ambil siswa aktif di kelas ini, urutkan nama
        $students = Student::where('class_id', $classId)
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();

        // Map nilai existing ke setiap siswa (agar input terisi jika sudah pernah disimpan)
        foreach ($students as $student) {
            // Cari form nilai milik siswa ini
            $form = $assessment->forms->where('student_id', $student->id)->first();
            
            // Simpan objek form ke student sementara
            $student->form = $form; 
        }

        return view('teacher.classes.assessment-marks', compact('class', 'assessment', 'students'));
    }

    // 2. Menyimpan Nilai (Per Skill)
    public function updateAssessmentMarks(Request $request, $classId, $assessmentId)
    {
        // Validasi input (pastikan angka 0-100)
        $request->validate([
            'marks' => 'array',
            'marks.*.vocabulary' => 'nullable|numeric|min:0|max:100',
            'marks.*.grammar'    => 'nullable|numeric|min:0|max:100',
            'marks.*.listening'  => 'nullable|numeric|min:0|max:100',
            'marks.*.speaking'   => 'nullable|numeric|min:0|max:100',
            'marks.*.reading'    => 'nullable|numeric|min:0|max:100',
            'marks.*.spelling'   => 'nullable|numeric|min:0|max:100',
        ]);

        // Loop setiap siswa dan simpan nilainya
        // Struktur data dari view: marks[student_id][skill_name]
        foreach ($request->marks as $studentId => $scores) {
            
            AssessmentForm::updateOrCreate(
                [
                    'assessment_session_id' => $assessmentId,
                    'student_id' => $studentId,
                ],
                [
                    'vocabulary' => $scores['vocabulary'] ?? null,
                    'grammar'    => $scores['grammar'] ?? null,
                    'listening'  => $scores['listening'] ?? null,
                    'speaking'   => $scores['speaking'] ?? null,
                    'reading'    => $scores['reading'] ?? null,
                    'spelling'   => $scores['spelling'] ?? null,
                ]
            );
        }

        return redirect()->route('teacher.classes.detail', $classId)
                         ->with('success', 'Assessment marks updated successfully!');
    }

}