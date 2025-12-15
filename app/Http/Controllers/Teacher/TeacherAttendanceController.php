<?php

namespace App\Http\Controllers\Teacher;

use App\Models\ClassModel;
use App\Models\ClassSession;
use Illuminate\Http\Request;
use App\Models\AttendanceRecord;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TeacherAttendanceController extends Controller
{
    // Menyimpan sesi absensi baru (tombol "Create Session")
    public function storeSession(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date',
            'topics' => 'required|string',
        ]);

        // Cek Duplikasi: Apakah sudah ada sesi pada tanggal ini untuk kelas ini?
        $existingSession = ClassSession::where('class_id', $id)
                                        ->where('date', $request->date)
                                        ->first();
        
        if ($existingSession) {
            return redirect()->back()
                ->with('error', 'Attendance session already exists for this date. Please edit the existing session.')
                ->withInput(); // Opsional: mempertahankan input
        }

        $session = ClassSession::create([
            'class_id' => $id,
            'date' => $request->date,
            'comment' => $request->topics,
            'teacher_id' => Auth::id(),
        ]);

        // Redirect ke halaman input absen
        return redirect()->route('teacher.classes.session.detail', [$id, $session->id])
            ->with('success', 'New attendance session created!');
    }

    // Menampilkan halaman input/detail absensi (Tidak ada perubahan di sini)
    public function sessionDetail($classId, $sessionId)
    {
        $class = ClassModel::findOrFail($classId);
        $session = ClassSession::where('class_id', $classId)
                                    ->where('id', $sessionId)
                                    ->firstOrFail();

        $students = $class->students()
                        ->where('is_active', 1)
                        ->get();

        $attendanceRecords = AttendanceRecord::where('class_session_id', $sessionId)
                                            ->pluck('status', 'student_id')
                                            ->toArray();

        $students = $students->map(function ($student) use ($attendanceRecords) {
            $student->current_status = $attendanceRecords[$student->id] ?? null;
            if ($student->current_status == 'permission') {
                $student->current_status = 'permitted';
            }
            return $student;
        });

        return view('teacher.classes.session-attandance', compact('class', 'session', 'students'));
    }

    // Menyimpan/Update data absensi (Tidak ada perubahan di sini)
    public function updateSession(Request $request, $classId, $sessionId)
    {
        $request->validate([
            'attendance' => 'required|array',
            'attendance.*' => 'in:present,absent,late,permitted,sick',
        ]);

        $session = ClassSession::where('class_id', $classId)
                                    ->where('id', $sessionId)
                                    ->firstOrFail();

        DB::beginTransaction();
        try {
            foreach ($request->input('attendance') as $studentId => $status) {
                $dbStatus = ($status === 'permitted') ? 'permission' : $status;

                AttendanceRecord::updateOrCreate(
                    [
                        'class_session_id' => $sessionId,
                        'student_id' => $studentId,
                    ],
                    [
                        'status' => $dbStatus,
                    ]
                );
            }

            DB::commit();

            // Redirect kembali ke halaman Detail Kelas
            return redirect()->route('teacher.classes.detail', $classId)
                            ->with('success', 'Attendance updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed: ' . $e->getMessage());
        }
    }
}