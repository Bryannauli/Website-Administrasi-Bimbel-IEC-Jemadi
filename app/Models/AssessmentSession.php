<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;

class AssessmentSession extends Model
{
    use HasFactory, SoftDeletes;
    use LogsActivity;

    protected $fillable = [
        'class_id',
        'type',
        'written_date',    // [RENAMED] Dari 'date'
        'speaking_date',   // [NEW]
        'speaking_topic',  // [NEW]
        'interviewer_id',  // [NEW]
        'status',
    ];

    /**
     * Relasi ke Class
     */
    public function classModel()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    /**
     * Relasi ke Interviewer (Guru)
     */
    public function interviewer()
    {
        return $this->belongsTo(User::class, 'interviewer_id')->where('is_teacher', true);
    }

    /**
     * Relasi ke Nilai Written (Assessment Forms)
     */
    public function forms()
    {
        return $this->hasMany(AssessmentForm::class, 'assessment_session_id');
    }

    /**
     * Relasi ke Nilai Speaking (Speaking Test Results)
     * Langsung hasMany karena SpeakingTestResult sekarang punya assessment_session_id
     */
    public function speakingResults()
    {
        return $this->hasMany(SpeakingTestResult::class, 'assessment_session_id');
    }

    /**
     * Helper untuk cek apakah sesi terkunci
     */
    public function isLocked()
    {
        // Sesi terkunci jika statusnya 'submitted' (menunggu review) atau 'final' (sudah sah)
        return in_array($this->status, ['submitted', 'final']);
    }
}