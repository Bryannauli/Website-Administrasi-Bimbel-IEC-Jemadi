<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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

    public function attendanceSummary(Request $request)
{
    $filter = $req->filter ?? 'today';

    // Ambil tanggal hari ini
    $today = now()->toDateString();

    if ($filter === 'today') {
        $records = \App\Models\AttendanceRecord::whereHas('session', function ($q) use ($today) {
            $q->where('date', $today);
        })->get();
    } else {
        $records = \App\Models\AttendanceRecord::all();
    }

    $total = $records->count();

    // Kalau tidak ada data, return semuanya 0%
    if ($total == 0) {
        return response()->json([
            'present' => 0,
            'permission' => 0,
            'sick' => 0,
            'late' => 0,
            'absent' => 0,
        ]);
    }

    // Hitung masing-masing
    $present    = $records->where('status', 'present')->count();
    $permission = $records->where('status', 'permission')->count();
    $sick       = $records->where('status', 'sick')->count();
    $late       = $records->where('status', 'late')->count();
    $absent     = $records->where('status', 'absent')->count();

    // Ubah ke persen
    return response()->json([
        'present'    => round($present / $total * 100),
        'permission' => round($permission / $total * 100),
        'sick'       => round($sick / $total * 100),
        'late'       => round($late / $total * 100),
        'absent'     => round($absent / $total * 100),
    ]);
}
public function attendanceStats(Request $request)
{
    $type = $request->query('type', 'today'); // today / all

    if ($type === 'today') {
        $stats = Attendance::today()->selectRaw("
            SUM(status='present') as present,
            SUM(status='permission') as permission,
            SUM(status='sick') as sick,
            SUM(status='late') as late,
            SUM(status='absent') as absent
        ")->first();
    } else {
        $stats = Attendance::selectRaw("
            SUM(status='present') as present,
            SUM(status='permission') as permission,
            SUM(status='sick') as sick,
            SUM(status='late') as late,
            SUM(status='absent') as absent
        ")->first();
    }

    return response()->json($stats);
}

public function weeklyAbsence()
{
    $data = \DB::select("CALL get_weekly_absence()");

    return response()->json($data);
}


}
