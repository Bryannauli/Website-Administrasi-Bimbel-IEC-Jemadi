<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Penting: untuk Auth::id()
use App\Models\ClassModel; // Penting: panggil Model

class ClassTeacherController extends Controller
{
    public function index()
    {
        $teacherId = Auth::id();

        // Ambil data kelas dimana user ini menjadi Form Teacher ATAU Local Teacher
        $classes = ClassModel::with('schedules') // Load relasi jadwal
            ->where(function($query) use ($teacherId) {
                $query->where('form_teacher_id', $teacherId)
                      ->orWhere('local_teacher_id', $teacherId);
            })
            ->orderBy('is_active', 'desc') // Kelas aktif paling atas
            ->orderBy('created_at', 'desc')
            ->paginate(10); // Batasi 10 baris per halaman

        return view('teacher.classes.index', compact('classes'));
    }

    public function show($id)
    {
        // Logika detail (akan kita bahas nanti jika view detail sudah siap)
        return view('teacher.classes.show');
    }

    public function detail($id)
    {
        // Redirect ke show atau gunakan view terpisah
        return view('teacher.classes.detail');
    }
}