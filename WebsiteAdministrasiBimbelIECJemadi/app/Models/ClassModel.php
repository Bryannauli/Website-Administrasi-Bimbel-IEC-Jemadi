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
        'number',
        'classroom',
        'status',
        'form_teacher_id',
        'local_teacher_id',
    ];

    // Relasi ke user (form teacher)
    public function formTeacher()
    {
        return $this->belongsTo(User::class, 'form_teacher_id')->where('role', 'teacher');
    }

    // Relasi ke user (local teacher)
    public function localTeacher()
    {
        return $this->belongsTo(User::class, 'local_teacher_id')->where('role', 'teacher');
    }

    // Relasi ke Student (satu kelas punya banyak siswa)
    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
