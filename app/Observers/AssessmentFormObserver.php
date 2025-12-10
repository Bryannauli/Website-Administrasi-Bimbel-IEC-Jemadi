<?php

namespace App\Observers;

use App\Models\AssessmentForm;
use App\Models\AssessmentFormLog;
use Illuminate\Support\Facades\Auth;

class AssessmentFormObserver
{
    public function created(AssessmentForm $form): void
    {
        AssessmentFormLog::create([
            'assessment_form_id' => $form->id,
            'user_id'    => Auth::id(),
            'action'     => 'CREATE',
            'old_values' => null,
            'new_values' => $form->toArray(),
        ]);
    }

    public function updated(AssessmentForm $form): void
    {
        $changes = $form->getChanges();
        unset($changes['updated_at']);

        if (empty($changes)) return;

        $original = array_intersect_key($form->getOriginal(), $changes);
        
        AssessmentFormLog::create([
            'assessment_form_id' => $form->id,
            'user_id'    => Auth::id(),
            'action'     => 'UPDATE',
            'old_values' => $original,
            'new_values' => $changes,
        ]);
    }

    public function deleted(AssessmentForm $form): void
    {
        if ($form->isForceDeleting()) return;

        AssessmentFormLog::create([
            'assessment_form_id' => $form->id,
            'user_id'    => Auth::id(),
            'action'     => 'SOFT_DELETE',
            'old_values' => $form->toArray(),
            'new_values' => ['deleted_at' => now()],
        ]);
    }

    public function restored(AssessmentForm $form): void
    {
        AssessmentFormLog::create([
            'assessment_form_id' => $form->id,
            'user_id'    => Auth::id(),
            'action'     => 'RESTORE',
            'old_values' => ['deleted_at' => $form->deleted_at],
            'new_values' => ['deleted_at' => null],
        ]);
    }

    public function forceDeleted(AssessmentForm $form): void
    {
        AssessmentFormLog::create([
            'assessment_form_id' => $form->id, 
            'user_id'    => Auth::id(),
            'action'     => 'FORCE_DELETE',
            'old_values' => $form->toArray(),
            'new_values' => null,
        ]);
    }
}