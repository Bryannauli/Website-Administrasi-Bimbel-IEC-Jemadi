<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TeacherAttendanceRecord; // <--- Pastikan Model ini di-import
use App\Models\User;

class TeacherAttendanceRecordController extends Controller
{
    /**
     * Halaman Rekap Absensi (Index)
     */
    public function index()
    {
        // Contoh ambil semua data absensi dengan paginasi
        $records = TeacherAttendanceRecord::with(['teacher', 'session'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);

        return view('admin.attendance.index', compact('records'));
    }

    /**
     * Halaman Detail Absensi per Guru
     */
    public function detail($teacherId)
    {
        // 1. Cek Data Guru
        $teacher = User::findOrFail($teacherId);

        // 2. Ambil Data Absensi Menggunakan MODEL (Bukan Controller)
        $attendance = TeacherAttendanceRecord::where('teacher_id', $teacherId)
                        ->with('session')
                        ->orderBy('id', 'desc')
                        ->get();

        return view('admin.attendance.detail', compact('teacher', 'attendance'));
    }
}