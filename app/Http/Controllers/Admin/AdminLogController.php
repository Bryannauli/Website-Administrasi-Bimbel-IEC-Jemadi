<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class AdminLogController extends Controller
{
    /**
     * Menampilkan Halaman Activity Log
     */
    public function index(Request $request)
    {
        // Ambil data log dengan paginasi
        $logs = ActivityLog::with(['actor', 'subject'])
                    ->latest() // Urutkan dari yang terbaru
                    ->paginate(15); // 15 item per halaman

        return view('admin.logs.log', compact('logs'));
    }

    /**
     * Menampilkan detail log tertentu (untuk melihat perubahan 'properties')
     */
    public function show(ActivityLog $log)
    {
        // Load relasi agar data Actor dan Subject muncul
        $log->load(['actor', 'subject']);
        
        // Kirim variabel $log ke view (pastikan di blade juga diupdate atau pakai compact)
        return view('admin.logs.detail-log', ['activityLog' => $log]);
    }
}