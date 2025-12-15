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
        // 1. AMBIL DATA FILTER (Dari ClassModel, karena Student tidak punya kolom ini)
        
        // List Kelas untuk dropdown
        $classes = ClassModel::orderBy('name', 'asc')->get();
        
        // Ambil tahun unik dari tabel CLASSES
        $years = ClassModel::select('academic_year')
                        ->whereNotNull('academic_year')
                        ->distinct()
                        ->orderBy('academic_year', 'desc')
                        ->pluck('academic_year');
                        
        // Ambil kategori unik dari tabel CLASSES
        $categories = ClassModel::select('category')
                        ->whereNotNull('category')
                        ->distinct()
                        ->pluck('category');

        // 2. QUERY SISWA
        $query = Student::query();

        // --- FILTERING ---
        
        // Filter Pencarian (Nama atau ID)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('student_number', 'like', "%{$search}%");
            });
        }

        // Filter Kelas (Langsung ID)
        if ($request->filled('class_id')) {
            if ($request->class_id == 'no_class') {
                $query->whereNull('class_id');
            } else {
                $query->where('class_id', $request->class_id);
            }
        }

        // Filter Tahun Angkatan (Via Relasi Class)
        if ($request->filled('academic_year')) {
            $query->whereHas('classModel', function($q) use ($request) {
                $q->where('academic_year', $request->academic_year);
            });
        }

        // Filter Kategori (Via Relasi Class)
        if ($request->filled('category')) {
            $query->whereHas('classModel', function($q) use ($request) {
                $q->where('category', $request->category);
            });
        }

        // Filter Status Aktif/Inaktif
        if ($request->filled('status')) {
            if ($request->status == 'active') {
                $query->where('is_active', true);
            } elseif ($request->status == 'inactive') {
                $query->where('is_active', false);
            }
        }

        // --- SORTING ---
        $sort = $request->input('sort', 'newest'); // Default newest

        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'number_asc':
                $query->orderBy('student_number', 'asc');
                break;
            case 'number_desc':
                $query->orderBy('student_number', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'newest':
            default:
                $query->latest(); 
                break;
        }

        // 3. EKSEKUSI QUERY
        // Gunakan with('classModel') untuk mencegah N+1 Query problem
        $students = $query->with('classModel')->paginate(10)->withQueryString();

        // 4. STATISTIK (Opsional)
        $globalTotal = Student::count();
        $totalActive = Student::where('is_active', true)->count();
        $totalInactive = Student::where('is_active', false)->count();

        return view('admin.student.student', compact(
            'students', 
            'classes', 
            'years', 
            'categories', 
            'globalTotal', 
            'totalActive', 
            'totalInactive'
        ));
    }

    // ... (Method add, store, detail, update, toggleStatus, delete TETAP SAMA) ...
    
    public function add()
    {
        return redirect()->route('admin.student.index');
    }

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
        
        Student::create(array_merge($request->all(), ['is_active' => 1]));
        return back()->with('success', 'Student successfully added!');
    }
    
    public function detail($id)
    {
        $student = Student::with('classModel')->findOrFail($id);
        $classes = ClassModel::where('is_active', true)->orderBy('category')->orderBy('name')->get();
        $categories = $classes->pluck('category')->unique();

        $attendance = DB::table('v_student_attendance')
            ->where('student_id', $id)
            ->where('class_id', $student->class_id)
            ->orderBy('session_date', 'ASC')
            ->get();

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

        return view('admin.student.detail-student', compact(
            'student', 'attendance', 'summary', 'presentPercent', 
            'totalDays', 
            'classes', 'categories'
        ));
    }

    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'student_number' => ['required', 'string', Rule::unique('students')->ignore($id)],
            'name'           => 'required|string|max:255',
            'gender'         => 'required|in:male,female',
            'phone'          => 'nullable|string|max:30',
            'address'        => 'nullable|string',
            'class_id'       => 'nullable|exists:classes,id',
            'is_active'      => 'required|boolean', 
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput($request->all() + ['id' => $id]) 
                ->with('edit_failed', true);
        }

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
    
    public function toggleStatus($id)
    {
        $student = Student::findOrFail($id);
        $student->update(['is_active' => !$student->is_active]);
        $statusText = $student->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Student has been {$statusText}.");
    }
    
    public function delete($id)
    {
        try {
            $student = Student::findOrFail($id);
            $student->delete(); 
            return redirect()->route('admin.student.index')->with('success', 'Student moved to trash.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete student: ' . $e->getMessage());
        }
    }
}