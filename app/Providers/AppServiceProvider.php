<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\ClassSession;
use App\Models\AttendanceRecord;  
use App\Models\TeacherAttendanceRecord;
use App\Models\AssessmentSession;
use App\Models\AssessmentForm;
use App\Models\SpeakingTest;
use App\Models\SpeakingTestResult;

use App\Observers\UserObserver;
use App\Observers\ClassObserver;
use App\Observers\StudentObserver;
use App\Observers\ClassSessionObserver;
use App\Observers\AttendanceRecordObserver;
use App\Observers\TeacherAttendanceRecordObserver;
use App\Observers\AssessmentSessionObserver;
use App\Observers\AssessmentFormObserver;
use App\Observers\SpeakingTestObserver;
use App\Observers\SpeakingTestResultObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        User::observe(UserObserver::class);
        ClassModel::observe(ClassObserver::class);
        Student::observe(StudentObserver::class);
        AssessmentSession::observe(AssessmentSessionObserver::class);
        AssessmentForm::observe(AssessmentFormObserver::class);
        ClassSession::observe(ClassSessionObserver::class);
        AttendanceRecord::observe(AttendanceRecordObserver::class);
        SpeakingTest::observe(SpeakingTestObserver::class);
        SpeakingTestResult::observe(SpeakingTestResultObserver::class);
    }
}
