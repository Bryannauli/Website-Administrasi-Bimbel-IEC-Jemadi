<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassSessionLog extends Model
{
    protected $fillable = ['class_session_id', 'user_id', 'action', 'old_values', 'new_values'];
    protected $casts = ['old_values' => 'array', 'new_values' => 'array'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function ClassSession()
    {
        return $this->belongsTo(ClassSession::class);
    }
}