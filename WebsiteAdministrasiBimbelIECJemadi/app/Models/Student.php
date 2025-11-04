<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'name',
        'phone',
        'address',
        'status',
        'class_id',
    ];

    // Relasi ke kelas
    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }
}
