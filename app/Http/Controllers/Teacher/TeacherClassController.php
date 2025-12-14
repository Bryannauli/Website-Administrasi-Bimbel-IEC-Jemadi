<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Auth tidak lagi krusial untuk filter, tapi mungkin masih dipakai di tempat lain
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\ClassSession;
use App\Models\AssessmentSession;
use Carbon\Carbon;

class TeacherClassController extends Controller
{
    // Menampilkan daftar kelas
    public function index(Request $request)
    {
        // $user = Auth::user(); // <-- Tidak lagi dipakai untuk memfilter query
        
        $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        
        // Tentukan Hari Filter
        if ($request->has('day')) {
            $currentDay = $request->input('day');
        } else {
            $currentDay = Carbon::now()->format('l');
        }

        // 1. Query untuk Daftar Kelas (Dropdown Filter Class Name)
        // MODIFIKASI: Hapus filter berdasarkan ID guru, ambil SEMUA kelas aktif
        $filterClassQuery = ClassModel::where('is_active', true)
            ->orderBy('name', 'asc');
            
        if ($request->filled('category')) {
            $filterClassQuery->where('category', $request->category);
        }

        $classesForFilter = $filterClassQuery->get();

        // 2. Query Utama (Data Tabel)
        $query = ClassModel::with(['schedules' => function ($q) use ($currentDay) {
            // Tetap filter jadwal berdasarkan hari yang dipilih (agar tampilan rapi)
            if (!empty($currentDay)) {
                $q->where('day_of_week', $currentDay);
            }
        }])
        ->where('is_active', true); // MODIFIKASI: Hanya ambil kelas aktif, tanpa cek guru

        // --- FILTER LOGIC ---
        
        // A. Search
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('classroom', 'LIKE', "%{$searchTerm}%");
            });
        }
        
        // B. Category Filter
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        // C. Class Name Filter
        if ($request->filled('class_id')) {
            $query->where('id', $request->class_id);
        }
        
        // D. Day Filter
        if (!empty($currentDay)) {
            $query->whereHas('schedules', function ($q) use ($currentDay) {
                $q->where('day_of_week', $currentDay);
            });
        }

        $classes = $query->paginate(10)
                        ->appends($request->except('page'));

        return view('teacher.classes.index', [
            'classes' => $classes,
            'classesForFilter' => $classesForFilter,
            'daysOfWeek' => $daysOfWeek, 
            'currentDay' => $currentDay, 
        ]);
    }

    // ... (Method detail dll tetap sama)
    public function detail(Request $request, $id)
    {
        $class = ClassModel::with('schedules')->findOrFail($id);
        
        $perPage = $request->input('per_page', 5);     
        $students = Student::where('class_id', $id)
            ->where('is_active', true)
            ->paginate($perPage, ['*'], 'student_page') 
            ->appends(request()->except('student_page'));

        $classSessions = ClassSession::where('class_id', $id)
            ->orderBy('date', 'desc')
            ->paginate(5, ['*'], 'attendance_page');

        $assessments = AssessmentSession::where('class_id', $id)
            ->orderBy('date', 'desc')
            ->paginate(5, ['*'], 'assessment_page');

        return view('teacher.classes.detail', compact('class', 'students', 'classSessions', 'assessments'));
    }
}