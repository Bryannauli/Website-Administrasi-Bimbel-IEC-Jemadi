<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'date',
        'type',
    ];

    // Relasi ke model kelas
    public function classModel()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    // Relasi ke banyak form nilai
    public function forms()
    {
        return $this->hasMany(AssessmentForm::class);
    }
    
    public function logs()
    {
        return $this->hasMany(AssessmentSessionLog::class);
    }
}
