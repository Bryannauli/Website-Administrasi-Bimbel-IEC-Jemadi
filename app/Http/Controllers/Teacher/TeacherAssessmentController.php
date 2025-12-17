<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\AssessmentSession;
use App\Models\SpeakingTest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TeacherAssessmentController extends Controller
{
    /**
     * Menampilkan Halaman Input Nilai
     */
    public function assessmentDetail($classId, $assessmentId)
    {
        // 1. Load Data Kelas & Guru
        $class = ClassModel::with('formTeacher')->findOrFail($classId);
        $assessment = AssessmentSession::findOrFail($assessmentId);

        // 2. Pastikan Row Speaking Test ada
        $speakingTest = SpeakingTest::firstOrCreate(
            ['assessment_session_id' => $assessment->id],
            ['date' => null, 'interviewer_id' => $class->local_teacher_id]
        );
        $speakingTest->load('interviewer');

        // 3. List Guru untuk Dropdown Interviewer
        $teachers = User::where('is_teacher', true)
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();

        // =================================================================
        // OPTIMISASI: MENGGUNAKAN STORED PROCEDURE (SAMA DENGAN ADMIN)
        // =================================================================
        
        // Panggil Procedure yang sama persis dengan Admin.
        $rawStudentData = DB::select('CALL p_GetAssessmentSheet(?, ?)', [$classId, $assessmentId]);

        // Mapping Data agar sesuai struktur yang diharapkan View (Array Based)
        // Kita samakan strukturnya dengan AdminAssessmentController
        $studentData = collect($rawStudentData)->map(function ($row) {
            return [
                'id' => $row->student_id,
                'name' => $row->name,
                'student_number' => $row->student_number,
                'is_active' => $row->is_active,
                'deleted_at' => $row->deleted_at,
                'current_class_id' => $row->current_class_id,
                'written' => [
                    'form_id' => $row->form_id,
                    'vocabulary' => $row->vocabulary,
                    'grammar' => $row->grammar,
                    'listening' => $row->listening,
                    'reading' => $row->reading,
                    'spelling' => $row->spelling,
                ],
                'speaking' => [
                    'content' => $row->speaking_content,
                    'participation' => $row->speaking_participation,
                    'total' => $row->speaking_total,
                ],
                'avg_score' => $row->final_score,
                'grade_text' => $row->grade_text ?? '-',
            ];
        });

        // Kirim variable $studentData (bukan $students) ke view
        return view('teacher.classes.assessment-marks', compact('class', 'assessment', 'studentData', 'speakingTest', 'teachers'));
    }

    /**
     * Menyimpan / Update Nilai (Menggunakan Stored Procedure)
     */
    public function updateAssessmentMarks(Request $request, $classId, $assessmentId)
    {
        $session = AssessmentSession::findOrFail($assessmentId);

        // SECURITY: Kunci jika status bukan draft
        if ($session->status !== 'draft') {
            return redirect()->back()->with('error', 'Assessment has been submitted. Changes locked.');
        }

        // 1. Validasi Input
        $request->validate([
            'written_date' => 'required|date',
            'speaking_date' => 'nullable|date',
            'interviewer_id' => 'nullable|exists:users,id',
            'topic' => 'nullable|string|max:200',
            'action_type' => 'required|in:save,submit', 
            
            // Validasi Array Nilai
            'marks' => 'array',
            'marks.*.vocabulary' => 'nullable|numeric|min:0|max:100',
            'marks.*.grammar'    => 'nullable|numeric|min:0|max:100',
            'marks.*.listening'  => 'nullable|numeric|min:0|max:100',
            'marks.*.reading'    => 'nullable|numeric|min:0|max:100',
            'marks.*.spelling'   => 'nullable|numeric|min:0|max:100',
            // Speaking max 50 karena totalnya 100
            'marks.*.speaking_content' => 'nullable|numeric|min:0|max:50',
            'marks.*.speaking_participation' => 'nullable|numeric|min:0|max:50',
        ]);

        try {
            DB::beginTransaction();

            // 2. Update Header Assessment (Tanggal & Status)
            $updateData = ['date' => $request->written_date];
            if ($request->action_type === 'submit') {
                $updateData['status'] = 'submitted';
            }
            $session->update($updateData);

            // 3. Update Header Speaking Test
            $speakingTest = SpeakingTest::updateOrCreate(
                ['assessment_session_id' => $assessmentId],
                [
                    'date' => $request->speaking_date,
                    'interviewer_id' => $request->interviewer_id,
                    'topic' => $request->topic
                ]
            );

            // =============================================================
            // 4. LOOP & CALL PROCEDURE (Logika Inti)
            // =============================================================
            if ($request->has('marks')) {
                foreach ($request->marks as $studentId => $scores) {
                    
                    // Panggil Stored Procedure: p_UpdateStudentGrade
                    // Procedure ini menangani: 
                    // - Insert/Update ke tabel assessment_forms
                    // - Insert/Update ke tabel speaking_test_results
                    // - Menghitung total speaking (content + participation)
                    
                    DB::statement('CALL p_UpdateStudentGrade(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                        $assessmentId,                          // p_session_id
                        $studentId,                             // p_student_id
                        0,                                      // p_form_id (Set 0/null karena procedure handle by logic)
                        $speakingTest->id,                      // p_speaking_test_id
                        
                        $scores['vocabulary'] ?? null,          // p_vocab
                        $scores['grammar'] ?? null,             // p_grammar
                        $scores['listening'] ?? null,           // p_listening
                        $scores['reading'] ?? null,             // p_reading
                        $scores['spelling'] ?? null,            // p_spelling
                        
                        $scores['speaking_content'] ?? null,      // p_s_content
                        $scores['speaking_participation'] ?? null // p_s_partic
                    ]);
                }
            }

            DB::commit();

            // Feedback Message
            $msg = ($request->action_type === 'submit') 
                ? 'Assessment submitted successfully! Waiting for Admin review.' 
                : 'Changes saved successfully.';

            return redirect()->route('teacher.classes.assessment.detail', ['classId' => $classId, 'assessmentId' => $assessmentId])
                            ->with('success', $msg);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Database Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Membuat Sesi Assessment Baru (Create)
     */
    public function storeAssessment(Request $request, $classId)
    {
        $request->validate([
            'type' => 'required|in:mid,final',
            'date' => 'required|date',
        ]);

        ClassModel::findOrFail($classId);

        // Gunakan Eloquent biasa karena ini simple insert header
        $assessmentSession = AssessmentSession::create([
            'class_id' => $classId,
            'type' => $request->type,
            'date' => $request->date,
            'status' => 'draft'
        ]);
        
        // Buat dummy speaking test row
        SpeakingTest::create([
            'assessment_session_id' => $assessmentSession->id,
            'date' => null, 
            'interviewer_id' => Auth::id() // Default interviewer diri sendiri
        ]);

        $typeLabel = ($request->type == 'mid') ? 'Mid Term Exam' : 'Final Exam';
        
        return redirect()->route('teacher.classes.detail', $classId)
                        ->with('success', $typeLabel . ' scheduled successfully.');
    }
}