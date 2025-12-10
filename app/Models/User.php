<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'username',
        'name',
        'photo',
        'email',
        'phone',
        'password',
        'role',
        'is_teacher',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    /**
     * Cek apakah user adalah admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Cek apakah user adalah teacher
     */
    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    // Riwayat perubahan yang terjadi pada user ini
    public function logs()
    {
        return $this->hasMany(UserLog::class, 'user_id');
    }

    // Aktivitas yang dilakukan user ini
    public function activities()
    {
        return $this->hasMany(UserLog::class, 'actor_id');
    }
}
