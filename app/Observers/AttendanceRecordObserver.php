<?php

namespace App\Observers;

use App\Models\AttendanceRecord;
use App\Models\AttendanceRecordLog;
use Illuminate\Support\Facades\Auth;

class AttendanceRecordObserver
{
    private function createLog(string $action, AttendanceRecord $record, array $original = null, array $changes = null): void
    {
        AttendanceRecordLog::create([
            'attendance_record_id' => $record->id,
            'user_id'    => Auth::id(), 
            'action'     => $action,
            'old_values' => $original ? json_encode($original) : null,
            'new_values' => $changes ? json_encode($changes) : $record->toArray(),
        ]);
    }

    public function created(AttendanceRecord $record): void
    {
        $this->createLog('CREATE', $record, null, $record->toArray());
    }

    public function updated(AttendanceRecord $record): void
    {
        $changes = $record->getChanges();
        unset($changes['updated_at']);
        
        if (!empty($changes)) {
            $original = array_intersect_key($record->getOriginal(), $changes);
            $this->createLog('UPDATE', $record, $original, $changes);
        }
    }

    public function deleted(AttendanceRecord $record): void
    {
        $this->createLog('DELETE', $record, $record->toArray(), null);
    }
}