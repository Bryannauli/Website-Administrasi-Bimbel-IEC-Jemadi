<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'student_id',
        'type',
        'date',
        'vocabulary',
        'grammar',
        'listening',
        'speaking',
        'reading',
        'spelling',
    ];

    public function classModel()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Update total speaking dari speaking test
     */
    public function updateSpeakingFromTest(string $type)
    {
        $speakingTest = $this->student->speakingTests()
            ->where('type', $type)
            ->first();

        if ($speakingTest) {
            $this->speaking = $speakingTest->totalScore();
            $this->save();
        }
    }
}
