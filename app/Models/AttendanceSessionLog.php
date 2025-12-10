<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceSessionLog extends Model
{
    protected $fillable = ['attendance_session_id', 'user_id', 'action', 'old_values', 'new_values'];
    protected $casts = ['old_values' => 'array', 'new_values' => 'array'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function attendanceSession()
    {
        return $this->belongsTo(AttendanceSession::class);
    }
}