<?php

namespace App\Observers;

use App\Models\User;
use App\Models\UserLog;
use Illuminate\Support\Facades\Auth;

class UserObserver
{
    public function created(User $user): void
    {
        UserLog::create([
            'user_id'    => $user->id,
            'actor_id'   => Auth::id(),
            'action'     => 'CREATE',
            'old_values' => null,
            'new_values' => $user->toArray(),
        ]);
    }

    public function updated(User $user): void
    {
        $changes = $user->getChanges();
        
        // Bersihkan data sampah
        unset($changes['remember_token']); 
        unset($changes['updated_at']);

        if (empty($changes)) return;

        $original = array_intersect_key($user->getOriginal(), $changes);

        // Jika password berubah, kita sembunyikan hash aslinya demi keamanan log (Opsional)
        // Atau biarkan saja karena sudah di-hash.
        
        UserLog::create([
            'user_id'    => $user->id,
            'actor_id'   => Auth::id(),
            'action'     => 'UPDATE',
            'old_values' => $original,
            'new_values' => $changes,
        ]);
    }

    /**
     * MENANGANI SOFT DELETE (Masuk Tong Sampah)
     */
    public function deleted(User $user): void
    {
        if ($user->isForceDeleting()) {
            return; // Lanjut ke forceDeleted
        }

        UserLog::create([
            'user_id'    => $user->id,
            'actor_id'   => Auth::id(),
            'action'     => 'SOFT_DELETE', // Label lebih jelas
            'old_values' => $user->toArray(),
            'new_values' => ['deleted_at' => now()],
        ]);
    }

    /**
     * MENANGANI RESTORE (Dikembalikan)
     */
    public function restored(User $user): void
    {
        UserLog::create([
            'user_id'    => $user->id,
            'actor_id'   => Auth::id(),
            'action'     => 'RESTORE',
            'old_values' => ['deleted_at' => $user->deleted_at],
            'new_values' => ['deleted_at' => null],
        ]);
    }

    /**
     * MENANGANI HAPUS PERMANEN
     */
    public function forceDeleted(User $user): void
    {
        UserLog::create([
            'user_id'    => $user->id, // Akan jadi NULL di DB
            'actor_id'   => Auth::id(),
            'action'     => 'FORCE_DELETE',
            'old_values' => $user->toArray(), // Backup data terakhir
            'new_values' => null,
        ]);
    }
}