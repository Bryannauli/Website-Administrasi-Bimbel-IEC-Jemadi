<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssessmentSession extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'class_id',
        'date',
        'type',
        'status', // <--- WAJIB DITAMBAHKAN
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

    /**
     * Helper untuk cek apakah sesi ini terkunci (tidak bisa diedit guru).
     * Bisa dipakai di Blade: @if($session->isLocked()) ... @endif
     */
    public function isLocked()
    {
        // Sesi terkunci jika statusnya 'submitted' (menunggu review) atau 'final' (sudah sah)
        return in_array($this->status, ['submitted', 'final']);
    }
}