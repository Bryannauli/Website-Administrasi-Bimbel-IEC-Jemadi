<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;

class AssessmentForm extends Model
{
    use HasFactory, SoftDeletes;
    use LogsActivity;

    protected $fillable = [
        'assessment_session_id',
        'student_id',
        'vocabulary',
        'grammar',
        'listening',
        'speaking',
        'reading',
        'spelling',
    ];

    public function session()
    {
        return $this->belongsTo(AssessmentSession::class, 'assessment_session_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Update total speaking dari speaking test result
     * [UPDATED LOGIC]
     */
    public function updateSpeakingFromTest()
    {
        // Cari result speaking berdasarkan Session ID dan Student ID
        $result = SpeakingTestResult::where('assessment_session_id', $this->assessment_session_id)
            ->where('student_id', $this->student_id)
            ->first();

        if ($result) {
            $this->speaking = $result->totalScore();
            $this->save();
        }
    }

    /**
     * Hitung rata-rata
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
            return null; 
        }

        return (int) round(array_sum($validScores) / count($validScores));
    }
}