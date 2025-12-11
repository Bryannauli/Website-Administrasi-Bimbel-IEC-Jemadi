<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceRecord;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardAdminController extends Controller
{
    /**
     * Menampilkan Halaman Dashboard Utama (PAKAI STORED PROCEDURE)
     */
    public function index()
    {
        // Panggil Stored Procedure: p_GetDashboardStats
        // @variable adalah cara MySQL menyimpan output sementara
        $result = DB::select('CALL p_GetDashboardStats(@s, @t, @c, @b, @g)');
        
        // Ambil hasil output variable tersebut
        $stats = DB::select('SELECT @s as students, @t as teachers, @c as classes, @b as boys, @g as girls')[0];

        // Mapping ke variabel view
        $students = $stats->students;
        $teachers = $stats->teachers;
        $classes  = $stats->classes;
        $boys     = $stats->boys;
        $girls    = $stats->girls;

        return view('admin.dashboard', compact(
            'students', 
            'teachers', 
            'classes', 
            'boys', 
            'girls'
        ));
    }

    /**
     * API JSON: Laporan Absen Mingguan (PAKAI DATABASE VIEW)
     */
    public function getWeeklyAbsenceReport()
    {
        // 7 Hari terakhir
        $startDate = Carbon::now()->subDays(6)->format('Y-m-d');
        $endDate   = Carbon::now()->format('Y-m-d');

        // Mengambil data dari VIEW 'v_weekly_absence'
        // View ini tidak berubah, tetap 'v_weekly_absence'
        $data = DB::table('v_weekly_absence')
                    ->whereBetween('date', [$startDate, $endDate])
                    ->get()
                    ->keyBy('date'); 

        // Format data untuk Chart (Looping PHP untuk mengisi hari kosong dengan 0)
        $chartData = [];
        $currentDate = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        while ($currentDate->lte($end)) {
            $dateString = $currentDate->format('Y-m-d');
            
            $chartData[] = [
                'day_label' => $currentDate->format('D'),
                'date'      => $dateString,
                // Jika tidak ada data di view, set 0
                'total'     => isset($data[$dateString]) ? $data[$dateString]->total_absence : 0,
            ];
            
            $currentDate->addDay();
        }

        return response()->json($chartData);
    }

    /**
     * API JSON: Statistik Absensi
     */
    public function getAttendanceStats(Request $request)
    {
        $type = $request->query('type', 'month'); // Default: month
        
        // PERUBAHAN DI SINI: Ganti 'attendance_summary_v' menjadi 'v_attendance_summary'
        $query = DB::table('v_attendance_summary'); 
        $stats = null;

        if ($type == 'today') {
            // Filter TODAY: Cari 1 baris data di view
            $stats = $query->whereDate('date', Carbon::today())->first();

        } elseif ($type == 'month') {
            // Filter MONTH: Lakukan SUM dari semua baris view di bulan ini
            $stats = $query->whereMonth('date', Carbon::now()->month)
                            ->whereYear('date', Carbon::now()->year)
                            ->select(
                                DB::raw('SUM(total_present) as total_present'),
                                DB::raw('SUM(total_late) as total_late'),
                                DB::raw('SUM(total_permission) as total_permission'),
                                DB::raw('SUM(total_sick) as total_sick'),
                                DB::raw('SUM(total_absent) as total_absent')
                            )
                            ->first();
        }

        // Jika tidak ada data di view (null), kembalikan array 0
        if (is_null($stats) || empty((array)$stats)) {
            return response()->json([
                'present' => 0, 'late' => 0, 'permission' => 0, 'sick' => 0, 'absent' => 0
            ]);
        }

        // Rename/Map kolom ke format yang diharapkan JS (present, late, etc.)
        // Kolom di view: total_present, total_late, dll.
        // Kolom di JS: present, late, dll.
        return response()->json([
            'present'    => (int)($stats->total_present ?? $stats->present ?? 0),
            'late'       => (int)($stats->total_late ?? $stats->late ?? 0),
            'permission' => (int)($stats->total_permission ?? $stats->permission ?? 0),
            'sick'       => (int)($stats->total_sick ?? $stats->sick ?? 0),
            'absent'     => (int)($stats->total_absent ?? $stats->absent ?? 0),
        ]);
    }
}