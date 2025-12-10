<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpeakingTestResultLog extends Model
{
    use HasFactory;

    protected $fillable = ['speaking_test_result_id', 'user_id', 'action', 'old_values', 'new_values'];

    protected $casts = [
        'old_values' => 'array', 
        'new_values' => 'array'
    ];

    public function user() { return $this->belongsTo(User::class)->withTrashed(); }

    public function logs()
    {
        return $this->hasMany(SpeakingTestResultLog::class);
    }
}