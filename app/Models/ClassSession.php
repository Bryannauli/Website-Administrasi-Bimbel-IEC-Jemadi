<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'teacher_id',
        'date',
        'comment',
    ];

    public function classModel()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function records()
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class)->where('is_teacher', true);
    }

    public function logs()
    {
        return $this->hasMany(ClassSessionLog::class);
    }
}
