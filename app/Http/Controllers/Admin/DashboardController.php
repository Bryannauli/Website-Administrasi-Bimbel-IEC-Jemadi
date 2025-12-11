<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentLog;
use App\Models\AssessmentSessionLog;
use App\Models\AssessmentFormLog;
use App\Models\AttendanceSessionLog;
use App\Models\AttendanceRecordLog;
use App\Models\TeacherAttendanceRecordLog;
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
    // Menggunakan $request daripada $req
    $filter = $request->filter ?? 'today'; 
    
    // Ambil tanggal hari ini
    $today = Carbon::now()->toDateString();
    
    $record = null;

    if ($filter === 'today') {
        // Ambil data agregat HARI INI dari View
        $record = DB::table('attendance_summary_v')
                    ->where('date', $today)
                    ->first();
    } else {
        // Logika untuk filter 'all' (atau lainnya)
        // Lakukan SUM untuk semua kolom di seluruh View
        $record = DB::table('attendance_summary_v')
                    ->select(
                        DB::raw('SUM(total_present) as total_present'),
                        DB::raw('SUM(total_permission) as total_permission'),
                        DB::raw('SUM(total_sick) as total_sick'),
                        DB::raw('SUM(total_late) as total_late'),
                        DB::raw('SUM(total_absent) as total_absent')
                    )
                    ->first();
    }

    // Default hasil jika tidak ada data ditemukan
    $zeroSummary = [
        'present' => 0,
        'permission' => 0,
        'sick' => 0,
        'late' => 0,
        'absent' => 0,
    ];

    // Cek apakah ada record yang ditemukan
    if (!$record) {
        return response()->json($zeroSummary);
    }
    
    // Ambil total count yang sudah diagregasi dari record
    // Jika filter 'all', kita harus menghitung total dari SUM kolom-kolom status
    $total = $record->total_present + $record->total_permission + $record->total_sick + $record->total_late + $record->total_absent;

    // Kalau tidak ada data (total = 0), return semuanya 0%
    if ($total == 0) {
        return response()->json($zeroSummary);
    }

    // Ubah ke persen
    return response()->json([
        'present'    => round(($record->total_present / $total) * 100),
        'permission' => round(($record->total_permission / $total) * 100),
        'sick'       => round(($record->total_sick / $total) * 100),
        'late'       => round(($record->total_late / $total) * 100),
        'absent'     => round(($record->total_absent / $total) * 100),
    ]);
}

public function attendanceStats(Request $request)
{
    // Menggunakan $request->query() untuk mendapatkan parameter
    $type = $request->query('type', 'today'); // today / all

    $today = Carbon::now()->toDateString();
    
    $stats = null;

    if ($type === 'today') {
        // Ambil data agregat HARI INI dari View
        $stats = DB::table('attendance_summary_v')
                    ->where('date', $today)
                    ->select(
                        'total_present as present', 
                        'total_permission as permission', 
                        'total_sick as sick', 
                        'total_late as late', 
                        'total_absent as absent'
                    )
                    ->first();

    } else { // type === 'all'
        // Ambil data agregat KESELURUHAN dari View
        // Kita perlu menjumlahkan semua baris (tanggal) di View
        $stats = DB::table('attendance_summary_v')
                    ->select(
                        DB::raw('SUM(total_present) as present'),
                        DB::raw('SUM(total_permission) as permission'),
                        DB::raw('SUM(total_sick) as sick'),
                        DB::raw('SUM(total_late) as late'),
                        DB::raw('SUM(total_absent) as absent')
                    )
                    ->first();
    }
    
    // Pastikan hasil kembalian selalu berupa objek dengan nilai default 0 
    // jika tidak ada data (mirip dengan perilaku original fungsi Anda).
    if (is_null($stats) || ($type === 'today' && empty((array)$stats))) {
        return response()->json([
            'present' => 0,
            'permission' => 0,
            'sick' => 0,
            'late' => 0,
            'absent' => 0,
        ]);
    }
    
    return response()->json($stats);
}

public function weeklyAbsence()
{
    $data = \DB::select("CALL get_weekly_absence()");

    return response()->json($data);
}


}
