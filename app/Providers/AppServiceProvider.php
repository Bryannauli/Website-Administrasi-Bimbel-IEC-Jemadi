<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Observers\StudentObserver;
use App\Observers\AssessmentSessionObserver;
use App\Observers\AssessmentFormObserver;
use App\Observers\AttendanceSessionObserver;
use App\Observers\AttendanceRecordObserver;
use App\Observers\TeacherAttendanceRecordObserver;
use App\Models\Student;
use App\Models\AssessmentSession;
use App\Models\AssessmentForm;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;  
use App\Models\TeacherAttendanceRecord;

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
        Student::observe(StudentObserver::class);
        AssessmentSession::observe(AssessmentSessionObserver::class);
        AssessmentForm::observe(AssessmentFormObserver::class);
        AttendanceSession::observe(AttendanceSessionObserver::class);
        AttendanceRecord::observe(AttendanceRecordObserver::class);
        TeacherAttendanceRecord::observe(TeacherAttendanceRecordObserver::class);
    }
}
