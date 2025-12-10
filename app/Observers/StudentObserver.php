<?php

namespace App\Observers;

use App\Models\Student;
use App\Models\StudentLog;
use Illuminate\Support\Facades\Auth;

class StudentObserver
{
    /**
     * Handle the Student "created" event.
     */
    public function created(Student $student): void
    {
        StudentLog::create([
            'student_id' => $student->id,
            'user_id'    => Auth::id(),
            'action'     => 'CREATE',
            'old_values' => null,
            'new_values' => $student->toArray(),
        ]);
    }

    /**
     * Handle the Student "updated" event.
     */
    public function updated(Student $student): void
    {
        $changes = $student->getChanges();
        
        // Hapus updated_at dari log karena tidak penting
        unset($changes['updated_at']);

        // Jika tidak ada perubahan data (selain updated_at), jangan log
        if (empty($changes)) return;
        
        $original = array_intersect_key($student->getOriginal(), $changes);

        StudentLog::create([
            'student_id' => $student->id,
            'user_id'    => Auth::id(),
            'action'     => 'UPDATE',
            'old_values' => $original,
            'new_values' => $changes,
        ]);
    }

    /**
     * Handle the Student "deleted" event.
     * INI MENANGANI SOFT DELETE (Masuk Tong Sampah)
     */
    public function deleted(Student $student): void
    {
        // PENTING: Cek apakah ini Force Delete? 
        // Jika ya, return saja (biar ditangani oleh method forceDeleted di bawah)
        // Agar tidak terjadi double log.
        if ($student->isForceDeleting()) {
            return;
        }

        StudentLog::create([
            'student_id' => $student->id,
            'user_id'    => Auth::id(),
            'action'     => 'SOFT_DELETE', // Label log lebih jelas
            'old_values' => $student->toArray(),
            'new_values' => ['deleted_at' => now()],
        ]);
    }

    /**
     * Handle the Student "restored" event.
     * INI MENANGANI RESTORE (Dikembalikan dari Sampah)
     */
    public function restored(Student $student): void
    {
        StudentLog::create([
            'student_id' => $student->id,
            'user_id'    => Auth::id(),
            'action'     => 'RESTORE',
            'old_values' => ['deleted_at' => $student->deleted_at], // Tanggal dihapusnya
            'new_values' => ['deleted_at' => null], // Sekarang null lagi
        ]);
    }

    /**
     * Handle the Student "force deleted" event.
     * INI MENANGANI HAPUS PERMANEN
     */
    public function forceDeleted(Student $student): void
    {
        StudentLog::create([
            'student_id' => $student->id, // Ini akan masuk log sbg NULL (karena set null on delete)
            'user_id'    => Auth::id(),
            'action'     => 'FORCE_DELETE',
            'old_values' => $student->toArray(), // Simpan data terakhir sebelum musnah
            'new_values' => null,
        ]);
    }
}