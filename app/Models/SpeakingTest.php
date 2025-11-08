<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpeakingTest extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'type',
        'date',
        'topic',
        'interviewer_id',
    ];

    public function classModel()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function interviewer()
    {
        return $this->belongsTo(User::class, 'interviewer_id')->where('role', 'teacher');
    }

    public function results()
    {
        return $this->hasMany(SpeakingTestResult::class, 'speaking_test_id');
    }
}
