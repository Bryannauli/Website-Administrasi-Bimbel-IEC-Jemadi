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
            'user_id'    => Auth::id(), // Mengambil ID user yang sedang login
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
        // Mendapatkan hanya data yang berubah
        $changes = $student->getChanges();
        
        // Mendapatkan data asli sebelum berubah (untuk kolom yang berubah saja)
        $original = array_intersect_key($student->getOriginal(), $changes);

        StudentLog::create([
            'student_id' => $student->id,
            'user_id'    => Auth::id(), // Mengambil ID user yang sedang login
            'action'     => 'UPDATE',
            'old_values' => $original,
            'new_values' => $changes,
        ]);
    }

    /**
     * Handle the Student "deleted" event.
     */
    public function deleted(Student $student): void
    {
        StudentLog::create([
            'student_id' => $student->id,
            'user_id'    => Auth::id(),
            'action'     => 'DELETE',
            'old_values' => $student->toArray(),
            'new_values' => null,
        ]);
    }
}