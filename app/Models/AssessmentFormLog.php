<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentFormLog extends Model
{
    // Nama tabel: 'assessment_form_logs'
    
    protected $fillable = [
        'assessment_form_id',
        'user_id',
        'action',
        'old_values',
        'new_values',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    // Relasi ke AssessmentForm
    public function assessmentForm()
    {
        return $this->belongsTo(AssessmentForm::class);
    }
}