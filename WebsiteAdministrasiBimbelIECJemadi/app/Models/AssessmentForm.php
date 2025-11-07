<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'student_id',
        'type',
        'date',
        'vocabulary',
        'grammar',
        'listening',
        'speaking',
        'reading',
        'spelling',
    ];

    public function classModel()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Update total speaking dari speaking test
     */
    public function updateSpeakingFromTest(string $type)
    {
        // Temukan hasil tes speaking yang relevan menggunakan join
        $result = SpeakingTestResult::join('speaking_tests', 'speaking_tests.id', '=', 'speaking_test_results.speaking_test_id')
            ->where('speaking_tests.class_id', $this->class_id)             // Cocokkan Class ID
            ->where('speaking_tests.type', $this->type)                     // Cocokkan Type (mid/final)
            ->where('speaking_test_results.student_id', $this->student_id)  // Cocokkan Student ID
            ->select('speaking_test_results.*')                             // Pastikan mendapatkan data dari model result
            ->first();

        if ($result) {
            $this->speaking = $result->totalScore();
            $this->save();
        }
    }

    /**
     * Hitung rata-rata semua skor (abaikan null) dan bulatkan
     * @return int|null
     */
    public function averageScore(): ?int
    {
        $scores = [
            $this->vocabulary,
            $this->grammar,
            $this->listening,
            $this->speaking,
            $this->reading,
            $this->spelling,
        ];

        // Hanya ambil yang tidak null
        $validScores = array_filter($scores, fn($score) => !is_null($score));

        if (empty($validScores)) {
            return null; // jika semua null, kembalikan null
        }

        return (int) round(array_sum($validScores) / count($validScores));
    }
}
