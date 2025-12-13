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
use Illuminate\Support\Facades\DB;

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

    // Detail halaman kelas
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

    // Menyimpan sesi absensi baru
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

    // Menampilkan halaman detail sesi absensi
    public function sessionDetail($classId, $sessionId)
    {
        // 1. Ambil data Kelas dan Sesi
        $class = ClassModel::findOrFail($classId);
        $session = AttendanceSession::where('class_id', $classId)
                                    ->where('id', $sessionId)
                                    ->firstOrFail();

        // 2. Ambil semua siswa di kelas ini
        $students = $class->students() // Asumsi ada relasi students() di ClassModel
                           ->where('is_active', 1)
                           ->get();

        // 3. Ambil data absensi yang sudah ada untuk sesi ini
        $attendanceRecords = AttendanceRecord::where('attendance_session_id', $sessionId)
                                             ->pluck('status', 'student_id')
                                             ->toArray();

        // 4. Gabungkan data siswa dengan status absensi
        $students = $students->map(function ($student) use ($attendanceRecords) {
            $student->current_status = $attendanceRecords[$student->id] ?? null;
            
            // Catatan: 'permission' di DB match dengan 'permitted' di Blade
            if ($student->current_status == 'permission') {
                $student->current_status = 'permitted';
            }

            return $student;
        });

        return view('teacher.classes.session-attandance', compact('class', 'session', 'students'));
    }

    // Menyimpan atau memperbarui data absensi untuk sesi tertentu
    public function updateSession(Request $request, $classId, $sessionId)
    {
        $request->validate([
            'attendance' => 'required|array',
            'attendance.*' => 'in:present,absent,late,permitted,sick', // Validasi status
        ]);

        $session = AttendanceSession::where('class_id', $classId)
                                    ->where('id', $sessionId)
                                    ->firstOrFail();

        DB::beginTransaction();
        try {
            foreach ($request->input('attendance') as $studentId => $status) {
                // Konversi 'permitted' dari Blade menjadi 'permission' di database
                $dbStatus = ($status === 'permitted') ? 'permission' : $status;

                AttendanceRecord::updateOrCreate(
                    [
                        'attendance_session_id' => $sessionId,
                        'student_id' => $studentId,
                    ],
                    [
                        'status' => $dbStatus,
                    ]
                );
            }

            // Tambahkan logika lain, misal: membuat log atau notifikasi

            DB::commit();

            return redirect()->route('teacher.classes.detail', $classId)
                             ->with('success', 'Attendance for ' . \Carbon\Carbon::parse($session->date)->format('d F Y') . ' updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            // Log error $e
            return redirect()->back()->with('error', 'Failed to update attendance: ' . $e->getMessage());
        }
    }

    // Menampilkan Halaman Input Nilai
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

    // Menyimpan Nilai (Per Skill)
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

    // Menyimpan sesi penilaian baru (Mid Term / Final)
    public function storeAssessment(Request $request, $classId)
    {
        // 1. Validasi Input
        $request->validate([
            'type' => 'required|in:mid,final', // Tipe harus 'mid' atau 'final'
            'date' => 'required|date',
        ]);

        // Pastikan kelas ada
        ClassModel::findOrFail($classId);

        // 2. Simpan ke database
        $assessmentSession = AssessmentSession::create([
            'class_id' => $classId,
            'type' => $request->type,
            'date' => $request->date,
        ]);

        // 3. Redirect kembali ke halaman detail kelas dengan pesan sukses
        $typeLabel = ($request->type == 'mid') ? 'Mid Term Exam' : 'Final Exam';
        
        return redirect()->route('teacher.classes.detail', $classId)
                         ->with('success', $typeLabel . ' scheduled successfully on ' . \Carbon\Carbon::parse($request->date)->format('d F Y') . '.');
    }
}