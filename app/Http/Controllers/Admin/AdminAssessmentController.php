<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\AssessmentSession;
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
        $assessments = $query->orderBy('written_date', 'desc')->paginate(10);
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
        if (!in_array($type, ['mid', 'final'])) { abort(404); }

        // [UPDATED] Load Session dengan interviewer
        $session = AssessmentSession::with('interviewer')->firstOrCreate(
            ['class_id' => $classId, 'type' => $type],
            ['written_date' => null, 'status' => 'draft']
        );

        $class = ClassModel::with(['localTeacher', 'formTeacher'])->findOrFail($classId);

        // Call Procedure
        $rawStudentData = DB::select('CALL p_GetAssessmentSheet(?, ?)', [$classId, $session->id]);

        $allTeachers = User::where('role', 'teacher')->where('is_active', true)->orderBy('name', 'asc')->get();

        $currentInterviewerId = $session->interviewer_id ?? $class->local_teacher_id;

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
        
        return view('admin.assessment.assessment-detail', compact(
            'class', 'session', 'type', 'studentData', 'allTeachers', 'currentInterviewerId'
        ));
    }

    /**
     * Store/Update Grades
     */
    public function storeOrUpdateGrades(Request $request, $classId, $type)
    {
        $session = AssessmentSession::where('class_id', $classId)
            ->where('type', $type)
            ->firstOrFail();

        $action = $request->input('action_type', 'save');

        // QUICK STATUS CHANGE
        if ($action === 'finalize_quick' || $action === 'draft_quick') {
            $newStatus = ($action === 'finalize_quick') ? 'final' : 'draft';
            if ($session->status !== $newStatus) {
                $session->update(['status' => $newStatus]);
                $msg = ($newStatus === 'final') ? 'Assessment has been FINALISED.' : 'Assessment status reverted to DRAFT.';
            } else {
                $msg = 'Status already updated.';
            }
            return redirect()->route('admin.classes.assessment.detail', ['classId' => $classId, 'type' => $type])->with('success', $msg);
        }
        
        // CHECK LOCK
        if ($session->status === 'draft') return back()->with('error', 'Cannot edit grades in DRAFT mode.');
        if ($session->status === 'final') return back()->with('error', 'Assessment is FINALISED.');

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
        ];

        $validatedData = $request->validate($rules);
        $grades = $validatedData['grades'];

        try {
            $newStatus = $session->status;
            if ($action === 'finalize') $newStatus = 'final';
            if ($action === 'draft') $newStatus = 'draft';

            // [UPDATED] Update Session Headers
            $session->update([
                'written_date' => $validatedData['written_date'],
                'speaking_date' => $validatedData['speaking_date'],
                'speaking_topic' => $validatedData['topic'],
                'interviewer_id' => $validatedData['interviewer_id'],
                'status' => $newStatus,
            ]);

            foreach ($grades as $grade) {
                // [UPDATED] Call Procedure (10 Params)
                DB::statement('CALL p_UpdateStudentGrade(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                    $session->id,
                    $grade['student_id'],
                    $grade['form_id'] ?? null, 
                    // Speaking test id REMOVED
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
            if ($action === 'finalize') $msg = 'Assessment FINALISED.';
            if ($action === 'draft') $msg = 'Reverted to DRAFT.';

            return redirect()->route('admin.classes.assessment.detail', ['classId' => $classId, 'type' => $type])->with('success', $msg);

        } catch (\Exception $e) {
            Log::error("Update failed: " . $e->getMessage());
            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function printAssessmentForm($classId, $sessionId)
    {
        // 1. Ambil Header Informasi Kelas
        // Kita perlu data kelas dan detail sesinya (term, guru, dll)
        $headerData = DB::table('assessment_sessions as asess')
            ->join('classes as c', 'asess.class_id', '=', 'c.id')
            ->leftJoin('users as ft', 'c.form_teacher_id', '=', 'ft.id') // Form Teacher
            ->leftJoin('users as lt', 'c.local_teacher_id', '=', 'lt.id') // Other/Local Teacher
            ->where('asess.id', $sessionId)
            ->select(
                'c.name as class_name',
                'c.start_time', 'c.end_time',
                'c.start_month', 'c.end_month', 'c.academic_year', // Untuk Month/Term
                'ft.name as form_teacher',
                'lt.name as other_teacher',
                'asess.type as assessment_type'
            )
            ->first();

        // --- TAMBAHAN BARU: AMBIL HARI ---
        // Mengambil daftar hari dari tabel schedules dan digabung dengan simbol "&"
        $days = DB::table('schedules')
            ->where('class_id', $classId)
            ->pluck('day_of_week') // Pastikan nama kolom di DB Anda 'day_of_week' atau 'day'
            ->toArray();
        
        // Hasil: "Monday & Thursday"
        $headerData->class_days = !empty($days) ? implode(' & ', $days) : '-';

        // 2. Ambil Data Nilai Siswa dari VIEW v_student_grades
        // Ini yang akan mengisi tabel utama
        $students = DB::table('v_student_grades')
            ->where('assessment_session_id', $sessionId) // Filter berdasarkan sesi ujian
            ->where('class_id', $classId)
            ->orderBy('student_number', 'ASC') // Urutkan sesuai nomor induk
            ->get();

        // 3. Kirim ke View Blade
        return view('admin.assessment.assessment-report', compact('headerData', 'students'));
    }
}