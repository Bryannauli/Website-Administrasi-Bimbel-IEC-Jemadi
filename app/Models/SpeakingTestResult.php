<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;

class SpeakingTestResult extends Model
{
    use HasFactory, SoftDeletes;
    use LogsActivity;

    protected $fillable = [
        'assessment_session_id', // [UPDATED] FK baru
        'student_id',
        'content_score',
        'participation_score',
    ];

    /**
     * Relasi balik ke Assessment Session
     */
    public function assessmentSession()
    {
        return $this->belongsTo(AssessmentSession::class, 'assessment_session_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function totalScore(): int
    {
        return (int) $this->content_score + (int) $this->participation_score;
    }
}