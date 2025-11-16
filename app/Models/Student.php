<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'name',
        'gender',
        'phone',
        'address',
        'status',
        'class_id',
    ];

    // Relasi ke kelas
    public function classModel()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function speakingTests()
    {
        return $this->hasMany(SpeakingTest::class);
    }

    public function assessmentForms()
    {
        return $this->hasMany(AssessmentForm::class);
    }
}
