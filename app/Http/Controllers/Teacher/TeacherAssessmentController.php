<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\AssessmentSession;
use App\Models\AssessmentForm;
use App\Models\SpeakingTest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TeacherAssessmentController extends Controller
{
    public function assessmentDetail($classId, $assessmentId)
    {
        // 1. Load Form Teacher (Wali Kelas)
        $class = ClassModel::with('formTeacher')->findOrFail($classId);
        
        $assessment = AssessmentSession::with('forms')->findOrFail($assessmentId);

        $speakingTest = SpeakingTest::firstOrCreate(
            ['assessment_session_id' => $assessment->id],
            ['date' => null, 'interviewer_id' => $class->local_teacher_id]
        );
        $speakingTest->load('interviewer');

        $teachers = User::where('is_teacher', true)
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();

        // UPDATE: Mengurutkan berdasarkan student_number
        $students = Student::where('class_id', $classId)
            ->where('is_active', true)
            ->orderBy('student_number', 'asc') // <-- Perubahan disini
            ->get();

        foreach ($students as $student) {
            $form = $assessment->forms->where('student_id', $student->id)->first();
            $speakingDetail = DB::table('speaking_test_results')
                ->where('speaking_test_id', $speakingTest->id)
                ->where('student_id', $student->id)
                ->first();

            $student->form = $form; 
            $student->speaking_detail = $speakingDetail; 
        }

        return view('teacher.classes.assessment-marks', compact('class', 'assessment', 'students', 'speakingTest', 'teachers'));
    }

    public function updateAssessmentMarks(Request $request, $classId, $assessmentId)
    {
        $session = AssessmentSession::findOrFail($assessmentId);

        // SECURITY CHECK: Guru tidak boleh edit jika status sudah submitted/final
        if ($session->status !== 'draft') {
            return redirect()->back()->with('error', 'Assessment has already been submitted. You cannot edit it anymore.');
        }

        $request->validate([
            'written_date' => 'required|date',
            'speaking_date' => 'nullable|date',
            'interviewer_id' => 'nullable|exists:users,id',
            'topic' => 'nullable|string|max:200',
            'action_type' => 'required|in:save,submit', // Validasi tombol aksi
            'marks' => 'array',
            'marks.*.vocabulary' => 'nullable|numeric|min:0|max:100',
            'marks.*.grammar'    => 'nullable|numeric|min:0|max:100',
            'marks.*.listening'  => 'nullable|numeric|min:0|max:100',
            'marks.*.speaking_content' => 'nullable|numeric|min:0|max:50',
            'marks.*.speaking_participation' => 'nullable|numeric|min:0|max:50',
            'marks.*.reading'    => 'nullable|numeric|min:0|max:100',
            'marks.*.spelling'   => 'nullable|numeric|min:0|max:100',
        ]);

        DB::transaction(function () use ($request, $session, $assessmentId) {
            // 1. Update Tanggal & Status
            $updateData = ['date' => $request->written_date];
            
            // Jika tombol SUBMIT ditekan, ubah status jadi 'submitted'
            if ($request->action_type === 'submit') {
                $updateData['status'] = 'submitted';
            }

            $session->update($updateData);

            // 2. Update Info Speaking
            SpeakingTest::updateOrCreate(
                ['assessment_session_id' => $assessmentId],
                [
                    'date' => $request->speaking_date,
                    'interviewer_id' => $request->interviewer_id,
                    'topic' => $request->topic
                ]
            );
            
            $speakingTest = SpeakingTest::where('assessment_session_id', $assessmentId)->first();

            // 3. Simpan Nilai
            if ($request->has('marks')) {
                foreach ($request->marks as $studentId => $scores) {
                    $sContent = $scores['speaking_content'] ?? null;
                    $sPartic = $scores['speaking_participation'] ?? null;
                    
                    $totalSpeaking = ($sContent !== null || $sPartic !== null) 
                        ? ((int)$sContent + (int)$sPartic) 
                        : null;

                    DB::table('speaking_test_results')->updateOrInsert(
                        ['speaking_test_id' => $speakingTest->id, 'student_id' => $studentId],
                        [
                            'content_score' => $sContent,
                            'participation_score' => $sPartic,
                            'updated_at' => now()
                        ]
                    );

                    AssessmentForm::updateOrCreate(
                        ['assessment_session_id' => $assessmentId, 'student_id' => $studentId],
                        [
                            'vocabulary' => $scores['vocabulary'] ?? null,
                            'grammar'    => $scores['grammar'] ?? null,
                            'listening'  => $scores['listening'] ?? null,
                            'speaking'   => $totalSpeaking,
                            'reading'    => $scores['reading'] ?? null,
                            'spelling'   => $scores['spelling'] ?? null,
                        ]
                    );
                }
            }
        });

        // Pesan Feedback Berbeda
        $msg = ($request->action_type === 'submit') 
            ? 'Assessment submitted successfully! Waiting for Admin review.' 
            : 'Changes saved successfully.';

        return redirect()->route('teacher.classes.assessment.detail', ['classId' => $classId, 'assessmentId' => $assessmentId])
                         ->with('success', $msg);
    }
    
    public function storeAssessment(Request $request, $classId)
    {
        $request->validate([
            'type' => 'required|in:mid,final',
            'date' => 'required|date',
        ]);

        ClassModel::findOrFail($classId);

        $assessmentSession = AssessmentSession::create([
            'class_id' => $classId,
            'type' => $request->type,
            'date' => $request->date,
        ]);
        
        SpeakingTest::create([
            'assessment_session_id' => $assessmentSession->id,
            'date' => null, 
            'interviewer_id' => Auth::id()
        ]);

        $typeLabel = ($request->type == 'mid') ? 'Mid Term Exam' : 'Final Exam';
        
        return redirect()->route('teacher.classes.detail', $classId)
                         ->with('success', $typeLabel . ' scheduled successfully.');
    }
}