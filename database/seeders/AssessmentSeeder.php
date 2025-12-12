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
        // 1. Ambil semua kelas yang aktif dan memiliki siswa
        $classes = ClassModel::with('students')->where('is_active', true)->get();

        // Ambil satu guru secara acak untuk jadi interviewer
        $interviewer = User::where('is_teacher', true)->inRandomOrder()->first();

        foreach ($classes as $class) {
            // Kita buat 2 jenis ujian: Mid dan Final
            $types = ['mid', 'final'];

            foreach ($types as $type) {
                
                // Tentukan tanggal (Mid bulan lalu, Final bulan ini/depan)
                $date = $type === 'mid' 
                    ? Carbon::now()->subMonths(2) 
                    : Carbon::now()->subMonth();

                // 2. Buat Assessment Session (Wadah Ujiannya)
                // Cek unique constraint (class_id, type) biar tidak error kalau run seeder berulang
                $session = AssessmentSession::firstOrCreate(
                    [
                        'class_id' => $class->id,
                        'type' => $type
                    ],
                    [
                        'date' => $date,
                    ]
                );

                // 3. Buat Speaking Test Event untuk sesi ini
                $speakingTest = SpeakingTest::firstOrCreate(
                    ['assessment_session_id' => $session->id],
                    [
                        'date' => $date->copy()->addDays(1), // Speaking test biasanya beda hari
                        'topic' => $type === 'mid' ? 'Describe your family' : 'Future Plans',
                        'interviewer_id' => $interviewer ? $interviewer->id : null,
                    ]
                );

                // 4. Loop semua siswa di kelas tersebut
                foreach ($class->students as $student) {
                    
                    // A. Generate Nilai Speaking (Detail)
                    // Content (0-50) + Participation (0-50)
                    $contentScore = rand(30, 50); // Anggap siswanya pintar (min 30)
                    $participationScore = rand(30, 50);
                    $totalSpeakingScore = $contentScore + $participationScore;

                    // Simpan ke Speaking Test Results
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
                    // Nilai speaking diambil dari $totalSpeakingScore di atas
                    AssessmentForm::updateOrCreate(
                        [
                            'assessment_session_id' => $session->id,
                            'student_id' => $student->id
                        ],
                        [
                            // Nilai acak untuk skill lain (0-100)
                            'vocabulary' => rand(60, 95),
                            'grammar'    => rand(60, 95),
                            'listening'  => rand(60, 95),
                            'reading'    => rand(60, 95),
                            'spelling'   => rand(60, 95),
                            
                            // PENTING: Nilai Speaking diambil dari hasil tes speaking
                            'speaking'   => $totalSpeakingScore, 
                        ]
                    );
                }
            }
        }
    }
}