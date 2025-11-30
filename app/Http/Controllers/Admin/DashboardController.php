<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Panggil procedure
        $genderData = DB::connection('iec_admin')->select('CALL get_active_student_gender()');
        $summaryData = DB::connection('iec_admin')->select('CALL get_summary_counts()');
        $summary = collect($summaryData)->keyBy('type');

        // Transform ke format chart
        $boys = $genderData[0]->total ?? 0;
        $girls = $genderData[1]->total ?? 0;

        $students = $summary['students']->total ?? 0;
        $teachers = $summary['teachers']->total ?? 0;
        $employees = ($summary['employees']->total ?? 0) - 1;
        $classes = $summary['classes']->total ?? 0;

        return view('admin.dashboard', compact('boys', 'girls', 'students', 'teachers', 'employees', 'classes'));
    }
}
