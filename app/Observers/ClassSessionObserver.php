<?php

namespace App\Observers;

use App\Models\ClassSession;
use App\Models\ClassSessionLog;
use Illuminate\Support\Facades\Auth;

class ClassSessionObserver
{
    private function createLog(string $action, ClassSession $session, array $original = null, array $changes = null): void
    {
        ClassSessionLog::create([
            'class_session_id' => $session->id,
            'user_id'    => Auth::id(), 
            'action'     => $action,
            'old_values' => $original ? json_encode($original) : null,
            'new_values' => $changes ? json_encode($changes) : $session->toArray(),
        ]);
    }

    public function created(ClassSession $session): void
    {
        $this->createLog('CREATE', $session, null, $session->toArray());
    }

    public function updated(ClassSession $session): void
    {
        $changes = $session->getChanges();
        unset($changes['updated_at']); // Abaikan perubahan updated_at
        
        if (!empty($changes)) {
            $original = array_intersect_key($session->getOriginal(), $changes);
            $this->createLog('UPDATE', $session, $original, $changes);
        }
    }

    public function deleted(ClassSession $session): void
    {
        $this->createLog('DELETE', $session, $session->toArray(), null);
    }
}