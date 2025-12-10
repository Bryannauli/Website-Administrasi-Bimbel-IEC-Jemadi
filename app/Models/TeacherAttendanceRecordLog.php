<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherAttendanceRecordLog extends Model
{
    protected $table = 'teacher_attendance_record_logs';

    protected $fillable = ['teacher_attendance_record_id', 'user_id', 'action', 'old_values', 'new_values'];
    protected $casts = ['old_values' => 'array', 'new_values' => 'array'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function teacherAttendanceRecord()
    {
        return $this->belongsTo(TeacherAttendanceRecord::class);
    }
}