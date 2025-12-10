<?php

namespace App\Observers;

use App\Models\AttendanceSession;
use App\Models\AttendanceSessionLog;
use Illuminate\Support\Facades\Auth;

class AttendanceSessionObserver
{
    private function createLog(string $action, AttendanceSession $session, array $original = null, array $changes = null): void
    {
        AttendanceSessionLog::create([
            'attendance_session_id' => $session->id,
            'user_id'    => Auth::id(), 
            'action'     => $action,
            'old_values' => $original ? json_encode($original) : null,
            'new_values' => $changes ? json_encode($changes) : $session->toArray(),
        ]);
    }

    public function created(AttendanceSession $session): void
    {
        $this->createLog('CREATE', $session, null, $session->toArray());
    }

    public function updated(AttendanceSession $session): void
    {
        $changes = $session->getChanges();
        unset($changes['updated_at']); // Abaikan perubahan updated_at
        
        if (!empty($changes)) {
            $original = array_intersect_key($session->getOriginal(), $changes);
            $this->createLog('UPDATE', $session, $original, $changes);
        }
    }

    public function deleted(AttendanceSession $session): void
    {
        $this->createLog('DELETE', $session, $session->toArray(), null);
    }
}