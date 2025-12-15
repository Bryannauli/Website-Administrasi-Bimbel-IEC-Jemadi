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

        // --- [BARU] Filter by Assessment Status (Draft/Submitted/Final) ---
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
        
        // [BARU] Opsi Status untuk Dropdown
        $statuses = ['draft', 'submitted', 'final'];

        return view('admin.assessment.assessment', compact('assessments', 'categories', 'years', 'types', 'classes', 'statuses'));
    }
    
    /**
     * Menampilkan form input nilai (Detail View)
     */
    public function detail(Request $request, $classId, $type)
    {
        if (!in_array($type, ['mid', 'final'])) {
            abort(404);
        }

        // 1. Load data Kelas & Siswa Aktif
        $class = ClassModel::with([
            'students' => function($query) { $query->where('is_active', true); }, 
            'localTeacher', 
            'formTeacher' 
        ])->findOrFail($classId);

        // 2. First or Create Session
        $session = AssessmentSession::firstOrCreate(
            ['class_id' => $classId, 'type' => $type],
            ['date' => null]
        );

        $allTeachers = User::where('role', 'teacher')->where('is_active', true)->orderBy('name', 'asc')->get();

        // 3. AMBIL SEMUA GRADES DARI DATABASE VIEW (v_student_grades)
        // Ini menggantikan AssessmentForm::where(...) dan join manual
        $gradesFromView = DB::table('v_student_grades')
            ->where('class_id', $classId)
            ->where('assessment_type', $type)
            ->get()
            ->keyBy('student_id');

        // 4. AMBIL INFO SPEAKING TEST UTAMA (Interviewer, Tanggal, Topik)
        // Walaupun detail speaking ada di view, kita tetap perlu data ini untuk form config
        $speakingTest = SpeakingTest::with(['interviewer'])
            ->where('assessment_session_id', $session->id)
            ->first();
        
        $currentInterviewerId = $speakingTest->interviewer_id ?? $class->local_teacher_id;

        // 5. MAPPING DATA SISWA
        $studentData = $class->students->map(function ($student) use ($gradesFromView, $speakingTest) {
            
            // Ambil data nilai dari View (jika ada)
            $gradeRecord = $gradesFromView->get($student->id);

            // Jika ada nilai, gunakan data dari View. Jika tidak, semua null/default.
            $written = $gradeRecord;
            $speaking = $gradeRecord;

            // Hitung total speaking untuk keperluan Alpine (jika record ada)
            $totalSpeakingScore = ($speaking->speaking_content ?? 0) + ($speaking->speaking_participation ?? 0);

            return [
                'id' => $student->id,
                'name' => $student->name,
                'student_number' => $student->student_number,
                'written' => [
                    'form_id' => $written->form_id ?? null,
                    'vocabulary' => $written->vocabulary ?? null,
                    'grammar' => $written->grammar ?? null,
                    'listening' => $written->listening ?? null,
                    'reading' => $written->reading ?? null,
                    'spelling' => $written->spelling ?? null,
                ],
                'speaking' => [
                    // Ambil detail speaking dari view (speaking_content, speaking_participation)
                    'content' => $speaking->speaking_content ?? null,
                    'participation' => $speaking->speaking_participation ?? null,
                    'total' => $totalSpeakingScore, // Diisi untuk kompatibilitas Alpine
                ],
                // Nilai Rata-rata & Predikat dari View (sudah dihitung oleh Stored Function)
                'avg_score' => $written->final_score ?? null,
                'grade_text' => $written->grade_text ?? '-',
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

        // 1. Tentukan Aksi Utama
        $action = $request->input('action_type', 'save');

        // ==========================================================
        // LOGIKA QUICK STATUS CHANGE (Dari tombol mode baca: finalize_quick atau draft_quick)
        // Jika ini Quick Action, kita tidak perlu memproses data grades
        // ==========================================================
        if ($action === 'finalize_quick' || $action === 'draft_quick') {
            
            $newStatus = ($action === 'finalize_quick') ? 'final' : 'draft';
            
            // Cek apakah ada perubahan status
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
        
        // ==========================================================
        // LOGIKA FULL GRADE UPDATE (Jika bukan Quick Action, lanjutkan proses nilai)
        // ==========================================================

        // [BARU] SECURITY CHECK: Admin tidak boleh edit nilai jika masih DRAFT
        if ($session->status === 'draft') {
            return back()->with('error', 'Action denied. You cannot edit grades while the assessment is still in DRAFT mode (Teacher is working on it).');
        }

        // [BARU] SECURITY CHECK: Admin tidak boleh edit nilai jika sudah FINAL (Kecuali revert)
        // (Opsional, karena UI sudah menghilangkannya, tapi bagus untuk keamanan)
        if ($session->status === 'final') {
            return back()->with('error', 'Action denied. Assessment is already FINALISED.');
        }

        $rules = [
            'written_date' => 'required|date',
            'speaking_date' => 'required|date',
            'interviewer_id' => 'required|exists:users,id',
            'topic' => 'nullable|string|max:255',
            'grades' => 'required|array',
            
            // WAJIB (Core Skills)
            'grades.*.vocabulary' => 'required|integer|between:0,100',
            'grades.*.grammar'    => 'required|integer|between:0,100',
            'grades.*.listening'  => 'required|integer|between:0,100',
            'grades.*.reading'    => 'required|integer|between:0,100',

            // OPSIONAL (Spelling Boleh Null)
            'grades.*.spelling'   => 'nullable|integer|between:0,100',

            // WAJIB (Speaking Components)
            'grades.*.speaking_content'      => 'required|integer|between:0,50',
            'grades.*.speaking_participation'=> 'required|integer|between:0,50',
            
            'grades.*.student_id' => 'required|exists:students,id',
            'grades.*.form_id' => 'nullable', 
            
            // TANGKAP ACTION TYPE DARI TOMBOL MODE EDIT
            'action_type' => 'nullable|string|in:save,finalize,draft', 
        ];

        // Custom Messages agar error "grades.1.listening" jadi lebih enak dibaca
        $messages = [
            'grades.*.vocabulary.required' => 'Vocabulary field is required.',
            'grades.*.grammar.required' => 'Grammar field is required.',
            'grades.*.listening.required' => 'Listening field is required.',
            'grades.*.reading.required' => 'Reading field is required.',
            'grades.*.speaking_content.required' => 'Speaking Content field is required.',
            'grades.*.speaking_participation.required' => 'Speaking Participation field is required.',
            
            // Tambahan untuk range angka (optional, untuk kerapian)
            'grades.*.vocabulary.between' => 'Vocabulary must be between 0 and 100.',
            'grades.*.speaking_content.between' => 'Speaking Content must be between 0 and 50.',
        ];

        // Jalankan Validasi
        $validatedData = $request->validate($rules, $messages);
        $grades = $validatedData['grades'];

        try {
            // A. Tentukan Status Baru Berdasarkan Tombol yang Diklik (Mode Edit)
            $newStatus = $session->status; // Default status tidak berubah

            if ($action === 'finalize') {
                $newStatus = 'final';
            } elseif ($action === 'draft') {
                $newStatus = 'draft';
            }
            // Jika action 'save', status tetap sama (hanya nilai yang terupdate)

            // B. Update Info Sesi (Termasuk Status Baru)
            $session->update([
                'date' => $validatedData['written_date'],
                'status' => $newStatus, // <--- UPDATE STATUS SESI DI SINI
            ]);

            // C. Update Info Speaking Test
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

            // D. LOOP DAN PANGGIL STORED PROCEDURE
            foreach ($grades as $grade) {
                
                // Panggil Stored Procedure untuk mengurus INSERT/UPDATE/TRANSACTION SATU SISWA
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
            
            // Pesan Sukses yang Dinamis
            $msg = 'Grades updated successfully!';
            if ($action === 'finalize') $msg = 'Assessment has been APPROVED and FINALISED. All grades are now locked.';
            if ($action === 'draft') $msg = 'Assessment status reverted to DRAFT.';

            // Redirect ke mode baca (tanpa parameter 'mode=edit')
            return redirect()->route('admin.classes.assessment.detail', [
                'classId' => $classId,
                'type' => $type
            ])->with('success', $msg);

        } catch (\Exception $e) {
            // Jika ada error (termasuk trigger database), PHP akan menangkapnya di sini.
            Log::error("Grade update failed for class {$classId} type {$type}: " . $e->getMessage());
            
            return back()
                ->with('error', 'Error Detail: ' . $e->getMessage())
                ->withInput();
        }
    }
}