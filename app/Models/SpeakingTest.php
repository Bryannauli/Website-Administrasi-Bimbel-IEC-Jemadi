<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;

class SpeakingTest extends Model
{
    use HasFactory, SoftDeletes;
    use LogsActivity;
    
    protected $fillable = [
        'assessment_session_id',
        'date',
        'topic',
        'interviewer_id',
    ];

    public function assessmentSession()
    {
        return $this->belongsTo(AssessmentSession::class);
    }

    public function interviewer()
    {
        return $this->belongsTo(User::class, 'interviewer_id')->where('is_teacher', true);
    }

    public function results()
    {
        return $this->hasMany(SpeakingTestResult::class, 'speaking_test_id');
    }
}
