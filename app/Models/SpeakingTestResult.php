<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SpeakingTestResult extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'speaking_test_id',
        'student_id',
        'content_score',
        'participation_score',
    ];

    public function session()
    {
        return $this->belongsTo(SpeakingTest::class, 'speaking_test_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function totalScore(): int
    {
        return $this->content_score + $this->participation_score;
    }

    
}
