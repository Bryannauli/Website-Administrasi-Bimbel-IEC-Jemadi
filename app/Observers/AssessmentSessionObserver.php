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

    public function updated(AssessmentSession $session): void
    {
        $changes = $session->getChanges();
        unset($changes['updated_at']);

        if (empty($changes)) return;

        $original = array_intersect_key($session->getOriginal(), $changes);

        AssessmentSessionLog::create([
            'assessment_session_id' => $session->id,
            'user_id'    => Auth::id(),
            'action'     => 'UPDATE',
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
            'action'     => 'SOFT_DELETE',
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
        AssessmentSessionLog::create([
            'assessment_session_id' => $session->id, // Akan jadi NULL (set null)
            'user_id'    => Auth::id(),
            'action'     => 'FORCE_DELETE',
            'old_values' => $session->toArray(),
            'new_values' => null,
        ]);
    }
}