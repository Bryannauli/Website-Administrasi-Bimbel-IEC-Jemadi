<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity; // <--- 1. Import Trait

class Schedule extends Model
{
    use HasFactory;
    use LogsActivity; // <--- 2. Pasang Trait

    protected $fillable = [
        'class_id',
        'day_of_week',
        'teacher_type',
    ];

    /**
     * Accessor untuk mengambil guru yang bertugas.
     */
    public function getAssignedTeacherAttribute()
    {
        if (!$this->relationLoaded('classModel')) {
            $this->load('classModel.formTeacher', 'classModel.localTeacher');
        }

        if ($this->teacher_type === 'local') {
            return $this->classModel->localTeacher;
        }

        return $this->classModel->formTeacher;
    }

    // Relasi balik ke ClassModel (diperlukan untuk accessor di atas)
    public function classModel()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }
}