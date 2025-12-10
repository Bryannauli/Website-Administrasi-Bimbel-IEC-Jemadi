<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssessmentForm extends Model
{
    use HasFactory, SoftDeletes;

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
     * Update total speaking dari speaking test
     */
    public function updateSpeakingFromTest()
    {
        // Pastikan relasi session sudah ter-load
        $this->loadMissing('session');

        // Jika session tidak ada, tidak ada yang bisa dilakukan
        if (!$this->session) {
            return;
        }

        // Ambil info dari session induk
        $class_id = $this->session->class_id;
        $type = $this->session->type;
        $student_id = $this->student_id;

        // Temukan hasil tes speaking yang relevan menggunakan join
        $result = SpeakingTestResult::join('speaking_tests', 'speaking_tests.id', '=', 'speaking_test_results.speaking_test_id')
            ->where('speaking_tests.class_id', $class_id)             // Cocokkan Class ID
            ->where('speaking_tests.type', $type)                     // Cocokkan Type (mid/final)
            ->where('speaking_test_results.student_id', $student_id)  // Cocokkan Student ID
            ->select('speaking_test_results.*')                       // Pastikan mendapatkan data dari model result
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

    public function logs()
    {
        return $this->hasMany(AssessmentFormLog::class);
    }
}
