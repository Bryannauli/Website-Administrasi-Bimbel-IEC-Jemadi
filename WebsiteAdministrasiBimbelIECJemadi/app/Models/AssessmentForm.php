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
        $speakingTest = $this->student->speakingTests()
            ->where('type', $type)
            ->first();

        if ($speakingTest) {
            $this->speaking = $speakingTest->totalScore();
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
