<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

trait LogsActivity
{
    /**
     * Boot trait saat model digunakan.
     */
    public static function bootLogsActivity()
    {
        // 1. Event standar yang pasti ada
        $events = ['created', 'updated', 'deleted'];

        // 2. Cek apakah Model menggunakan SoftDeletes?
        // Jika YA, baru kita tambahkan event restored & forceDeleted
        if (in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses_recursive(static::class))) {
            $events[] = 'restored';
            $events[] = 'forceDeleted';
        }

        // 3. Daftarkan Event
        foreach ($events as $event) {
            static::$event(function (Model $model) use ($event) {
                $model->recordActivity($event);
            });
        }
    }

    /**
     * Logika utama pencatatan aktivitas.
     * (Sudah PUBLIC agar bisa dipanggil dari closure)
     */
    public function recordActivity($event)
    {
        // 1. TENTUKAN NAMA EVENT YANG LEBIH SPESIFIK
        if ($event === 'deleted') {
            // Cek apakah model menggunakan SoftDeletes dan apakah ini Soft Delete?
            if (method_exists($this, 'isForceDeleting') && !$this->isForceDeleting()) {
                $event = 'soft_deleted';
            }
        } elseif ($event === 'forceDeleted') {
            $event = 'force_deleted';
        }

        // 2. SIAPKAN DATA (PROPERTIES)
        $properties = [];
        
        // Daftar kolom yang HARAM dicatat (Security & Kebersihan DB)
        $hiddenAttributes = ['password', 'remember_token', 'updated_at', 'email_verified_at', 'deleted_at'];

        if ($event === 'updated') {
            $changes = $this->getChanges();
            
            foreach ($hiddenAttributes as $attr) {
                unset($changes[$attr]);
            }

            if (empty($changes)) return; 

            $properties = [
                'old' => array_intersect_key($this->getOriginal(), $changes),
                'attributes' => $changes
            ];
        } 
        elseif ($event === 'created') {
            $attributes = $this->toArray();
            foreach ($hiddenAttributes as $attr) unset($attributes[$attr]); 
            $properties = ['attributes' => $attributes];
        } 
        elseif (in_array($event, ['soft_deleted', 'force_deleted', 'deleted'])) {
            $old = $this->toArray();
            foreach ($hiddenAttributes as $attr) unset($old[$attr]);
            
            $properties = [
                'old' => $old,
                'attributes' => $event === 'soft_deleted' ? ['deleted_at' => now()] : null
            ];
        }
        elseif ($event === 'restored') {
            $properties = [
                'old' => ['deleted_at' => $this->deleted_at], 
                'attributes' => ['deleted_at' => null]
            ];
        }

        // 3. SIMPAN LOG (Pakai try-catch biar aman saat seeding CLI)
        try {
            ActivityLog::create([
                'actor_type'   => Auth::check() ? get_class(Auth::user()) : null,
                'actor_id'     => Auth::id(), 
                'subject_type' => get_class($this),
                'subject_id'   => $this->id,
                'event'        => $event,
                'description'  => strtoupper(str_replace('_', ' ', $event)) . " " . class_basename($this),
                'properties'   => $properties,
                'ip_address'   => request()->ip(),
                'user_agent'   => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            // Silent fail saat seeding/console command jika terjadi error auth/request
        }
    }

    /**
     * RELASI: Mengambil semua log aktivitas milik model ini.
     */
    public function activities()
    {
        return $this->morphMany(ActivityLog::class, 'subject')->latest();
    }
}