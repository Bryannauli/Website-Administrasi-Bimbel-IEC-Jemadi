<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\ClassSession;
use App\Models\AssessmentSession;

class TeacherClassController extends Controller
{
    // Menampilkan daftar kelas
    public function index()
    {
        $user = Auth::user();

        $classes = ClassModel::with('schedules')
            ->where('form_teacher_id', $user->id)
            ->orWhere('local_teacher_id', $user->id)
            ->paginate(10);

        return view('teacher.classes.index', compact('classes'));
    }

    // Menampilkan detail kelas (List Siswa, History Absen, History Nilai)
    public function detail(Request $request, $id)
    {
        $class = ClassModel::with('schedules')->findOrFail($id);

        // 1. Pagination Siswa
        $perPage = $request->input('per_page', 5);     
        $students = Student::where('class_id', $id)
            ->where('is_active', true)
            ->paginate($perPage, ['*'], 'student_page') 
            ->appends(request()->except('student_page'));

        // 2. Pagination Attendance
        $attendanceSessions = ClassSession::where('class_id', $id)
            ->orderBy('date', 'desc')
            ->paginate(5, ['*'], 'attendance_page');

        // 3. Pagination Assessment
        $assessments = AssessmentSession::where('class_id', $id)
            ->orderBy('date', 'desc')
            ->paginate(5, ['*'], 'assessment_page');

        return view('teacher.classes.detail', compact('class', 'students', 'attendanceSessions', 'assessments'));
    }
}