<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\AssessmentSession;
use App\Models\AssessmentForm;
use App\Models\SpeakingTest; // Tambahkan Model
use App\Models\User; // Tambahkan Model
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TeacherAssessmentController extends Controller
{
    // Menampilkan Halaman Input Nilai
    public function assessmentDetail($classId, $assessmentId)
    {
        $class = ClassModel::findOrFail($classId);
        $assessment = AssessmentSession::with('forms')->findOrFail($assessmentId);

        // 1. Ambil data Speaking Test (Create if not exists agar tidak error di view)
        $speakingTest = SpeakingTest::firstOrCreate(
            ['assessment_session_id' => $assessment->id],
            ['date' => null, 'interviewer_id' => $class->local_teacher_id] // Default ke teacher kelas
        );

        // 2. Ambil daftar Guru untuk Dropdown Interviewer
        $teachers = User::where('is_teacher', true)
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();

        // 3. Ambil Siswa
        $students = Student::where('class_id', $classId)
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();

        foreach ($students as $student) {
            $form = $assessment->forms->where('student_id', $student->id)->first();
            $student->form = $form; 
        }

        // Kirim $speakingTest dan $teachers ke View
        return view('teacher.classes.assessment-marks', compact('class', 'assessment', 'students', 'speakingTest', 'teachers'));
    }

    // Menyimpan Nilai & Info Assessment
    public function updateAssessmentMarks(Request $request, $classId, $assessmentId)
    {
        // 1. Validasi Input (Termasuk Header Info)
        $request->validate([
            'written_date' => 'required|date',
            'speaking_date' => 'nullable|date',
            'interviewer_id' => 'nullable|exists:users,id',
            'topic' => 'nullable|string|max:200',

            'marks' => 'array',
            'marks.*.vocabulary' => 'nullable|numeric|min:0|max:100',
            'marks.*.grammar'    => 'nullable|numeric|min:0|max:100',
            'marks.*.listening'  => 'nullable|numeric|min:0|max:100',
            'marks.*.speaking_content' => 'nullable|numeric|min:0|max:50',
            'marks.*.speaking_participation' => 'nullable|numeric|min:0|max:50',
            'marks.*.reading'    => 'nullable|numeric|min:0|max:100',
            'marks.*.spelling'   => 'nullable|numeric|min:0|max:100',
        ]);

        DB::transaction(function () use ($request, $assessmentId) {
            // 2. Update Header Info (Assessment Session - Written Date)
            $session = AssessmentSession::findOrFail($assessmentId);
            $session->update([
                'date' => $request->written_date
            ]);

            // 3. Update Header Info (Speaking Test)
            SpeakingTest::updateOrCreate(
                ['assessment_session_id' => $assessmentId],
                [
                    'date' => $request->speaking_date,
                    'interviewer_id' => $request->interviewer_id,
                    'topic' => $request->topic
                ]
            );
            
            // Ambil ID speaking test yang baru saja diupdate/create
            $speakingTest = SpeakingTest::where('assessment_session_id', $assessmentId)->first();

            // 4. Loop Simpan Nilai Siswa
            if ($request->has('marks')) {
                foreach ($request->marks as $studentId => $scores) {
                    
                    // Hitung Total Speaking
                    $sContent = $scores['speaking_content'] ?? 0;
                    $sPartic = $scores['speaking_participation'] ?? 0;
                    $totalSpeaking = ($scores['speaking_content'] !== null || $scores['speaking_participation'] !== null) 
                        ? $sContent + $sPartic 
                        : null;

                    // Panggil Procedure (Atau pakai logic Eloquent seperti sebelumnya, disini saya pakai Eloquent agar aman)
                    
                    // A. Simpan Speaking Detail
                    DB::table('speaking_test_results')->updateOrInsert(
                        ['speaking_test_id' => $speakingTest->id, 'student_id' => $studentId],
                        [
                            'content_score' => $scores['speaking_content'] ?? null,
                            'participation_score' => $scores['speaking_participation'] ?? null,
                            'updated_at' => now()
                        ]
                    );

                    // B. Simpan Form Utama
                    AssessmentForm::updateOrCreate(
                        [
                            'assessment_session_id' => $assessmentId,
                            'student_id' => $studentId,
                        ],
                        [
                            'vocabulary' => $scores['vocabulary'] ?? null,
                            'grammar'    => $scores['grammar'] ?? null,
                            'listening'  => $scores['listening'] ?? null,
                            'speaking'   => $totalSpeaking, // Total Score
                            'reading'    => $scores['reading'] ?? null,
                            'spelling'   => $scores['spelling'] ?? null,
                        ]
                    );
                }
            }
        });

        return redirect()->route('teacher.classes.detail', $classId)
                         ->with('success', 'Assessment marks and details updated successfully!');
    }

    // Function storeAssessment tetap sama...
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
        
        // Create Speaking Test Placeholder agar tidak null saat dibuka
        SpeakingTest::create([
            'assessment_session_id' => $assessmentSession->id,
            'date' => null, // Guru nanti isi sendiri
            'interviewer_id' => Auth::id() // Default ke penginput
        ]);

        $typeLabel = ($request->type == 'mid') ? 'Mid Term Exam' : 'Final Exam';
        
        return redirect()->route('teacher.classes.detail', $classId)
                         ->with('success', $typeLabel . ' scheduled successfully.');
    }
}