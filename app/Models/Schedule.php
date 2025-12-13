<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'day_of_week',
        'teacher_type',
    ];

    /**
     * (OPSIONAL) Accessor untuk langsung mengambil data Guru yang bertugas.
     * Cara pakai: $schedule->assigned_teacher->name
     */
    public function getAssignedTeacherAttribute()
    {
        // Pastikan relasi classModel sudah di-load sebelumnya (Eager Loading)
        // untuk menghindari N+1 query.
        if (!$this->relationLoaded('classModel')) {
            $this->load('classModel.formTeacher', 'classModel.localTeacher');
        }

        if ($this->teacher_type === 'local') {
            return $this->classModel->localTeacher;
        }

        return $this->classModel->formTeacher;
    }
}
