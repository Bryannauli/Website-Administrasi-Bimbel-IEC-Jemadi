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
        AssessmentFormLog::create([
            'assessment_form_id' => $form->id,
            'user_id'    => Auth::id(),
            'action'     => 'DELETE',
            'old_values' => $form->toArray(),
            'new_values' => null,
        ]);
    }
}