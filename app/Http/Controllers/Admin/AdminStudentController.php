<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\ClassModel;
use App\Models\Student;
use Illuminate\Validation\ValidationException;

class AdminStudentController extends Controller
{
    /**
     * Halaman List Siswa (Index)
     */
    public function index(Request $request)
    {
        // 1. STATISTIK (DIHAPUS KARENA TIDAK DIGUNAKAN DI VIEW)
        // Kode lama yang dihapus:
        // $total_students = Student::count();
        // $total_active   = Student::where('is_active', 1)->count();
        // $total_inactive = Student::where('is_active', 0)->count();

        // 2. SIAPKAN DATA FILTER
        $years = ClassModel::select('academic_year')->distinct()->orderBy('academic_year', 'desc')->pluck('academic_year');
        $categories = ClassModel::select('category')->distinct()->pluck('category');

        // 3. Query Dropdown Kelas
        $classQuery = ClassModel::orderBy('name', 'asc');
        if ($request->filled('academic_year')) $classQuery->where('academic_year', $request->academic_year);
        if ($request->filled('category')) $classQuery->where('category', $request->category);
        $classes = $classQuery->get();

        // 4. QUERY DATA SISWA
        $query = Student::with('classModel');

        // Filter Logic
        if ($request->filled('academic_year') && $request->class_id != 'no_class') {
            $query->whereHas('classModel', fn($q) => $q->where('academic_year', $request->academic_year));
        }
        if ($request->filled('category') && $request->class_id != 'no_class') {
            $query->whereHas('classModel', fn($q) => $q->where('category', $request->category));
        }
        if ($request->filled('class_id')) {
            $request->class_id == 'no_class' ? $query->whereNull('class_id') : $query->where('class_id', $request->class_id);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn($q) => $q->where('name', 'LIKE', "%$search%")->orWhere('student_number', 'LIKE', "%$search%"));
        }

        // Sorting
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'name_asc': $query->orderBy('name', 'asc'); break;
            case 'name_desc': $query->orderBy('name', 'desc'); break;
            case 'number_asc': $query->orderBy('student_number', 'asc'); break;
            case 'oldest': $query->orderBy('created_at', 'asc'); break;
            case 'newest': default: $query->orderBy('created_at', 'desc'); break;
        }

        $students = $query->paginate(10)->appends($request->query());

        // Variabel statistik (total_students, total_active, total_inactive) tidak lagi di-compact
        return view('admin.student.student', compact(
            'students', 'classes', 'years', 'categories'
        ));
    }

    /**
     * Halaman Form Tambah Siswa (Sekarang via Modal)
     */
    public function add()
    {
        // Method ini seharusnya sudah dihapus, jika masih ada, sebaiknya redirect ke index
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
        
        // Pastikan 'is_active' diset ke 1 jika tidak ada di request (karena form add tidak mengirimnya)
        Student::create(array_merge($request->all(), ['is_active' => 1]));
        
        return back()->with('success', 'Student successfully added!');
    }
    
    /**
     * Halaman Detail Siswa (Juga menangani data untuk Modal Edit)
     */
    public function detail($id)
    {
        $student = Student::findOrFail($id);

        // Data untuk Modal Edit
        $classes = ClassModel::where('is_active', true)->orderBy('category')->orderBy('name')->get();
        $categories = $classes->pluck('category')->unique();

        // 1. Ambil History Absensi (View)
        $attendance = DB::table('v_student_attendance')
            ->where('student_id', $id)
            ->where('class_id', $student->class_id) // <--- FILTER PENTING
            ->orderBy('session_date', 'DESC')
            ->get();

        // 2. Ambil Summary Statistik (Stored Procedure)
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

        // 3. Grafik 7 Hari Terakhir
        $last7Days = [];
        $today = Carbon::today();

        for ($i = 6; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i)->format('Y-m-d');
            
            $record = DB::table('v_student_attendance')
                ->where('student_id', $student->id)
                ->where('class_id', $student->class_id)
                ->where('session_date', $date)
                ->select('status')
                ->first();

            $status = $record->status ?? 'none';
            $last7Days[] = ['date' => $date, 'day' => Carbon::parse($date)->format('D'), 'status' => $status];
        }
        
        $rangeStart = $today->copy()->subDays(6)->format('d M Y');
        $rangeEnd   = $today->format('d M Y');

        return view('admin.student.detail-student', compact(
            'student', 'attendance', 'summary', 'presentPercent', 
            'totalDays', 'last7Days', 'rangeStart', 'rangeEnd',
            'classes', 'categories'
        ));
    }

    /**
     * Proses Update Data Siswa
     */
    public function update(Request $request, $id)
    {
        // Tangani validasi di dalam blok try-catch untuk menangkap ValidationException
        try {
            $data = $request->validate([
                'student_number' => ['required', 'string', Rule::unique('students')->ignore($id)],
                'name'           => 'required|string|max:255',
                'gender'         => 'required|in:male,female',
                'phone'          => 'nullable|string|max:30',
                'address'        => 'nullable|string',
                'class_id'       => 'nullable|exists:classes,id',
                'is_active'      => 'required|boolean', 
            ]);
            
            $student = Student::findOrFail($id);
            $student->update($data);
            
            return back()->with('success', 'Student profile updated successfully.');
            
        } catch (ValidationException $e) {
            // PERBAIKAN: Jika validasi gagal
            // Kita perlu mengirimkan old('id') agar init() di Alpine bisa mengatur updateUrl
            return back()
                ->withErrors($e->errors())
                ->withInput($request->all() + ['id' => $id]) // Tambahkan 'id' ke old()
                ->with('edit_failed', true); // Flag agar Edit Modal terbuka
                
        } catch (\Throwable $e) {
            return back()->withInput($request->all() + ['id' => $id])
                ->with('error', 'Update failed: '.$e->getMessage())
                ->with('edit_failed', true);
        }
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
                ->with('success', 'Student moved to trash. Data is safe and hidden.');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete student: ' . $e->getMessage());
        }
    }
}