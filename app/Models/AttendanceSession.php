<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'date',
    ];

    public function classModel()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function records()
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function teacherRecords()
    {
        return $this->hasMany(TeacherAttendanceRecord::class);
    }

    public function logs()
    {
        return $this->hasMany(AttendanceRecordLog::class);
    }
}
