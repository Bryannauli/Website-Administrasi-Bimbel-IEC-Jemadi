<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClassModel;
use App\Models\User;
use App\Models\AssessmentSession;
use App\Models\SpeakingTest;
use App\Models\SpeakingTestResult;
use App\Models\AssessmentForm;
use Carbon\Carbon;

class AssessmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ambil semua kelas yang aktif
        $classes = ClassModel::with(['students', 'formTeacher'])->where('is_active', true)->get();

        // Siapkan cadangan guru (jika local/form teacher tidak ada)
        $fallbackInterviewer = User::where('role', 'teacher')->where('is_active', true)->inRandomOrder()->first();

        foreach ($classes as $class) {
            
            // Tentukan apakah ini kelas khusus yang harus kosong (untuk testing input manual)
            $isEmptyExample = str_contains($class->name, 'Empty Assessment');

            // Tentukan Interviewer: Prioritas Local -> Form -> Fallback
            $interviewerId = $class->local_teacher_id 
                                ?? $class->form_teacher_id 
                                ?? ($fallbackInterviewer ? $fallbackInterviewer->id : null);

            $types = ['mid', 'final'];

            foreach ($types as $type) {
                
                // =================================================================
                // 1. PENENTUAN TANGGAL & STATUS SESI
                // =================================================================
                $sessionDate = $isEmptyExample 
                    ? null 
                    : ($type === 'mid' ? Carbon::now()->subMonths(2) : Carbon::now()->subMonth());
                
                $speakingDate = $isEmptyExample 
                    ? null 
                    : ($sessionDate ? $sessionDate->copy()->addDays(1) : null);
                
                $topic = $isEmptyExample 
                    ? null 
                    : ($type === 'mid' ? 'Describe your family' : 'Future Plans');

                // Tentukan Status: 
                // Jika kosong -> 'draft'
                // Jika terisi (seeder) -> 'submitted' (siap direview admin)
                $status = $isEmptyExample ? 'draft' : 'submitted'; 
                // =================================================================

                // 2. Buat Assessment Session
                $session = AssessmentSession::firstOrCreate(
                    [
                        'class_id' => $class->id,
                        'type' => $type
                    ],
                    [
                        'date' => $sessionDate,
                        'status' => $status, // <--- KOLOM STATUS BARU (ENUM)
                    ]
                );

                // 3. Buat Speaking Test Event
                $speakingTest = SpeakingTest::firstOrCreate(
                    ['assessment_session_id' => $session->id],
                    [
                        'date' => $speakingDate,
                        'topic' => $topic, 
                        'interviewer_id' => $interviewerId,
                    ]
                );
                
                // =================================================================
                // PENCEGAHAN SEEDING NILAI (Jika nama kelas mengandung 'Empty Assessment')
                // =================================================================
                if ($isEmptyExample) {
                    continue; // Lompati proses pembuatan nilai siswa
                }
                // =================================================================

                // 4. Loop semua siswa di kelas tersebut (Hanya berjalan jika TIDAK di-skip)
                foreach ($class->students as $student) {
                    
                    // A. Generate Nilai Speaking (Detail)
                    // Max 50 per komponen
                    $contentScore = rand(30, 50); 
                    $participationScore = rand(30, 50);
                    $totalSpeakingScore = $contentScore + $participationScore; // Total Max 100

                    SpeakingTestResult::updateOrCreate(
                        [
                            'speaking_test_id' => $speakingTest->id,
                            'student_id' => $student->id
                        ],
                        [
                            'content_score' => $contentScore,
                            'participation_score' => $participationScore,
                        ]
                    );

                    // B. Buat Assessment Form (Rekap Nilai Semua Skill)
                    // Max 100 per komponen
                    AssessmentForm::updateOrCreate(
                        [
                            'assessment_session_id' => $session->id,
                            'student_id' => $student->id
                        ],
                        [
                            'vocabulary' => rand(60, 95),
                            'grammar'    => rand(60, 95),
                            'listening'  => rand(60, 95),
                            'reading'    => rand(60, 95),
                            'spelling'   => rand(60, 95),
                            'speaking'   => $totalSpeakingScore, // Ambil dari total speaking test result
                            // 'is_submitted' SUDAH DIHAPUS, tidak perlu di-seed lagi.
                        ]
                    );
                }
            }
        }
    }
}