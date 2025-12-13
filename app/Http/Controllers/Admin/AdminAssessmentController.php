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
        // 1. Inisialisasi Query dengan relasi classModel
        $query = AssessmentSession::with(['classModel' => function($q) {
            // Pastikan 'name' dipilih agar bisa diakses untuk filtering
            $q->select('id', 'name', 'category', 'academic_year', 'is_active'); 
        }]);

        // 2. Logika Filter Berdasarkan Request

        // A. Filter Search (BARU)
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->whereHas('classModel', function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%");
            });
        }
        
        // B. Filter Academic Year (Existing)
        if ($request->filled('academic_year')) {
            $query->whereHas('classModel', function($q) use ($request) {
                $q->where('academic_year', $request->academic_year);
            });
        }

        // C. Filter Category (Existing)
        if ($request->filled('category')) {
            $query->whereHas('classModel', function($q) use ($request) {
                $q->where('category', $request->category);
            });
        }

        // D. Filter Exam Type (Existing)
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // E. Filter Class ID (BARU)
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        // F. Filter Class Status (BARU - Default: Active)
        $statusFilter = $request->get('class_status', 'active'); // Default ke 'active'
        if ($statusFilter != '') { // Jika bukan 'All Status'
            $isActive = $statusFilter === 'active';
            $query->whereHas('classModel', function($q) use ($isActive) {
                $q->where('is_active', $isActive);
            });
        }
        
        // 3. Eksekusi Query dengan Pagination
        $assessments = $query->orderBy('date', 'desc')->paginate(10);
        $assessments->appends($request->all());

        // 4. Data untuk Dropdown Filter
        $categories = ['pre_level', 'level', 'step', 'private'];
        $years = ClassModel::select('academic_year')->distinct()->pluck('academic_year')->sortDesc();
        $types = ['mid', 'final'];
        
        // Ambil daftar kelas untuk dropdown filter
        $classes = ClassModel::select('id', 'name')->orderBy('name', 'asc')->get();

        return view('admin.assessment.assessment', compact('assessments', 'categories', 'years', 'types', 'classes'));
    }
    
    /**
     * Menampilkan form input nilai dan Speaking Test untuk kelas tertentu.
     */
    public function detail(Request $request, $classId, $type)
    {
        if (!in_array($type, ['mid', 'final'])) {
            abort(404);
        }

        // Load data kelas beserta Form Teacher, Local Teacher, dan Siswa Aktif
        $class = ClassModel::with([
            'students' => function($query) { $query->where('is_active', true); }, 
            'localTeacher', 
            'formTeacher' 
        ])->findOrFail($classId);

        // Jika sesi sudah ada (karena fitur pre-create di AdminClassController), maka aman.
        // Jika belum ada (safety net), session akan dibuat dengan date = NULL.
        $session = AssessmentSession::firstOrCreate(
            ['class_id' => $classId, 'type' => $type],
            ['date' => null] // date diset NULL (kosong) jika sesi baru dibuat
        );

        $allTeachers = User::where('role', 'teacher')->where('is_active', true)->orderBy('name', 'asc')->get();

        $forms = AssessmentForm::where('assessment_session_id', $session->id)->get()->keyBy('student_id');

        $speakingTest = SpeakingTest::with(['results', 'interviewer'])->where('assessment_session_id', $session->id)->first();
        
        $speakingResults = $speakingTest ? $speakingTest->results->keyBy('student_id') : collect();
        $currentInterviewerId = $speakingTest->interviewer_id ?? $class->local_teacher_id;

        $studentData = $class->students->map(function ($student) use ($forms, $speakingResults) {
            $written = $forms->get($student->id);
            $speaking = $speakingResults->get($student->id);
            $totalSpeakingScore = ($speaking->content_score ?? 0) + ($speaking->participation_score ?? 0);

            // Mengambil rata-rata dari Model AssessmentForm jika data sudah ada
            $avgScore = $written ? $written->averageScore() : null;

            return [
                'id' => $student->id,
                'name' => $student->name,
                'written' => [
                    'form_id' => $written->id ?? null,
                    'vocabulary' => $written->vocabulary ?? null,
                    'grammar' => $written->grammar ?? null,
                    'listening' => $written->listening ?? null,
                    'reading' => $written->reading ?? null,
                    'spelling' => $written->spelling ?? null,
                ],
                'speaking' => [
                    'content' => $speaking->content_score ?? null,
                    'participation' => $speaking->participation_score ?? null,
                    'total' => $totalSpeakingScore,
                ],
                'avg_score' => $avgScore // Menggunakan hasil dari Model (seharusnya sudah diubah ke dynamic/fixed divisor)
            ];
        });
        
        return view('admin.assessment.assessment-detail', compact('class', 'session', 'type', 'speakingTest', 'studentData', 'allTeachers', 'currentInterviewerId'));
    }

    /**
     * Menyimpan atau memperbarui semua nilai (Tertulis dan Speaking)
     * Parameter disesuaikan dengan Route (classId, type)
     */
    public function storeOrUpdateGrades(Request $request, $classId, $type)
    {
        // 1. Cari Session berdasarkan ClassID dan Type (sesuai Route baru)
        $session = AssessmentSession::where('class_id', $classId)
            ->where('type', $type)
            ->firstOrFail();
        
        $rules = [
            'written_date' => 'required|date',
            'speaking_date' => 'required|date',
            'interviewer_id' => 'required|exists:users,id',
            'topic' => 'nullable|string|max:255',
            'grades' => 'required|array',
            
            // WAJIB ISI (Core Skills)
            'grades.*.vocabulary' => 'required|integer|between:0,100',
            'grades.*.grammar'    => 'required|integer|between:0,100',
            'grades.*.listening'  => 'required|integer|between:0,100',
            'grades.*.reading'    => 'required|integer|between:0,100',

            // OPSIONAL (Boleh Kosong)
            'grades.*.spelling'   => 'nullable|integer|between:0,100',

            // WAJIB ISI (Speaking Components)
            'grades.*.speaking_content'      => 'required|integer|between:0,50',
            'grades.*.speaking_participation'=> 'required|integer|between:0,50',
            
            'grades.*.student_id' => 'required|exists:students,id',
            'grades.*.form_id' => 'nullable', 
        ];

        // Custom Messages untuk mengatasi notasi grades.*.* yang tidak user-friendly
        $messages = [
            'grades.*.vocabulary.required' => 'Vocabulary field is required.',
            'grades.*.grammar.required' => 'Grammar field is required.',
            'grades.*.listening.required' => 'Listening field is required.',
            'grades.*.reading.required' => 'Reading field is required.',
            'grades.*.speaking_content.required' => 'Speaking Content field is required.',
            'grades.*.speaking_participation.required' => 'Speaking Participation field is required.',
        ];

        // Validasi dengan custom messages
        $validatedData = $request->validate($rules, $messages);
        $grades = $validatedData['grades'];

        DB::beginTransaction();
        try {
            // Update Info Sesi
            $session->update(['date' => $validatedData['written_date']]);

            // Update Info Speaking Test
            $speakingTest = SpeakingTest::updateOrCreate(
                ['assessment_session_id' => $session->id],
                [
                    'date' => $validatedData['speaking_date'],
                    'topic' => $validatedData['topic'],
                    'interviewer_id' => $validatedData['interviewer_id'],
                ]
            );

            // Loop Simpan Nilai
            foreach ($grades as $grade) {
                $studentId = $grade['student_id'];
                
                // 1. Simpan Nilai Speaking (Detail)
                $speakingResult = SpeakingTestResult::updateOrCreate(
                    [
                        'speaking_test_id' => $speakingTest->id,
                        'student_id' => $studentId,
                    ],
                    [
                        'content_score' => $grade['speaking_content'],
                        'participation_score' => $grade['speaking_participation'],
                    ]
                );
                
                // Hitung total speaking untuk dimasukkan ke AssessmentForm
                $totalSpeaking = $speakingResult->totalScore();

                // 2. Simpan Nilai Tertulis & Total Speaking
                $assessmentForm = AssessmentForm::updateOrCreate(
                    [
                        // Jika form_id ada, gunakan itu. Jika tidak, akan dibuat baru.
                        'id' => $grade['form_id'] ?? null, 
                    ], 
                    [
                        'student_id' => $studentId,
                        'assessment_session_id' => $session->id,
                        'vocabulary' => $grade['vocabulary'],
                        'grammar' => $grade['grammar'],
                        'listening' => $grade['listening'],
                        'reading' => $grade['reading'],
                        'spelling' => $grade['spelling'] ?? null, // Spelling nullable
                        'speaking' => $totalSpeaking, 
                        'is_submitted' => true,
                    ]
                );
            }
            
            DB::commit();

            // Redirect spesifik ke route detail (menghapus parameter '?mode=edit')
            return redirect()->route('admin.classes.assessment.detail', [
                'classId' => $classId,
                'type' => $type
            ])->with('success', 'Grades updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Grade update failed for class {$classId} type {$type}: " . $e->getMessage());
            // Jika error, gunakan back() agar inputan tidak hilang
            return back()->with('error', 'Failed to save grades: System Error.')->withInput();
        }
    }
}