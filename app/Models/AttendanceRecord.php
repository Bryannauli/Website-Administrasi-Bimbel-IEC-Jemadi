<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class AttendanceRecord extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = [
        'class_session_id',
        'student_id',
        'status',
    ];

    public function session()
    {
        return $this->belongsTo(ClassSession::class, 'class_session_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
