<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\AssessmentSession;
use App\Models\AssessmentForm;

class TeacherAssessmentController extends Controller
{
    // Menampilkan Halaman Input Nilai
    public function assessmentDetail($classId, $assessmentId)
    {
        $class = ClassModel::findOrFail($classId);
        $assessment = AssessmentSession::with('forms')->findOrFail($assessmentId);

        $students = Student::where('class_id', $classId)
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();

        foreach ($students as $student) {
            $form = $assessment->forms->where('student_id', $student->id)->first();
            $student->form = $form; 
        }

        return view('teacher.classes.assessment-marks', compact('class', 'assessment', 'students'));
    }

    // Menyimpan Nilai (Per Skill)
    public function updateAssessmentMarks(Request $request, $classId, $assessmentId)
    {
        $request->validate([
            'marks' => 'array',
            'marks.*.vocabulary' => 'nullable|numeric|min:0|max:100',
            'marks.*.grammar'    => 'nullable|numeric|min:0|max:100',
            'marks.*.listening'  => 'nullable|numeric|min:0|max:100',
            'marks.*.speaking'   => 'nullable|numeric|min:0|max:100',
            'marks.*.reading'    => 'nullable|numeric|min:0|max:100',
            'marks.*.spelling'   => 'nullable|numeric|min:0|max:100',
        ]);

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

        // Redirect kembali ke halaman Detail Kelas
        return redirect()->route('teacher.classes.detail', $classId)
                         ->with('success', 'Assessment marks updated successfully!');
    }
}