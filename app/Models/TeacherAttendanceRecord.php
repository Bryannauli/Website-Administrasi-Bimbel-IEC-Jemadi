<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherAttendanceRecord extends Model
{
    protected $fillable = [
        'attendance_session_id',
        'teacher_id',
        'status',
        'comment',
    ];

    public function session()
    {
        return $this->belongsTo(AttendanceSession::class, 'attendance_session_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class)->where('role', 'teacher');
    }
}
