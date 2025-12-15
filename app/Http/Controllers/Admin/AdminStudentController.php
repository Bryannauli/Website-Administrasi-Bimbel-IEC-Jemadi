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
        $years = ClassModel::select('academic_year')->distinct()->orderBy('academic_year', 'desc')->pluck('academic_year');
        $categories = ClassModel::select('category')->distinct()->pluck('category');

        $classQuery = ClassModel::orderBy('name', 'asc');
        if ($request->filled('academic_year')) $classQuery->where('academic_year', $request->academic_year);
        if ($request->filled('category')) $classQuery->where('category', $request->category);
        $classes = $classQuery->get();

        // 2. Query Utama Siswa
        $query = Student::with('classModel');

        // --- FILTER LOGIC ---
        if (!$request->has('status')) {
            $query->where('is_active', true); 
        } elseif ($request->filled('status')) {
            if ($request->status == 'active') {
                $query->where('is_active', true);
            } elseif ($request->status == 'inactive') {
                $query->where('is_active', false);
            }
        }

        if ($request->filled('academic_year') && $request->class_id != 'no_class') {
            $query->whereHas('classModel', fn($q) => $q->where('academic_year', $request->academic_year));
        }

        if ($request->filled('category') && $request->class_id != 'no_class') {
            $query->whereHas('classModel', fn($q) => $q->where('category', $request->category));
        }

        if ($request->filled('class_id')) {
            $request->class_id == 'no_class'
                ? $query->whereNull('class_id')
                : $query->where('class_id', $request->class_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn($q) => $q->where('name', 'LIKE', "%$search%")
                                    ->orWhere('student_number', 'LIKE', "%$search%"));
        }

        // --- SORTING (UPDATED) ---
        $sort = $request->get('sort', 'number_asc'); // <<< Default jadi number_asc (ID)
        switch ($sort) {
            case 'name_asc': $query->orderBy('name', 'asc'); break;
            case 'name_desc': $query->orderBy('name', 'desc'); break;
            case 'number_desc': $query->orderBy('student_number', 'desc'); break;
            case 'oldest': $query->orderBy('created_at', 'asc'); break;
            case 'newest': $query->orderBy('created_at', 'desc'); break;
            case 'number_asc': 
            default: 
                $query->orderBy('student_number', 'asc'); // <<< Default Sort by ID
                break;
        }

        // 3. Eksekusi Query & Pagination
        $students = $query->paginate(10)->appends($request->query());

        // 4. Hitung Statistik Global
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