<?php

namespace App\Http\Controllers;

use App\Models\ClassModel; 
use App\Models\Schedule; 
use App\Models\User; // Import Model User untuk ambil data guru
use Illuminate\Http\Request;

class ClassController extends Controller
{
    /**
     * Menampilkan daftar kelas dari Database
     */
    public function index(Request $request)
    {
        // 1. Load Data Kelas beserta relasinya (Guru & Jadwal)
        $query = ClassModel::with(['formTeacher', 'localTeacher', 'schedules']);

        // 2. Logika Search 
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('classroom', 'LIKE', "%{$searchTerm}%");
            });
        }
        
        // --- LOGIKA FILTER BARU ---
        // 3. Filter Academic Year
        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        // 4. Filter Category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        // 5. Filter Status (BARU)
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }
        
        // --- LOGIKA SORT ---
        $sort = $request->query('sort', 'newest'); 
        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // Ambil daftar kategori unik yang ada di database
        $categories = ['pre_level', 'level', 'step', 'private'];

        // Ambil daftar tahun akademik unik yang ada di database
        $years = ClassModel::select('academic_year')->distinct()->pluck('academic_year')->sortDesc();

        // 6. Pagination
        $classes = $query->paginate(10);
        $classes->appends($request->all());

        // 7. Ambil List Guru untuk Dropdown
        $teachers = User::where('is_teacher', true)->orderBy('name', 'asc')->get();

        // 8. Kirim data ke View
        return view('admin.classes.class', compact(
            'classes', 
            'teachers', 
            'years', 
            'categories'
        ));
    }

    /**
     * Menyimpan Data Kelas Baru (Create)
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'category' => 'required|string',
            'name' => 'required|string|max:100',
            'classroom' => 'required|string|max:50',
            'start_month' => 'required|string',
            'end_month' => 'required|string',
            'academic_year' => 'required',
            
            // Guru bersifat Opsional (Nullable)
            'form_teacher_id' => 'nullable|exists:users,id',
            'local_teacher_id' => 'nullable|exists:users,id',
            
            'start_time' => 'required', 
            'end_time' => 'required',
            
            // Validasi Array Hari (Checkbox)
            'days' => 'required|array',
            'days.*' => 'string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
        ]);

        // 2. Simpan Data Kelas Utama
        $class = ClassModel::create([
            'category' => $request->category,
            'name' => $request->name,
            'classroom' => $request->classroom,
            'start_month' => $request->start_month,
            'end_month' => $request->end_month,
            'academic_year' => $request->academic_year,
            'form_teacher_id' => $request->form_teacher_id,
            'local_teacher_id' => $request->local_teacher_id,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'is_active' => true, // Default Active
        ]);

        // 3. Simpan Jadwal Hari ke Tabel Schedules
        foreach ($request->days as $day) {
            Schedule::create([
                'class_id' => $class->id,
                'day_of_week' => $day,
            ]);
        }

        return redirect()->route('admin.classes.class')->with('success', 'Class created successfully!');
    }

    /**
     * Update Data Kelas (Edit)
     */
    public function update(Request $request, $id)
    {
        $class = ClassModel::findOrFail($id);

        // Validasi
        $request->validate([
            'name' => 'required|string|max:100',
            'classroom' => 'required|string|max:50',
            'form_teacher_id' => 'nullable|exists:users,id',
            'local_teacher_id' => 'nullable|exists:users,id',
            'days' => 'nullable|array',
        ]);

        // Update Data Utama
        $class->update([
            'category' => $request->category,
            'name' => $request->name,
            'classroom' => $request->classroom,
            'start_month' => $request->start_month,
            'end_month' => $request->end_month,
            'academic_year' => $request->academic_year,
            'form_teacher_id' => $request->form_teacher_id,
            'local_teacher_id' => $request->local_teacher_id,
            
            // Handle jika nama field di form beda (time_start vs start_time)
            'start_time' => $request->start_time ?? $class->start_time,
            'end_time' => $request->end_time ?? $class->end_time,
            
            'is_active' => $request->status == 'active' ? true : false,
        ]);

        // Sync Jadwal Hari: Hapus yang lama, insert yang baru
        $class->schedules()->delete(); 

        if ($request->has('days')) {
            foreach ($request->days as $day) {
                Schedule::create([
                    'class_id' => $class->id,
                    'day_of_week' => $day,
                ]);
            }
        }

        return redirect()->route('admin.classes.class')->with('success', 'Class updated successfully!');
    }
    
public function detailClass($id)
{
    // Ambil class sekaligus relasi schedules (sesuaikan nama relasi di model)
    $class = ClassModel::with(['schedules', 'formTeacher', 'localTeacher', 'students'])->findOrFail($id);

    // Ambil koleksi jadwal dari relasi
    $schedules = $class->schedules;

    // (Optional) hitung beberapa properti seperti di sebelumnya
    $class->students_count = $class->students ? $class->students->count() : 0;
    $class->teachers_count = collect([$class->formTeacher, $class->localTeacher])->filter()->count();
    $class->total_sessions = $class->schedules->count() * 4;
    $class->completed_sessions = 0; // ganti dengan logika attendance kalau ada
    $class->progress_percent = $class->total_sessions ? round(($class->completed_sessions / $class->total_sessions) * 100) : 0;

    // Pastikan nama view sesuai file blade kamu
    return view('admin.classes.detail-class', compact('class', 'schedules'));
}


    // --- Method Placeholder untuk View Detail ---
    // public function class($id) { return view('admin.classes.class2'); }
    // public function students($id) { return view('admin.classes.students'); }
}