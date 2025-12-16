<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;

class ClassSession extends Model
{
    use HasFactory, SoftDeletes;
    use LogsActivity;

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
}
