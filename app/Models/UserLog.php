<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'actor_id',
        'action',
        'old_values',
        'new_values',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    // User yang diedit
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    // Pelaku
    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id')->withTrashed();
    }
}