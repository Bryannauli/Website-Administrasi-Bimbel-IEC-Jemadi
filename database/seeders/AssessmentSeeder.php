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
        // 1. Ambil semua kelas yang aktif dan memiliki relasi students dan formTeacher
        $classes = ClassModel::with(['students', 'formTeacher'])->where('is_active', true)->get();

        // Siapkan cadangan guru (fallback terakhir jika Form & Local Teacher kosong)
        $fallbackInterviewer = User::where('role', 'teacher')->where('is_active', true)->inRandomOrder()->first();

        foreach ($classes as $class) {
            
            // Tentukan Interviewer:
            // Prioritas 1: Local Teacher (local_teacher_id)
            // Prioritas 2: Form Teacher (form_teacher_id)
            // Prioritas 3: Fallback (Guru Acak)
            $interviewerId = $class->local_teacher_id 
                                ?? $class->form_teacher_id 
                                ?? ($fallbackInterviewer ? $fallbackInterviewer->id : null);

            // Kita buat 2 jenis ujian: Mid dan Final
            $types = ['mid', 'final'];

            foreach ($types as $type) {
                
                // Tentukan tanggal
                $date = $type === 'mid' 
                    ? Carbon::now()->subMonths(2) 
                    : Carbon::now()->subMonth();

                // 2. Buat Assessment Session
                $session = AssessmentSession::firstOrCreate(
                    [
                        'class_id' => $class->id,
                        'type' => $type
                    ],
                    [
                        'date' => $date,
                    ]
                );

                // 3. Buat Speaking Test Event (Interviewer menyesuaikan prioritas di atas)
                $speakingTest = SpeakingTest::firstOrCreate(
                    ['assessment_session_id' => $session->id],
                    [
                        'date' => $date->copy()->addDays(1),
                        'topic' => $type === 'mid' ? 'Describe your family' : 'Future Plans',
                        'interviewer_id' => $interviewerId,
                    ]
                );

                // 4. Loop semua siswa di kelas tersebut
                foreach ($class->students as $student) {
                    
                    // A. Generate Nilai Speaking (Detail)
                    $contentScore = rand(30, 50); 
                    $participationScore = rand(30, 50);
                    $totalSpeakingScore = $contentScore + $participationScore;

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
                            'speaking'   => $totalSpeakingScore, 
                        ]
                    );
                }
            }
        }
    }
}