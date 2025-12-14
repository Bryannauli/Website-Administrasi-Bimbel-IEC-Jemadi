<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Models\ClassModel;
use App\Models\Student;

class AdminStudentController extends Controller
{
    /**
     * Halaman List Siswa (Index)
     */
    public function index(Request $request)
    {
        // 1. Data Pendukung Filter (Dropdown)
        // Mengambil tahun dan kategori unik dari tabel classes
        $years = ClassModel::select('academic_year')->distinct()->orderBy('academic_year', 'desc')->pluck('academic_year');
        $categories = ClassModel::select('category')->distinct()->pluck('category');

        // Filter Dropdown Kelas (Dinamis berdasarkan filter tahun/kategori jika ada)
        $classQuery = ClassModel::orderBy('name', 'asc');
        if ($request->filled('academic_year')) $classQuery->where('academic_year', $request->academic_year);
        if ($request->filled('category')) $classQuery->where('category', $request->category);
        $classes = $classQuery->get();

        // 2. Query Utama Siswa dengan Eager Loading
        // Menggunakan 'classModel' sesuai definisi di model Student.php
        $query = Student::with('classModel');

        // --- FILTER LOGIC ---
        
        // A. Filter Status
        if (!$request->has('status')) {
            $query->where('is_active', true); // Default: Active only
        } elseif ($request->filled('status')) {
            if ($request->status == 'active') {
                $query->where('is_active', true);
            } elseif ($request->status == 'inactive') {
                $query->where('is_active', false);
            }
        }

        // B. Filter Academic Year (via Relasi Class)
        if ($request->filled('academic_year') && $request->class_id != 'no_class') {
            $query->whereHas('classModel', fn($q) => $q->where('academic_year', $request->academic_year));
        }

        // C. Filter Category (via Relasi Class)
        if ($request->filled('category') && $request->class_id != 'no_class') {
            $query->whereHas('classModel', fn($q) => $q->where('category', $request->category));
        }

        // D. Filter Specific Class
        if ($request->filled('class_id')) {
            $request->class_id == 'no_class'
                ? $query->whereNull('class_id')
                : $query->where('class_id', $request->class_id);
        }

        // E. Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn($q) => $q->where('name', 'LIKE', "%$search%")
                                    ->orWhere('student_number', 'LIKE', "%$search%"));
        }

        // --- SORTING ---
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'name_asc': $query->orderBy('name', 'asc'); break;
            case 'name_desc': $query->orderBy('name', 'desc'); break;
            case 'number_asc': $query->orderBy('student_number', 'asc'); break;
            case 'number_desc': $query->orderBy('student_number', 'desc'); break;
            case 'oldest': $query->orderBy('created_at', 'asc'); break;
            case 'newest': default: $query->orderBy('created_at', 'desc'); break;
        }

        // 3. Eksekusi Query & Pagination
        $students = $query->paginate(10)->appends($request->query());

        // 4. Hitung Statistik Global (Stored Procedure)
        // Menggunakan Procedure agar beban hitung ada di MySQL
        DB::statement('CALL p_get_student_global_stats(@total, @active, @inactive)');
        $stats = DB::select('SELECT @total AS total, @active AS active, @inactive AS inactive');
        
        $globalTotal = $stats[0]->total;
        $totalActive = $stats[0]->active;
        $totalInactive = $stats[0]->inactive;

        return view('admin.student.student', compact(
            'students', 'classes', 'years', 'categories',
            'totalActive', 'totalInactive', 'globalTotal'
        ));
    }

    /**
     * Halaman Form Tambah Siswa (Redirect ke Index karena pakai Modal)
     */
    public function add()
    {
        return redirect()->route('admin.student.index');
    }

    /**
     * Proses Simpan Siswa Baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_number' => 'required|unique:students',
            'name'           => 'required|string|max:255',
            'gender'         => 'required|in:male,female',
            'phone'          => 'nullable',
            'address'        => 'nullable',
            'class_id'       => 'nullable|exists:classes,id',
        ]);
        
        // Default 'is_active' => 1 saat create
        Student::create(array_merge($request->all(), ['is_active' => 1]));
        
        return back()->with('success', 'Student successfully added!');
    }
    
    /**
     * Halaman Detail Siswa
     */
    public function detail($id)
    {
        // 1. Ambil Data Siswa + Eager Load Kelas
        // Menggunakan 'with' mengurangi query n+1 saat menampilkan nama kelas di breadcrumb/header
        $student = Student::with('classModel')->findOrFail($id);

        // Data Pendukung untuk Modal Edit (di dalam halaman detail)
        $classes = ClassModel::where('is_active', true)->orderBy('category')->orderBy('name')->get();
        $categories = $classes->pluck('category')->unique();

        // 2. History Absensi
        // Hanya ambil history dari kelas yang SEDANG ditempati siswa (class_id saat ini)
        // Menggunakan View Database untuk performa read
        $attendance = DB::table('v_student_attendance')
            ->where('student_id', $id)
            ->where('class_id', $student->class_id)
            ->orderBy('session_date', 'ASC')
            ->get();

        // 3. Summary Statistik (Stored Procedure)
        // Menghitung total hadir/sakit/izin dsb secara efisien di DB
        $rawSummary = DB::select("CALL p_get_attendance_summary(?)", [$id]);
        $summaryData = $rawSummary[0];

        $summary = [
            'present'      => $summaryData->present,
            'absent'       => $summaryData->absent,
            'late'         => $summaryData->late,
            'permission'   => $summaryData->permission,
            'sick'         => $summaryData->sick,
        ];

        $totalDays = $summaryData->total_days;
        $presentPercent = round($summaryData->present_percent);

        // (Bagian Grafik 7 Hari dihapus karena tidak digunakan di View)

        return view('admin.student.detail-student', compact(
            'student', 'attendance', 'summary', 'presentPercent', 
            'totalDays', 
            'classes', 'categories'
        ));
    }

    /**
     * Proses Update Data Siswa
     */
    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        // 1. Definisikan Validasi
        $validator = Validator::make($request->all(), [
            'student_number' => ['required', 'string', Rule::unique('students')->ignore($id)],
            'name'           => 'required|string|max:255',
            'gender'         => 'required|in:male,female',
            'phone'          => 'nullable|string|max:30',
            'address'        => 'nullable|string',
            'class_id'       => 'nullable|exists:classes,id',
            'is_active'      => 'required|boolean', 
        ]);

        // 2. Cek Validasi Gagal
        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                // Kirim input lama + ID agar modal edit tahu siapa yang diedit & datanya tidak hilang
                ->withInput($request->all() + ['id' => $id]) 
                // Flag session untuk auto-open modal edit di frontend
                ->with('edit_failed', true);
        }

        // 3. Update Data
        $student->update([
            'student_number' => $request->student_number,
            'name'           => $request->name,
            'gender'         => $request->gender,
            'phone'          => $request->phone,
            'address'        => $request->address,
            'class_id'       => $request->class_id,
            'is_active'      => $request->is_active,
        ]);
        
        return back()->with('success', 'Student profile updated successfully.');
    }
    
    /**
     * Toggle Status (Active/Inactive)
     */
    public function toggleStatus($id)
    {
        $student = Student::findOrFail($id);
        $student->update(['is_active' => !$student->is_active]);
        
        $statusText = $student->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Student has been {$statusText}.");
    }
    
    /**
     * Soft Delete Siswa
     */
    public function delete($id)
    {
        try {
            $student = Student::findOrFail($id);
            $student->delete(); 

            return redirect()->route('admin.student.index')
                ->with('success', 'Student moved to trash.');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete student: ' . $e->getMessage());
        }
    }
}