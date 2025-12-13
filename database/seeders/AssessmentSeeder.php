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

        // Siapkan cadangan guru
        $fallbackInterviewer = User::where('role', 'teacher')->where('is_active', true)->inRandomOrder()->first();

        foreach ($classes as $class) {
            
            // Tentukan apakah ini kelas khusus yang harus kosong
            $isEmptyExample = str_contains($class->name, 'Empty Assessment');

            // Tentukan Interviewer:
            $interviewerId = $class->local_teacher_id 
                                ?? $class->form_teacher_id 
                                ?? ($fallbackInterviewer ? $fallbackInterviewer->id : null);

            $types = ['mid', 'final'];

            foreach ($types as $type) {
                
                // =================================================================
                // 1. PENENTUAN TANGGAL SESI (KRITIS!)
                // =================================================================
                $sessionDate = $isEmptyExample 
                    ? null // JIKA KELAS KOSONG, DATE = NULL
                    : ($type === 'mid' ? Carbon::now()->subMonths(2) : Carbon::now()->subMonth());
                
                $speakingDate = $isEmptyExample 
                    ? null // JIKA KELAS KOSONG, DATE = NULL
                    : ($sessionDate ? $sessionDate->copy()->addDays(1) : null);
                
                $topic = $isEmptyExample 
                    ? null // JIKA KELAS KOSONG, TOPIK = NULL
                    : ($type === 'mid' ? 'Describe your family' : 'Future Plans');
                // =================================================================


                // 2. Buat Assessment Session
                $session = AssessmentSession::firstOrCreate(
                    [
                        'class_id' => $class->id,
                        'type' => $type
                    ],
                    [
                        'date' => $sessionDate, // Menggunakan variabel yang sudah dikondisikan
                    ]
                );

                // 3. Buat Speaking Test Event
                $speakingTest = SpeakingTest::firstOrCreate(
                    ['assessment_session_id' => $session->id],
                    [
                        'date' => $speakingDate, // Menggunakan variabel yang sudah dikondisikan
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