<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\AssessmentSession;
use App\Models\AssessmentForm;
use App\Models\SpeakingTest;
use App\Models\SpeakingTestResult; // Import model baru
use App\Models\User; // Import model User untuk mengambil daftar guru
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminAssessmentController extends Controller
{
    /**
     * Menampilkan form input nilai dan Speaking Test untuk kelas tertentu.
     * Route: /admin/classes/{classId}/assessment/{type}
     */
    public function manageGrades(Request $request, $classId, $type)
{
    if (!in_array($type, ['mid', 'final'])) {
        abort(404);
    }

    // Load data kelas beserta Form Teacher, Local Teacher, dan Siswa Aktif
    $class = ClassModel::with([
        'students' => function($query) { $query->where('is_active', true); }, 
        'localTeacher', 
        'formTeacher' // <<< Pastikan relasi ini di-load
    ])->findOrFail($classId);

    $session = AssessmentSession::firstOrCreate(
        ['class_id' => $classId, 'type' => $type],
        ['date' => now()->toDateString()]
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
            'avg_score' => $written 
                ? round(($written->vocabulary + $written->grammar + $written->listening + $written->reading + $written->spelling + $totalSpeakingScore) / 6)
                : null
        ];
    });
    
    return view('admin.assessment.manage-grades', compact('class', 'session', 'type', 'speakingTest', 'studentData', 'allTeachers', 'currentInterviewerId'));
}

    /**
     * Menyimpan atau memperbarui semua nilai (Tertulis dan Speaking)
     */
    public function storeOrUpdateGrades(Request $request, $sessionId)
    {
        $session = AssessmentSession::findOrFail($sessionId);
        
        // 1. Validasi Massal
        $rules = [
            'written_date' => 'required|date', // Tanggal Ujian Tertulis
            'speaking_date' => 'required|date', // Tanggal Speaking Test
            'interviewer_id' => 'required|exists:users,id',
            'topic' => 'nullable|string|max:255',
            'grades' => 'required|array',
            
            // Validasi nilai tertulis (0-100)
            'grades.*.vocabulary' => 'nullable|integer|between:0,100',
            'grades.*.grammar' => 'nullable|integer|between:0,100',
            'grades.*.listening' => 'nullable|integer|between:0,100',
            'grades.*.reading' => 'nullable|integer|between:0,100',
            'grades.*.spelling' => 'nullable|integer|between:0,100',

            // Validasi nilai Speaking Breakdown (0-50)
            'grades.*.speaking_content' => 'nullable|integer|between:0,50',
            'grades.*.speaking_participation' => 'nullable|integer|between:0,50',
            
            // Hidden fields
            'grades.*.student_id' => 'required|exists:students,id',
            'grades.*.form_id' => 'nullable|exists:assessment_forms,id',
        ];

        $validatedData = $request->validate($rules);
        $grades = $validatedData['grades'];

        DB::beginTransaction();
        try {
            // 1. Update Tanggal Sesi (Written Date)
            $session->update(['date' => $validatedData['written_date']]);

            // 2. Simpan/Update Speaking Test Configuration
            $speakingTest = SpeakingTest::updateOrCreate(
                ['assessment_session_id' => $session->id],
                [
                    'date' => $validatedData['speaking_date'],
                    'topic' => $validatedData['topic'],
                    'interviewer_id' => $validatedData['interviewer_id'],
                ]
            );

            // 3. Loop dan Simpan Nilai per Siswa
            foreach ($grades as $grade) {
                $studentId = $grade['student_id'];
                
                // --- 3a. Proses Nilai Speaking (SpeakingTestResults) ---
                $speakingResult = SpeakingTestResult::updateOrCreate(
                    [
                        'speaking_test_id' => $speakingTest->id,
                        'student_id' => $studentId,
                    ],
                    [
                        'content_score' => $grade['speaking_content'] ?? null,
                        'participation_score' => $grade['speaking_participation'] ?? null,
                    ]
                );
                
                $totalSpeaking = ($speakingResult->content_score ?? 0) + ($speakingResult->participation_score ?? 0);

                // --- 3b. Proses Nilai Tertulis (AssessmentForm) ---
                AssessmentForm::updateOrCreate(
                    [
                        'id' => $grade['form_id'], 
                        'student_id' => $studentId,
                        'assessment_session_id' => $session->id,
                    ],
                    [
                        'vocabulary' => $grade['vocabulary'] ?? null,
                        'grammar' => $grade['grammar'] ?? null,
                        'listening' => $grade['listening'] ?? null,
                        'reading' => $grade['reading'] ?? null,
                        'spelling' => $grade['spelling'] ?? null,
                        'speaking' => $totalSpeaking, 
                        'is_submitted' => true,
                    ]
                );
            }
            
            DB::commit();
            return back()->with('success', 'Grades updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to save grades: ' . $e->getMessage())->withInput();
        }
    }
}