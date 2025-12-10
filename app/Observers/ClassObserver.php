<?php

namespace App\Observers;

use App\Models\ClassModel;
use App\Models\ClassLog;
use Illuminate\Support\Facades\Auth;

class ClassObserver
{
    public function created(ClassModel $class): void
    {
        ClassLog::create([
            'class_id'   => $class->id,
            'user_id'    => Auth::id(),
            'action'     => 'CREATE',
            'old_values' => null,
            'new_values' => $class->toArray(),
        ]);
    }

    public function updated(ClassModel $class): void
    {
        $changes = $class->getChanges();
        
        // Bersihkan data teknis
        unset($changes['updated_at']);
        
        if (empty($changes)) return;

        $original = array_intersect_key($class->getOriginal(), $changes);

        ClassLog::create([
            'class_id'   => $class->id,
            'user_id'    => Auth::id(),
            'action'     => 'UPDATE',
            'old_values' => $original,
            'new_values' => $changes,
        ]);
    }

    /**
     * Handle Soft Delete
     */
    public function deleted(ClassModel $class): void
    {
        if ($class->isForceDeleting()) {
            return;
        }

        ClassLog::create([
            'class_id'   => $class->id,
            'user_id'    => Auth::id(),
            'action'     => 'SOFT_DELETE',
            'old_values' => $class->toArray(),
            'new_values' => ['deleted_at' => now()],
        ]);
    }

    /**
     * Handle Restore
     */
    public function restored(ClassModel $class): void
    {
        ClassLog::create([
            'class_id'   => $class->id,
            'user_id'    => Auth::id(),
            'action'     => 'RESTORE',
            'old_values' => ['deleted_at' => $class->deleted_at],
            'new_values' => ['deleted_at' => null],
        ]);
    }

    /**
     * Handle Force Delete
     */
    public function forceDeleted(ClassModel $class): void
    {
        ClassLog::create([
            'class_id'   => $class->id, // Akan jadi NULL di DB
            'user_id'    => Auth::id(),
            'action'     => 'FORCE_DELETE',
            'old_values' => $class->toArray(),
            'new_values' => null,
        ]);
    }
}