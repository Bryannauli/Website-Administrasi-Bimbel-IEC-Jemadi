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
        // 1. Cek Data Guru (TETAP menggunakan Eloquent)
        $teacher = User::findOrFail($teacherId);

        // 2. Ambil Data Absensi menggunakan View
        // View v_teacher_attendance sudah melakukan JOIN ke attendance_sessions
        $attendance = DB::table('v_teacher_attendance')
            ->where('teacher_id', $teacherId)
            ->orderBy('record_id', 'desc')
            ->get();
            // Hasilnya adalah koleksi standar (stdClass) dari Query Builder

        return view('admin.attendance.detail', compact('teacher', 'attendance'));
    }
}