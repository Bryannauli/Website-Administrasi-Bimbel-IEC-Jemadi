<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceRecordLog extends Model
{
    protected $fillable = ['attendance_record_id', 'user_id', 'action', 'old_values', 'new_values'];
    protected $casts = ['old_values' => 'array', 'new_values' => 'array'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function attendanceRecord()
    {
        return $this->belongsTo(AttendanceRecord::class);
    }
}