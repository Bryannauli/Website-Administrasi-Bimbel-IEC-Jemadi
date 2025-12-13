<?php

namespace App\Observers;

use App\Models\AssessmentSession;
use App\Models\AssessmentSessionLog;
use Illuminate\Support\Facades\Auth;

class AssessmentSessionObserver
{
    public function created(AssessmentSession $session): void
    {
        AssessmentSessionLog::create([
            'assessment_session_id' => $session->id,
            'user_id'    => Auth::id(),
            'action'     => 'CREATE',
            'old_values' => null,
            'new_values' => $session->toArray(),
        ]);
    }

    /**
     * Logic Updated yang lebih Cerdas
     */
    public function updated(AssessmentSession $session): void
    {
        // Ambil apa saja yang berubah
        $changes = $session->getChanges();
        
        // Buang updated_at karena itu pasti berubah tiap save, nyampah di log
        unset($changes['updated_at']);

        if (empty($changes)) return;

        // Ambil data lama dari kolom yang berubah saja
        $original = array_intersect_key($session->getOriginal(), $changes);

        // --- DETEKSI JENIS AKSI ---
        $action = 'UPDATE'; // Default

        // Cek apakah kolom 'status' ikut berubah?
        if (isset($changes['status'])) {
            $newStatus = $changes['status'];
            
            // Berikan nama Action yang Keren & Jelas
            if ($newStatus === 'submitted') {
                $action = 'SUBMITTED';
            } elseif ($newStatus === 'final') {
                $action = 'FINALIZED (LOCKED)';
            } elseif ($newStatus === 'draft') {
                $action = 'REVERT TO DRAFT'; // Kasus Admin mengembalikan status
            } else {
                $action = 'STATUS CHANGE';
            }
        }

        AssessmentSessionLog::create([
            'assessment_session_id' => $session->id,
            'user_id'    => Auth::id(), // Pastikan user sedang login
            'action'     => $action,    // <-- Action dinamis
            'old_values' => $original,
            'new_values' => $changes,
        ]);
    }

    public function deleted(AssessmentSession $session): void
    {
        if ($session->isForceDeleting()) return;

        AssessmentSessionLog::create([
            'assessment_session_id' => $session->id,
            'user_id'    => Auth::id(),
            'action'     => 'SOFT_DELETE', // Arsip
            'old_values' => $session->toArray(),
            'new_values' => ['deleted_at' => now()],
        ]);
    }

    public function restored(AssessmentSession $session): void
    {
        AssessmentSessionLog::create([
            'assessment_session_id' => $session->id,
            'user_id'    => Auth::id(),
            'action'     => 'RESTORE',
            'old_values' => ['deleted_at' => $session->deleted_at],
            'new_values' => ['deleted_at' => null],
        ]);
    }

    public function forceDeleted(AssessmentSession $session): void
    {
        // Hati-hati: Karena record induknya hilang permanen, biasanya
        // assessment_session_id di log diset NULL (lewat constraint database)
        // atau log ini ikut terhapus.
        // Tapi jika log ingin disimpan sebagai 'yatim piatu' (orphan), biarkan saja.
        
        AssessmentSessionLog::create([
            'assessment_session_id' => null, // Putus relasi karena induknya musnah
            'user_id'    => Auth::id(),
            'action'     => 'FORCE_DELETE',
            'old_values' => $session->toArray(),
            'new_values' => null,
        ]);
    }
}