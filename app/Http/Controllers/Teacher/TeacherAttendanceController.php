<?php

namespace App\Http\Controllers\Teacher;

use App\Models\ClassModel;
use App\Models\ClassSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TeacherAttendanceController extends Controller
{
    // Store Session (Eloquent cukup, karena simple insert header)
    public function storeSession(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date',
            'topics' => 'required|string',
        ]);

        $existingSession = ClassSession::where('class_id', $id)
                                        ->where('date', $request->date)
                                        ->first();
        
        if ($existingSession) {
            return redirect()->back()
                ->with('error', 'Attendance session already exists for this date.')
                ->withInput();
        }

        $session = ClassSession::create([
            'class_id' => $id,
            'date' => $request->date,
            'comment' => $request->topics,
            'teacher_id' => Auth::id(),
        ]);

        return redirect()->route('teacher.classes.session.detail', [$id, $session->id])
            ->with('success', 'New attendance session created!');
    }

    // Session Detail (OPTIMIZED)
    public function sessionDetail($classId, $sessionId)
    {
        $class = ClassModel::findOrFail($classId);
        $session = ClassSession::where('class_id', $classId)
                                    ->where('id', $sessionId)
                                    ->firstOrFail();

        // [OPTIMISASI] Panggil Procedure
        // Procedure ini sudah menggabungkan Students + Attendance Records
        // dan sudah diurutkan berdasarkan student_number.
        // Output sudah berupa object list dengan property 'current_status'.
        $studentsRaw = DB::select('CALL p_GetSessionAttendanceList(?, ?)', [$classId, $sessionId]);
        
        // Ubah ke collection agar kompatibel dengan view (jika view butuh method collection)
        // tapi array of objects raw pun sebenarnya bisa di-loop di blade.
        $students = collect($studentsRaw);

        return view('teacher.classes.session-attandance', compact('class', 'session', 'students'));
    }

    // Update Session (OPTIMIZED WITH JSON BATCH)
    public function updateSession(Request $request, $classId, $sessionId)
    {
        $request->validate([
            'attendance' => 'required|array',
            'attendance.*' => 'in:present,absent,late,permitted,sick',
        ]);

        // Pastikan sesi valid milik kelas ini
        ClassSession::where('class_id', $classId)->where('id', $sessionId)->firstOrFail();

        try {
            // 1. Persiapkan Data JSON
            // Ubah format array [id => status] menjadi array of objects [{"student_id": 1, "status": "present"}, ...]
            $attendanceData = [];
            foreach ($request->input('attendance') as $studentId => $status) {
                // Normalisasi status ('permitted' di view -> 'permission' di database)
                $dbStatus = ($status === 'permitted') ? 'permission' : $status;
                
                $attendanceData[] = [
                    'student_id' => (int) $studentId,
                    'status'     => $dbStatus
                ];
            }
            
            $jsonAttendance = json_encode($attendanceData);

            // 2. Panggil Stored Procedure Batch (SINGLE QUERY)
            // Procedure ini menangani Transaction, Insert, dan Update sekaligus.
            DB::statement('CALL p_SaveAttendanceBatch(?, ?)', [
                $sessionId,
                $jsonAttendance
            ]);

            return redirect()->route('teacher.classes.detail', $classId)
                            ->with('success', 'Attendance updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed: ' . $e->getMessage());
        }
    }
}