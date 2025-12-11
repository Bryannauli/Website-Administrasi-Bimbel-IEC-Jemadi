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
        // Ini menggantikan query join/group by yang rumit sebelumnya
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
     * API JSON: Statistik Absensi (Tetap Eloquent / Query Builder)
     */
    public function getAttendanceStats(Request $request)
    {
        $type = $request->query('type', 'month'); // Default ganti jadi 'month'
        
        $query = AttendanceRecord::query();

        // Filter Waktu
        if ($type == 'today') {
            $query->whereHas('session', function($q) {
                $q->whereDate('date', Carbon::today());
            });
        } elseif ($type == 'month') {
            // FILTER BARU: Ambil bulan & tahun ini
            $query->whereHas('session', function($q) {
                $q->whereMonth('date', Carbon::now()->month)
                  ->whereYear('date', Carbon::now()->year);
            });
        }

        $stats = $query->select('status', DB::raw('count(*) as total'))
                        ->groupBy('status')
                        ->pluck('total', 'status')
                        ->toArray();

        return response()->json([
            'present'    => $stats['present'] ?? 0,
            'late'       => $stats['late'] ?? 0,
            'permission' => $stats['permission'] ?? 0,
            'sick'       => $stats['sick'] ?? 0,
            'absent'     => $stats['absent'] ?? 0,
        ]);
    }
}