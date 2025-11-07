<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpeakingTest extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'student_id',
        'type',
        'content_score',
        'participation_score',
        'date',
        'topic',
        'interviewer_id',
    ];

    public function classModel()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }
    
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function interviewer()
    {
        return $this->belongsTo(User::class, 'interviewer_id')->where('role', 'teacher');
    }

    public function totalScore()
    {
        return $this->content_score + $this->participation_score;
    }
}
