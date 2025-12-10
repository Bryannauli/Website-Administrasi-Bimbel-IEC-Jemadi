<?php

namespace App\Observers;

use App\Models\SpeakingTest;
use App\Models\SpeakingTestLog;
use Illuminate\Support\Facades\Auth;

class SpeakingTestObserver
{
    public function created(SpeakingTest $test): void
    {
        SpeakingTestLog::create([
            'speaking_test_id' => $test->id,
            'user_id'    => Auth::id(),
            'action'     => 'CREATE',
            'old_values' => null,
            'new_values' => $test->toArray(),
        ]);
    }

    public function updated(SpeakingTest $test): void
    {
        $changes = $test->getChanges();
        unset($changes['updated_at']);
        if (empty($changes)) return;

        $original = array_intersect_key($test->getOriginal(), $changes);

        SpeakingTestLog::create([
            'speaking_test_id' => $test->id,
            'user_id'    => Auth::id(),
            'action'     => 'UPDATE',
            'old_values' => $original,
            'new_values' => $changes,
        ]);
    }

    public function deleted(SpeakingTest $test): void
    {
        if ($test->isForceDeleting()) return;

        SpeakingTestLog::create([
            'speaking_test_id' => $test->id,
            'user_id'    => Auth::id(),
            'action'     => 'SOFT_DELETE',
            'old_values' => $test->toArray(),
            'new_values' => ['deleted_at' => now()],
        ]);
    }

    public function restored(SpeakingTest $test): void
    {
        SpeakingTestLog::create([
            'speaking_test_id' => $test->id,
            'user_id'    => Auth::id(),
            'action'     => 'RESTORE',
            'old_values' => ['deleted_at' => $test->deleted_at],
            'new_values' => ['deleted_at' => null],
        ]);
    }

    public function forceDeleted(SpeakingTest $test): void
    {
        SpeakingTestLog::create([
            'speaking_test_id' => $test->id,
            'user_id'    => Auth::id(),
            'action'     => 'FORCE_DELETE',
            'old_values' => $test->toArray(),
            'new_values' => null,
        ]);
    }
}