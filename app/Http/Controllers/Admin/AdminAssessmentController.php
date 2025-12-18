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
        $query = AssessmentSession::with(['classModel' => function($q) {
            $q->select('id', 'name', 'category', 'academic_year', 'is_active'); 
        }]);

        // --- FILTERING ---
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->whereHas('classModel', function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%");
            });
        }
        
        if ($request->filled('academic_year')) {
            $query->whereHas('classModel', function($q) use ($request) {
                $q->where('academic_year', $request->academic_year);
            });
        }

        if ($request->filled('category')) {
            $query->whereHas('classModel', function($q) use ($request) {
                $q->where('category', $request->category);
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('assessment_status')) {
            $query->where('status', $request->assessment_status);
        }

        $assessments = $query->orderBy('written_date', 'desc')->paginate(10)->withQueryString();

        $categories = ['pre_level', 'level', 'step', 'private'];
        $years = ClassModel::select('academic_year')->distinct()->pluck('academic_year')->sortDesc();
        $types = ['mid', 'final'];
        $classes = ClassModel::select('id', 'name')->orderBy('name', 'asc')->get();
        $statuses = ['draft', 'submitted', 'final'];

        return view('admin.assessment.assessment', compact('assessments', 'categories', 'years', 'types', 'classes', 'statuses'));
    }
    
    /**
     * Menampilkan form input nilai (Detail View)
     */
    public function detail(Request $request, $classId, $type)
    {
        if (!in_array($type, ['mid', 'final'])) { abort(404); }

        // 1. Ambil atau Buat Sesi Assessment
        $session = AssessmentSession::with('interviewer')->firstOrCreate(
            ['class_id' => $classId, 'type' => $type],
            ['written_date' => null, 'status' => 'draft']
        );

        // 2. CEK STATUS SILANG (UNTUK MIX REPORT)
        // Jika buka Mid, cek Final. Jika buka Final, cek Mid.
        $otherType = ($type === 'mid') ? 'final' : 'mid';
        $otherSession = AssessmentSession::where('class_id', $classId)->where('type', $otherType)->first();
        
        // Syarat: Sesi INI harus Final DAN Sesi SEBELAH juga harus Final
        $isBothFinal = ($session->status === 'final' && ($otherSession && $otherSession->status === 'final'));

        $class = ClassModel::with(['localTeacher', 'formTeacher'])->findOrFail($classId);
        
        // 3. Ambil Data Siswa & Nilai via Stored Procedure
        $rawStudentData = DB::select('CALL p_GetAssessmentSheet(?, ?)', [$classId, $session->id]);

        $allTeachers = User::where('role', 'teacher')->where('is_active', true)->orderBy('name', 'asc')->get();
        $currentInterviewerId = $session->interviewer_id ?? $class->local_teacher_id;

        // 4. Mapping Data agar mudah dipakai di Blade
        $studentData = collect($rawStudentData)->map(function ($row) {
            return [
                'id' => $row->student_id,
                'name' => $row->name,
                'student_number' => $row->student_number,
                'is_active' => $row->is_active,
                'deleted_at' => $row->deleted_at,
                'current_class_id' => $row->current_class_id, // Untuk membedakan siswa aktif vs alumni
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
            'class', 'session', 'type', 'studentData', 'allTeachers', 'currentInterviewerId', 'isBothFinal'
        ));
    }

    /**
     * Store/Update Grades (OPTIMIZED WITH STORED PROCEDURE)
     */
    public function storeOrUpdateGrades(Request $request, $classId, $type)
    {
        $session = AssessmentSession::where('class_id', $classId)->where('type', $type)->firstOrFail();
        $action = $request->input('action_type', 'save');

        // A. QUICK STATUS CHANGE (Tanpa Save Data Nilai)
        // Bagian ini tetap menggunakan Eloquent karena tidak melibatkan update nilai yang kompleks
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
        
        // B. SAVE DATA (Normal Save / Save & Finalize)
        if ($session->status === 'draft') return back()->with('error', 'Cannot edit grades in DRAFT mode. Wait for submission.');
        // Jika status final, admin TETAP BISA edit (untuk koreksi), asalkan tidak sedang dikunci sistem lain. 
        // Logic Anda sebelumnya memblokir jika final, tapi biasanya Admin butuh akses "Edit anyway". 
        // Namun saya ikuti logic Anda:
        if ($session->status === 'final') return back()->with('error', 'Assessment is FINALISED. Cannot edit.');

        $validatedData = $request->validate([
            'written_date' => 'required|date',
            'speaking_date' => 'required|date',
            'interviewer_id' => 'required|exists:users,id',
            'topic' => 'nullable|string|max:255',
            'grades' => 'required|array',
            'grades.*.vocabulary' => 'nullable|integer|between:0,100', // Ubah ke nullable agar fleksibel
            'grades.*.grammar'    => 'nullable|integer|between:0,100',
            'grades.*.listening'  => 'nullable|integer|between:0,100',
            'grades.*.reading'    => 'nullable|integer|between:0,100',
            'grades.*.spelling'   => 'nullable|integer|between:0,100',
            'grades.*.speaking_content'      => 'nullable|integer|between:0,50',
            'grades.*.speaking_participation'=> 'nullable|integer|between:0,50',
            'grades.*.student_id' => 'required|exists:students,id',
        ]);

        try {
            // 1. Tentukan Status Baru
            // Jika action 'finalize', status jadi 'final'. 
            // Jika action 'save', status TETAP (kirim null ke SP agar tidak berubah, atau kirim status saat ini).
            $newStatus = ($action === 'finalize') ? 'final' : null;

            // 2. Persiapkan Data JSON untuk Procedure
            $marksData = [];
            foreach ($validatedData['grades'] as $grade) {
                $marksData[] = [
                    'student_id'             => (int) $grade['student_id'],
                    'vocabulary'             => isset($grade['vocabulary']) && $grade['vocabulary'] !== '' ? (int) $grade['vocabulary'] : null,
                    'grammar'                => isset($grade['grammar']) && $grade['grammar'] !== '' ? (int) $grade['grammar'] : null,
                    'listening'              => isset($grade['listening']) && $grade['listening'] !== '' ? (int) $grade['listening'] : null,
                    'reading'                => isset($grade['reading']) && $grade['reading'] !== '' ? (int) $grade['reading'] : null,
                    'spelling'               => isset($grade['spelling']) && $grade['spelling'] !== '' ? (int) $grade['spelling'] : null,
                    'speaking_content'       => isset($grade['speaking_content']) && $grade['speaking_content'] !== '' ? (int) $grade['speaking_content'] : null,
                    'speaking_participation' => isset($grade['speaking_participation']) && $grade['speaking_participation'] !== '' ? (int) $grade['speaking_participation'] : null,
                ];
            }
            $jsonMarks = json_encode($marksData);

            // 3. Panggil Stored Procedure Batch
            // Parameter: session_id, written_date, speaking_date, interviewer_id, topic, status, json_marks
            DB::statement('CALL p_SaveAssessmentBatch(?, ?, ?, ?, ?, ?, ?)', [
                $session->id,
                $validatedData['written_date'],
                $validatedData['speaking_date'],
                $validatedData['interviewer_id'],
                $validatedData['topic'],
                $newStatus,
                $jsonMarks
            ]);
            
            $msg = ($action === 'finalize') ? 'Assessment FINALISED.' : 'Grades updated successfully!';
            return redirect()->route('admin.classes.assessment.detail', ['classId' => $classId, 'type' => $type])->with('success', $msg);

        } catch (\Exception $e) {
            Log::error("Update failed: " . $e->getMessage());
            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Print Single Assessment (Mid Only OR Final Only)
     */
    public function printAssessmentForm($classId, $sessionId)
    {
        $headerData = DB::table('assessment_sessions as asess')
            ->join('classes as c', 'asess.class_id', '=', 'c.id')
            ->leftJoin('users as ft', 'c.form_teacher_id', '=', 'ft.id')
            ->leftJoin('users as lt', 'c.local_teacher_id', '=', 'lt.id')
            ->where('asess.id', $sessionId)
            ->select(
                'c.name as class_name', 'c.start_time', 'c.end_time', 
                'c.start_month', 'c.end_month', 'c.academic_year', 
                'ft.name as form_teacher', 'lt.name as other_teacher', 
                'asess.type as assessment_type'
            )
            ->first();

        $days = DB::table('schedules')->where('class_id', $classId)->pluck('day_of_week')->toArray();
        $headerData->class_days = !empty($days) ? implode(' & ', $days) : '-';

        $students = DB::table('v_student_grades')
            ->where('assessment_session_id', $sessionId)
            ->where('class_id', $classId)
            ->orderBy('student_number', 'ASC')
            ->get();

        return view('admin.assessment.assessment-report', compact('headerData', 'students'));
    }

    /**
     * Print Mix Report (Mid + Final Gabungan)
     */
    public function printMixReport($classId)
    {
        // 1. Ambil Header Informasi Kelas
        $class = ClassModel::with(['formTeacher', 'localTeacher'])->findOrFail($classId);
        $days = DB::table('schedules')->where('class_id', $classId)->pluck('day_of_week')->toArray();
        
        $header = (object) [
            'month'         => strtoupper($class->start_month ?? '') . ' - ' . strtoupper($class->end_month ?? '') . ' ' . ($class->academic_year ?? ''),
            'form_teacher'  => $class->formTeacher->name ?? '-',
            'other_teacher' => $class->localTeacher->name ?? '-',
            'class_name'    => $class->name,
            'class_time'    => \Carbon\Carbon::parse($class->start_time)->format('g:i A') . ' - ' . \Carbon\Carbon::parse($class->end_time)->format('g:i A'),
            'class_days'    => !empty($days) ? implode(' & ', $days) : '-'
        ];

        $subjects = ['Vocabulary', 'Grammar', 'Listening', 'Speaking', 'Reading', 'Spelling'];
        
        // 2. Ambil semua nilai dari view (mencakup data historis di kelas ini)
        $allGrades = DB::table('v_student_grades')->where('class_id', $classId)->get();

        // 3. Ambil Daftar Siswa (Logika: Siswa di kelas ini SEKARANG atau yang PERNAH ada nilai di sini)
        $studentList = DB::table('students')
            ->where('class_id', $classId)
            ->orWhereIn('id', $allGrades->pluck('student_id'))
            ->select('id', 'name', 'student_number', 'is_active', 'deleted_at')
            ->orderBy('student_number', 'asc')
            ->get();

        // 4. Mapping Data
        $students = $studentList->map(function($student, $index) use ($allGrades, $subjects) {
            $marks = [];
            $totalAve = 0; $countSubj = 0;

            foreach ($subjects as $subj) {
                $field = strtolower($subj);
                $mid = $allGrades->where('student_id', $student->id)->where('assessment_type', 'mid')->first();
                $fin = $allGrades->where('student_id', $student->id)->where('assessment_type', 'final')->first();

                $mVal = $mid ? $mid->$field : 0;
                $fVal = $fin ? $fin->$field : 0;
                $ave  = ($mVal + $fVal) > 0 ? round(($mVal + $fVal) / 2) : 0;

                $marks[$subj] = (object) ['mid' => $mVal ?: '-', 'final' => $fVal ?: '-', 'ave' => $ave ?: '-'];
                if ($ave > 0) { $totalAve += $ave; $countSubj++; }
            }

            return (object) [
                'no'             => $index + 1,
                'student_number' => $student->student_number,
                'name'           => $student->name,
                'is_active'      => $student->is_active,
                'deleted_at'     => $student->deleted_at,
                'marks'          => $marks,
                'total_ave'      => $countSubj > 0 ? round($totalAve / $countSubj) : 0,
                'rank'           => '-',
                'at'             => ''
            ];
        });

        // 5. Ranking
        $sorted = $students->sortByDesc('total_ave')->values();
        foreach ($students as $s) {
            $s->rank = $sorted->search(fn($item) => $item->total_ave === $s->total_ave) + 1;
        }

        return view('admin.assessment.assessment-mix-report', compact('header', 'subjects', 'students'));
    }
}