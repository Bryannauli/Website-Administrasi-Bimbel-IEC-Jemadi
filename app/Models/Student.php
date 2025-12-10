<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_number',
        'name',
        'phone',
        'gender',
        'class_id',
        'address',
        'is_active',
    ];

    /**
     * Relasi ke kelas
     */
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

    public function logs()
    {
        return $this->hasMany(StudentLog::class);
    }
}
