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
     * Menyimpan / Update Nilai
     */
    public function updateAssessmentMarks(Request $request, $classId, $assessmentId)
    {
        $session = AssessmentSession::findOrFail($assessmentId);

        if ($session->status !== 'draft') {
            return redirect()->back()->with('error', 'Assessment has been submitted. Changes locked.');
        }

        $request->validate([
            'written_date' => 'required|date',
            'speaking_date' => 'nullable|date',
            'interviewer_id' => 'nullable|exists:users,id',
            'topic' => 'nullable|string|max:200',
            'action_type' => 'required|in:save,submit', 
            
            'marks' => 'array',
            'marks.*.vocabulary' => 'nullable|numeric|min:0|max:100',
            'marks.*.grammar'    => 'nullable|numeric|min:0|max:100',
            'marks.*.listening'  => 'nullable|numeric|min:0|max:100',
            'marks.*.reading'    => 'nullable|numeric|min:0|max:100',
            'marks.*.spelling'   => 'nullable|numeric|min:0|max:100',
            'marks.*.speaking_content' => 'nullable|numeric|min:0|max:50',
            'marks.*.speaking_participation' => 'nullable|numeric|min:0|max:50',
        ]);

        // --- VALIDASI LOGIKA SUBMIT ---
        if ($request->action_type === 'submit') {
            if (!$request->has('marks') || empty($request->marks)) {
                return redirect()->back()->with('error', 'Cannot submit empty assessment. Please input grades.')->withInput();
            }

            $hasOneCompleteStudent = collect($request->marks)->contains(function ($scores) {
                $mandatoryFields = ['vocabulary', 'grammar', 'listening', 'reading', 'speaking_content', 'speaking_participation'];
                foreach ($mandatoryFields as $field) {
                    if (!array_key_exists($field, $scores) || $scores[$field] === null || $scores[$field] === '') return false;
                }
                return true;
            });

            if (!$hasOneCompleteStudent) {
                return redirect()->back()->with('error', 'Submission Failed: At least one student must have ALL mandatory grades.')->withInput();
            }
        }

        try {
            DB::beginTransaction();

            // [UPDATED] Update Header Assessment (Gabungan Written & Speaking)
            $updateData = [
                'written_date'   => $request->written_date,  // Kolom baru
                'speaking_date'  => $request->speaking_date, // Pindah dari tabel speaking_tests
                'speaking_topic' => $request->topic,         // Pindah dari tabel speaking_tests
                'interviewer_id' => $request->interviewer_id // Pindah dari tabel speaking_tests
            ];
            
            if ($request->action_type === 'submit') {
                $updateData['status'] = 'submitted';
            }
            
            $session->update($updateData);

            // Loop Nilai Siswa
            if ($request->has('marks')) {
                foreach ($request->marks as $studentId => $scores) {
                    // [UPDATED] Procedure call: Hapus parameter ke-4 (speaking_test_id)
                    // Total parameter sekarang 10 (sebelumnya 11)
                    DB::statement('CALL p_UpdateStudentGrade(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                        $assessmentId,                          
                        $studentId,                             
                        0, // p_form_id (dummy, handled inside proc)
                        
                        // Nilai Written
                        $scores['vocabulary'] ?? null,          
                        $scores['grammar'] ?? null,             
                        $scores['listening'] ?? null,           
                        $scores['reading'] ?? null,             
                        $scores['spelling'] ?? null,            
                        
                        // Nilai Speaking
                        $scores['speaking_content'] ?? null,      
                        $scores['speaking_participation'] ?? null 
                    ]);
                }
            }

            DB::commit();

            $msg = ($request->action_type === 'submit') 
                ? 'Assessment submitted successfully!' 
                : 'Changes saved successfully.';

            return redirect()->route('teacher.classes.assessment.detail', ['classId' => $classId, 'assessmentId' => $assessmentId])
                            ->with('success', $msg);

        } catch (\Exception $e) {
            DB::rollBack();
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