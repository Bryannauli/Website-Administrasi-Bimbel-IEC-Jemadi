<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassModel extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'category',
        'name',
        'classroom',
        'start_time',
        'end_time',
        'status',
        'form_teacher_id',
        'local_teacher_id',
        'start_month',
        'end_month',
        'academic_year',
    ];

    // Relasi ke user (form teacher)
    public function formTeacher()
    {
        return $this->belongsTo(User::class, 'form_teacher_id')
                    ->where('is_teacher', true);
    }

    // Relasi ke user (local teacher)
    public function localTeacher()
    {
        return $this->belongsTo(User::class, 'local_teacher_id')
                    ->where('is_teacher', true);
    }

    // Relasi ke Student (satu kelas punya banyak siswa)
    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    // Relasi ke Schedule (satu kelas punya banyak jadwal)
    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'class_id');
    }
}
