<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

trait LogsActivity
{
    /**
     * Event Eloquent yang akan dipantau otomatis.
     */
    protected static $recordEvents = ['created', 'updated', 'deleted', 'restored', 'forceDeleted'];

    /**
     * Boot trait saat model digunakan.
     */
    public static function bootLogsActivity()
    {
        foreach (static::$recordEvents as $event) {
            static::$event(function (Model $model) use ($event) {
                $model->recordActivity($event);
            });
        }
    }

    /**
     * Logika utama pencatatan aktivitas.
     */
    protected function recordActivity($event)
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
            
            // Hapus atribut terlarang dari daftar perubahan
            foreach ($hiddenAttributes as $attr) {
                unset($changes[$attr]);
            }

            // Jika setelah dibersihkan ternyata kosong (misal cuma update timestamps), batalkan log
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
        elseif (in_array($event, ['soft_deleted', 'force_deleted'])) {
            $old = $this->toArray();
            foreach ($hiddenAttributes as $attr) unset($old[$attr]);
            
            $properties = [
                'old' => $old,
                // Tambahkan info kapan dihapus untuk soft delete
                'attributes' => $event === 'soft_deleted' ? ['deleted_at' => now()] : null
            ];
        }
        elseif ($event === 'restored') {
            $properties = [
                'old' => ['deleted_at' => $this->deleted_at], // Tanggal hapus lama
                'attributes' => ['deleted_at' => null]        // Sekarang aktif lagi
            ];
        }

        // 3. SIMPAN KE DATABASE (Tabel activity_logs)
        ActivityLog::create([
            // ACTOR: Siapa yang melakukan? (User yang login)
            'actor_type'   => Auth::check() ? get_class(Auth::user()) : null,
            'actor_id'     => Auth::id(),
            
            // SUBJECT: Apa yang diubah? (Model ini sendiri)
            'subject_type' => get_class($this),
            'subject_id'   => $this->id,
            
            // DETAIL
            'event'        => $event,
            'description'  => strtoupper(str_replace('_', ' ', $event)) . " " . class_basename($this),
            'properties'   => $properties,
            
            // METADATA
            'ip_address'   => request()->ip(),
            'user_agent'   => request()->userAgent(),
        ]);
    }

    /**
     * RELASI: Mengambil semua log aktivitas milik model ini.
     * Contoh: $student->activities
     */
    public function activities()
    {
        return $this->morphMany(ActivityLog::class, 'subject')->latest();
    }
}