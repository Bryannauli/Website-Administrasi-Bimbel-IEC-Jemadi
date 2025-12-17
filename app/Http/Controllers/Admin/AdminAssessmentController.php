<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\AssessmentSession;
use App\Models\AssessmentForm;
use App\Models\SpeakingTest;
use App\Models\SpeakingTestResult;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminAssessmentController extends Controller
{
    /**
     * Menampilkan daftar semua sesi penilaian (Index Global)
     */
    public function index(Request $request)
    {
        // 1. Query dasar dengan Eager Loading
        $query = AssessmentSession::with(['classModel' => function($q) {
            $q->select('id', 'name', 'category', 'academic_year', 'is_active'); 
        }]);

        // --- FILTERING LOGIC ---
        
        // Search by Class Name
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->whereHas('classModel', function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%");
            });
        }
        
        // Filter by Academic Year
        if ($request->filled('academic_year')) {
            $query->whereHas('classModel', function($q) use ($request) {
                $q->where('academic_year', $request->academic_year);
            });
        }

        // Filter by Category (Level/Step/etc)
        if ($request->filled('category')) {
            $query->whereHas('classModel', function($q) use ($request) {
                $q->where('category', $request->category);
            });
        }

        // Filter by Exam Type (Mid/Final)
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by Class ID
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        // Filter by Assessment Status (Draft/Submitted/Final)
        if ($request->filled('assessment_status')) {
            $query->where('status', $request->assessment_status);
        }

        // Filter by Class Active Status
        $statusFilter = $request->get('class_status', 'active');
        if ($statusFilter != '') {
            $isActive = $statusFilter === 'active';
            $query->whereHas('classModel', function($q) use ($isActive) {
                $q->where('is_active', $isActive);
            });
        }
        
        // 2. Pagination & Sorting
        $assessments = $query->orderBy('date', 'desc')->paginate(10);
        $assessments->appends($request->all());

        // 3. Data Pendukung untuk Dropdown Filter
        $categories = ['pre_level', 'level', 'step', 'private'];
        $years = ClassModel::select('academic_year')->distinct()->pluck('academic_year')->sortDesc();
        $types = ['mid', 'final'];
        $classes = ClassModel::select('id', 'name')->orderBy('name', 'asc')->get();
        $statuses = ['draft', 'submitted', 'final'];

        return view('admin.assessment.assessment', compact('assessments', 'categories', 'years', 'types', 'classes', 'statuses'));
    }
    
    /**
     * Menampilkan form input nilai (Detail View)
     * [UPDATED] Menggunakan Stored Procedure p_GetAssessmentSheet
     */
    public function detail(Request $request, $classId, $type)
    {
        if (!in_array($type, ['mid', 'final'])) {
            abort(404);
        }

        // 1. First or Create Session
        $session = AssessmentSession::firstOrCreate(
            ['class_id' => $classId, 'type' => $type],
            ['date' => null]
        );

        // 2. Load Data Kelas
        $class = ClassModel::with(['localTeacher', 'formTeacher'])->findOrFail($classId);

        // 3. AMBIL DATA SISWA + NILAI DARI STORED PROCEDURE
        // Procedure ini sudah menangani logika: Siswa Aktif OR Siswa Keluar tapi punya nilai
        $rawStudentData = DB::select('CALL p_GetAssessmentSheet(?, ?)', [$classId, $session->id]);

        $allTeachers = User::where('role', 'teacher')->where('is_active', true)->orderBy('name', 'asc')->get();

        // 4. Info Speaking Test
        $speakingTest = SpeakingTest::with(['interviewer'])
            ->where('assessment_session_id', $session->id)
            ->first();
        
        $currentInterviewerId = $speakingTest->interviewer_id ?? $class->local_teacher_id;

        // 5. MAPPING KE STRUKTUR VIEW
        // Karena result procedure flat (1 baris per siswa), kita perlu format jadi nested array
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
        
        return view('admin.assessment.assessment-detail', compact('class', 'session', 'type', 'speakingTest', 'studentData', 'allTeachers', 'currentInterviewerId'));
    }

    /**
     * Menyimpan/Update Nilai
     */
    public function storeOrUpdateGrades(Request $request, $classId, $type)
    {
        $session = AssessmentSession::where('class_id', $classId)
            ->where('type', $type)
            ->firstOrFail();

        $action = $request->input('action_type', 'save');

        // LOGIKA QUICK STATUS CHANGE
        if ($action === 'finalize_quick' || $action === 'draft_quick') {
            $newStatus = ($action === 'finalize_quick') ? 'final' : 'draft';
            
            if ($session->status !== $newStatus) {
                $session->update(['status' => $newStatus]);
                $msg = ($newStatus === 'final') 
                    ? 'Assessment has been APPROVED and FINALISED. All grades are now locked.' 
                    : 'Assessment status reverted to DRAFT. Teachers can now edit grades again.';
            } else {
                $msg = 'Assessment status is already ' . ucfirst($newStatus) . '. No changes made.';
            }

            return redirect()->route('admin.classes.assessment.detail', [
                'classId' => $classId,
                'type' => $type
            ])->with('success', $msg);
        }
        
        // LOGIKA FULL GRADE UPDATE
        if ($session->status === 'draft') {
            return back()->with('error', 'Action denied. You cannot edit grades while the assessment is still in DRAFT mode (Teacher is working on it).');
        }

        if ($session->status === 'final') {
            return back()->with('error', 'Action denied. Assessment is already FINALISED.');
        }

        $rules = [
            'written_date' => 'required|date',
            'speaking_date' => 'required|date',
            'interviewer_id' => 'required|exists:users,id',
            'topic' => 'nullable|string|max:255',
            'grades' => 'required|array',
            'grades.*.vocabulary' => 'required|integer|between:0,100',
            'grades.*.grammar'    => 'required|integer|between:0,100',
            'grades.*.listening'  => 'required|integer|between:0,100',
            'grades.*.reading'    => 'required|integer|between:0,100',
            'grades.*.spelling'   => 'nullable|integer|between:0,100',
            'grades.*.speaking_content'      => 'required|integer|between:0,50',
            'grades.*.speaking_participation'=> 'required|integer|between:0,50',
            'grades.*.student_id' => 'required|exists:students,id',
            'grades.*.form_id' => 'nullable', 
            'action_type' => 'nullable|string|in:save,finalize,draft', 
        ];

        $messages = [
            'grades.*.vocabulary.required' => 'Vocabulary field is required.',
            'grades.*.grammar.required' => 'Grammar field is required.',
            'grades.*.listening.required' => 'Listening field is required.',
            'grades.*.reading.required' => 'Reading field is required.',
            'grades.*.speaking_content.required' => 'Speaking Content field is required.',
            'grades.*.speaking_participation.required' => 'Speaking Participation field is required.',
            'grades.*.vocabulary.between' => 'Vocabulary must be between 0 and 100.',
            'grades.*.speaking_content.between' => 'Speaking Content must be between 0 and 50.',
        ];

        $validatedData = $request->validate($rules, $messages);
        $grades = $validatedData['grades'];

        try {
            $newStatus = $session->status;

            if ($action === 'finalize') {
                $newStatus = 'final';
            } elseif ($action === 'draft') {
                $newStatus = 'draft';
            }

            $session->update([
                'date' => $validatedData['written_date'],
                'status' => $newStatus,
            ]);

            $speakingTest = SpeakingTest::updateOrCreate(
                ['assessment_session_id' => $session->id],
                [
                    'date' => $validatedData['speaking_date'],
                    'topic' => $validatedData['topic'],
                    'interviewer_id' => $validatedData['interviewer_id'],
                ]
            );

            $speakingTestId = $speakingTest->id;
            $sessionId = $session->id;

            foreach ($grades as $grade) {
                DB::statement('CALL p_UpdateStudentGrade(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                    $sessionId,
                    $grade['student_id'],
                    $grade['form_id'] ?? null, 
                    $speakingTestId,
                    $grade['vocabulary'],
                    $grade['grammar'],
                    $grade['listening'],
                    $grade['reading'],
                    $grade['spelling'] ?? null,
                    $grade['speaking_content'],
                    $grade['speaking_participation'],
                ]);
            }
            
            $msg = 'Grades updated successfully!';
            if ($action === 'finalize') $msg = 'Assessment has been APPROVED and FINALISED. All grades are now locked.';
            if ($action === 'draft') $msg = 'Assessment status reverted to DRAFT.';

            return redirect()->route('admin.classes.assessment.detail', [
                'classId' => $classId,
                'type' => $type
            ])->with('success', $msg);

        } catch (\Exception $e) {
            Log::error("Grade update failed for class {$classId} type {$type}: " . $e->getMessage());
            return back()->with('error', 'Error Detail: ' . $e->getMessage())->withInput();
        }
    }
}