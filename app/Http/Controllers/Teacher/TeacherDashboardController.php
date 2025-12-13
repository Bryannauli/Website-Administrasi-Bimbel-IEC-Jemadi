<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\ClassModel; // Pastikan model ini di-import

class TeacherDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // 1. Tentukan Hari Ini (misal: "Monday", "Tuesday")
        $today = Carbon::now();
        $dayName = $today->format('l'); 

        // 2. Query Kelas berdasarkan Hari Ini dari tabel 'schedules'
        $todaysClasses = ClassModel::query()
            ->where('is_active', true) // Hanya kelas aktif
            // Filter: User harus guru di kelas itu (Form Teacher atau Local Teacher)
            ->where(function($q) use ($user) {
                $q->where('form_teacher_id', $user->id)
                  ->orWhere('local_teacher_id', $user->id);
            })
            // Filter: Kelas harus punya jadwal di hari ini
            ->whereHas('schedules', function($q) use ($dayName) {
                $q->where('day_of_week', $dayName);
            })
            // Urutkan berdasarkan jam mulai
            ->orderBy('start_time', 'asc')
            ->get();

        return view('teacher.dashboard', compact('user', 'today', 'todaysClasses'));
    }

    public function analytics()
    {
        return view('teacher.analytics');
    }
}