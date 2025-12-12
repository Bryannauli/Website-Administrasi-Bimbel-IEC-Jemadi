<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherAttendanceSession extends Model
{
    use HasFactory;

    // Kolom yang boleh diisi
    protected $fillable = [
        'date', 
        'start_time', 
        'end_time', 
        'status'
    ];

    // Relasi: Satu sesi absensi punya banyak record (data per guru)
    public function records()
    {
        return $this->hasMany(TeacherAttendanceRecord::class, 'attendance_session_id');
    }
}