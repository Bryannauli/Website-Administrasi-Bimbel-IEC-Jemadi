<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ClassModel;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;

class TeacherAttendanceController extends Controller
{
    // Menyimpan sesi absensi baru (tombol "Create Session")
    public function storeSession(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $session = AttendanceSession::create([
            'class_id' => $id,
            'date' => $request->date,
        ]);

        // Redirect ke halaman input absen
        return redirect()->route('teacher.classes.session.detail', [$id, $session->id])
            ->with('success', 'New attendance session created!');
    }

    // Menampilkan halaman input/detail absensi
    public function sessionDetail($classId, $sessionId)
    {
        $class = ClassModel::findOrFail($classId);
        $session = AttendanceSession::where('class_id', $classId)
                                    ->where('id', $sessionId)
                                    ->firstOrFail();

        $students = $class->students()
                          ->where('is_active', 1)
                          ->get();

        $attendanceRecords = AttendanceRecord::where('attendance_session_id', $sessionId)
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

    // Menyimpan/Update data absensi
    public function updateSession(Request $request, $classId, $sessionId)
    {
        $request->validate([
            'attendance' => 'required|array',
            'attendance.*' => 'in:present,absent,late,permitted,sick',
        ]);

        $session = AttendanceSession::where('class_id', $classId)
                                    ->where('id', $sessionId)
                                    ->firstOrFail();

        DB::beginTransaction();
        try {
            foreach ($request->input('attendance') as $studentId => $status) {
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