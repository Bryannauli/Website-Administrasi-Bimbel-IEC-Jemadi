<?php

namespace App\Observers;

use App\Models\SpeakingTestResult;
use App\Models\SpeakingTestResultLog;
use Illuminate\Support\Facades\Auth;

class SpeakingTestResultObserver
{
    public function created(SpeakingTestResult $result): void
    {
        SpeakingTestResultLog::create([
            'speaking_test_result_id' => $result->id,
            'user_id'    => Auth::id(),
            'action'     => 'CREATE', // Input Nilai Awal
            'old_values' => null,
            'new_values' => $result->toArray(),
        ]);
    }

    public function updated(SpeakingTestResult $result): void
    {
        $changes = $result->getChanges();
        unset($changes['updated_at']);
        if (empty($changes)) return;

        $original = array_intersect_key($result->getOriginal(), $changes);

        SpeakingTestResultLog::create([
            'speaking_test_result_id' => $result->id,
            'user_id'    => Auth::id(),
            'action'     => 'UPDATE', // Perubahan Score Content/Participation
            'old_values' => $original,
            'new_values' => $changes,
        ]);
    }

    public function deleted(SpeakingTestResult $result): void
    {
        if ($result->isForceDeleting()) return;

        SpeakingTestResultLog::create([
            'speaking_test_result_id' => $result->id,
            'user_id'    => Auth::id(),
            'action'     => 'SOFT_DELETE',
            'old_values' => $result->toArray(),
            'new_values' => ['deleted_at' => now()],
        ]);
    }

    public function restored(SpeakingTestResult $result): void
    {
        SpeakingTestResultLog::create([
            'speaking_test_result_id' => $result->id,
            'user_id'    => Auth::id(),
            'action'     => 'RESTORE',
            'old_values' => ['deleted_at' => $result->deleted_at],
            'new_values' => ['deleted_at' => null],
        ]);
    }

    public function forceDeleted(SpeakingTestResult $result): void
    {
        SpeakingTestResultLog::create([
            'speaking_test_result_id' => $result->id,
            'user_id'    => Auth::id(),
            'action'     => 'FORCE_DELETE',
            'old_values' => $result->toArray(),
            'new_values' => null,
        ]);
    }
}