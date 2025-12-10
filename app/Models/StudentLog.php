<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentLog extends Model
{
    protected $fillable = [
        'student_id',
        'user_id',
        'action',
        'old_values',
        'new_values',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function student()
    {
        return $this->belongsTo(Student::class); 
    }
}