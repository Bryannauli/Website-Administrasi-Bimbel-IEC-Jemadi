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
        AssessmentSessionLog::create([
            'assessment_session_id' => $session->id,
            'user_id'    => Auth::id(),
            'action'     => 'DELETE',
            'old_values' => $session->toArray(),
            'new_values' => null,
        ]);
    }
}