<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'user_id',
        'action',
        'old_values',
        'new_values',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    // Relasi ke User (Pelaku)
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    // Relasi ke Kelas
    public function classModel()
    {
        // Perhatikan nama relasinya 'classModel' biar konsisten, atau 'class'
        // Karena 'class' adalah reserved keyword di PHP, biasanya kita pakai nama lain
        // Tapi di relasi belongsTo, parameter string tabelnya aman.
        return $this->belongsTo(ClassModel::class, 'class_id')->withTrashed();
    }

    public function Logs()
    {
        return $this->hasMany(ClassLog::class);
    }
}