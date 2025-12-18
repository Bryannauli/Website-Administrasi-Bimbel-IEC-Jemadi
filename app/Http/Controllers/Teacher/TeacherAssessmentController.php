<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClassModel;
use App\Models\AssessmentSession;
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
        $class = ClassModel::with('formTeacher')->findOrFail($classId);
        
        // Load Session dengan relasi interviewer
        $assessment = AssessmentSession::with('interviewer')->findOrFail($assessmentId);

        // Default Interviewer logic (jika kosong, isi dengan local teacher kelas)
        if (!$assessment->interviewer_id && $class->local_teacher_id) {
            $assessment->interviewer_id = $class->local_teacher_id;
        }

        // List Guru untuk Dropdown
        $teachers = User::where('is_teacher', true)
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();

        // Ambil Data Nilai (Stored Procedure)
        $rawStudentData = DB::select('CALL p_GetAssessmentSheet(?, ?)', [$classId, $assessmentId]);

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

        return view('teacher.classes.assessment-marks', compact('class', 'assessment', 'studentData', 'teachers'));
    }

    /**
     * Menyimpan / Update Nilai (OPTIMIZED WITH JSON STORED PROCEDURE)
     */
    public function updateAssessmentMarks(Request $request, $classId, $assessmentId)
    {
        $session = AssessmentSession::findOrFail($assessmentId);

        if ($session->status !== 'draft') {
            return redirect()->back()->with('error', 'Assessment has been submitted. Changes locked.');
        }

        // 1. Validasi Input
        $request->validate([
            'written_date'   => 'required|date',
            'speaking_date'  => 'nullable|date',
            'interviewer_id' => 'nullable|exists:users,id',
            'topic'          => 'nullable|string|max:200',
            'action_type'    => 'required|in:save,submit', 
            
            'marks'                          => 'array',
            'marks.*.vocabulary'             => 'nullable|numeric|min:0|max:100',
            'marks.*.grammar'                => 'nullable|numeric|min:0|max:100',
            'marks.*.listening'              => 'nullable|numeric|min:0|max:100',
            'marks.*.reading'                => 'nullable|numeric|min:0|max:100',
            'marks.*.spelling'               => 'nullable|numeric|min:0|max:100',
            'marks.*.speaking_content'       => 'nullable|numeric|min:0|max:50',
            'marks.*.speaking_participation' => 'nullable|numeric|min:0|max:50',
        ]);

        // 2. Cek Logika Submit (Minimal 1 siswa lengkap)
        if ($request->action_type === 'submit') {
            if (!$request->has('marks') || empty($request->marks)) {
                return redirect()->back()->with('error', 'Cannot submit empty assessment.')->withInput();
            }

            $hasOneCompleteStudent = collect($request->marks)->contains(function ($scores) {
                $mandatoryFields = ['vocabulary', 'grammar', 'listening', 'reading', 'speaking_content', 'speaking_participation'];
                foreach ($mandatoryFields as $field) {
                    if (!isset($scores[$field]) || $scores[$field] === '' || $scores[$field] === null) return false;
                }
                return true;
            });

            if (!$hasOneCompleteStudent) {
                return redirect()->back()->with('error', 'Submission Failed: At least one student must have ALL mandatory grades.')->withInput();
            }
        }

        try {
            // 3. Persiapkan Data JSON untuk Procedure
            // Kita ubah array marks menjadi array of objects yang bersih (null jika kosong)
            $marksData = [];
            if ($request->has('marks')) {
                foreach ($request->marks as $studentId => $scores) {
                    $marksData[] = [
                        'student_id'             => (int) $studentId,
                        'vocabulary'             => $scores['vocabulary'] !== null ? (int) $scores['vocabulary'] : null,
                        'grammar'                => $scores['grammar'] !== null ? (int) $scores['grammar'] : null,
                        'listening'              => $scores['listening'] !== null ? (int) $scores['listening'] : null,
                        'reading'                => $scores['reading'] !== null ? (int) $scores['reading'] : null,
                        'spelling'               => $scores['spelling'] !== null ? (int) $scores['spelling'] : null,
                        'speaking_content'       => $scores['speaking_content'] !== null ? (int) $scores['speaking_content'] : null,
                        'speaking_participation' => $scores['speaking_participation'] !== null ? (int) $scores['speaking_participation'] : null,
                    ];
                }
            }
            $jsonMarks = json_encode($marksData);

            // 4. Tentukan Status Baru
            $newStatus = ($request->action_type === 'submit') ? 'submitted' : null; // Kirim null jika save draft (agar status tidak berubah di SP)

            // 5. Panggil Stored Procedure Batch (SINGLE QUERY)
            DB::statement('CALL p_SaveAssessmentBatch(?, ?, ?, ?, ?, ?, ?)', [
                $assessmentId,
                $request->written_date,
                $request->speaking_date,
                $request->interviewer_id,
                $request->topic,
                $newStatus,
                $jsonMarks // Parameter JSON
            ]);

            $msg = ($request->action_type === 'submit') 
                ? 'Assessment submitted successfully!' 
                : 'Changes saved successfully.';

            return redirect()->route('teacher.classes.assessment.detail', ['classId' => $classId, 'assessmentId' => $assessmentId])
                            ->with('success', $msg);

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Database Error: ' . $e->getMessage());
        }
    }
    
    public function storeAssessment(Request $request, $classId)
    {
        $request->validate([
            'type' => 'required|in:mid,final',
            'date' => 'required|date',
        ]);

        ClassModel::findOrFail($classId);

        // [UPDATED] Create AssessmentSession saja (SpeakingTest sudah dihapus)
        AssessmentSession::create([
            'class_id' => $classId,
            'type' => $request->type,
            'written_date' => $request->date, // Kolom baru
            'speaking_date' => null,
            'interviewer_id' => Auth::id(), // Default interviewer
            'status' => 'draft'
        ]);
        
        $typeLabel = ($request->type == 'mid') ? 'Mid Term Exam' : 'Final Exam';
        
        return redirect()->route('teacher.classes.detail', $classId)
                        ->with('success', $typeLabel . ' scheduled successfully.');
    }
}