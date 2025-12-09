<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;



class StudentController extends Controller
{
    // Halaman List Siswa
    public function index(Request $request)
    {
        // 1. STATISTIK
        $total_students = Student::count();
        $total_active   = Student::where('is_active', 1)->count();
        $total_inactive = Student::where('is_active', 0)->count();


        // 2. SIAPKAN DATA FILTER (TAHUN & KELAS)
        // A. Ambil daftar tahun unik dari tabel kelas untuk dropdown pertama
        $years = ClassModel::select('academic_year')
                    ->distinct()
                    ->orderBy('academic_year', 'desc')
                    ->pluck('academic_year');

        // B. Query untuk isi Dropdown Kelas (Dropdown kedua)
        $classQuery = ClassModel::orderBy('name', 'asc');

        // Jika user MEMILIH Tahun, filter daftar kelasnya
        if ($request->filled('academic_year')) {
            $classQuery->where('academic_year', $request->academic_year);
        }
        
        $classes = $classQuery->get(); // Eksekusi query kelas

        // 3. QUERY DATA SISWA (UTAMA)
        $query = Student::with('classModel');

        // Filter A: Berdasarkan Tahun Akademik (via Relasi Kelas)
        // PENTING: Jika user mencari siswa "Tanpa Kelas", kita harus ABAIKAN filter tahun.
        // Karena siswa tanpa kelas tidak punya tahun akademik.
        if ($request->filled('academic_year') && $request->class_id != 'no_class') {
            $query->whereHas('classModel', function($q) use ($request) {
                $q->where('academic_year', $request->academic_year);
            });
        }

        // Filter B: Berdasarkan Kelas Spesifik
        if ($request->filled('class_id')) {
            if ($request->class_id == 'no_class') {
                // Cari yang class_id-nya NULL
                $query->whereNull('class_id'); 
            } else {
                // Cari berdasarkan ID kelas
                $query->where('class_id', $request->class_id);
            }
        }

        // Filter C: Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%$search%")
                    ->orWhere('student_number', 'LIKE', "%$search%");
            });
        }

        // Filter D: Sort
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'name_asc': $query->orderBy('name', 'asc'); break;
            case 'name_desc': $query->orderBy('name', 'desc'); break;
            case 'number_asc': $query->orderBy('student_number', 'asc'); break;
            case 'oldest': $query->orderBy('created_at', 'asc'); break;
            case 'newest': default: $query->orderBy('created_at', 'desc'); break;
        }

        // Eksekusi Pagination
        $students = $query->paginate(10)->appends($request->query());


        // 4. RETURN VIEW
        return view('admin.student.student', compact(
            'students',
            'classes',
            'years',
            'total_students',
            'total_active',
            'total_inactive'
        ));
    }


    // Halaman Form Tambah Siswa
    public function add()
    {
        $classes = ClassModel::where('is_active', 1)->get();

        return view('admin.student.add-student', compact('classes'));
    }

    // Halaman Detail Siswa
    public function detail($id)
    {
        $student = Student::findOrFail($id);

        // Ambil semua attendance student ini
        $attendance = AttendanceRecord::with('session')
                        ->where('student_id', $id)
                        ->orderBy('attendance_session_id', 'DESC')
                        ->get();

        // Summary Attendance
        $summary = [
            'present'     => $attendance->where('status', 'present')->count(),
            'absent'      => $attendance->where('status', 'absent')->count(),
            'late'        => $attendance->where('status', 'late')->count(),
            'permission'  => $attendance->where('status', 'permission')->count(),
            'sick'        => $attendance->where('status', 'sick')->count(),
        ];

        // Total Working Days
        $totalDays = $attendance->count();

        // Present Percentage
        $presentPercent = $totalDays > 0
            ? round(($summary['present'] / $totalDays) * 100)
            : 0;

        // Last 7 days attendance
        $last7Days = [];
        $today = Carbon::today();

        for ($i = 6; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i)->format('Y-m-d');

            $record = DB::table('attendance_records')
                ->join('attendance_sessions', 'attendance_records.attendance_session_id', '=', 'attendance_sessions.id')
                ->where('attendance_records.student_id', $student->id)
                ->where('attendance_sessions.date', $date)
                ->select('attendance_records.status')
                ->first();

            $status = $record->status ?? 'none'; // jika tidak ada data

            $last7Days[] = [
                'date' => $date,
                'day' => Carbon::parse($date)->format('D'), // M, T, W, T, F
                'status' => $status
            ];

            // Rentang tanggal
            $rangeStart = $today->copy()->subDays(6)->format('d M Y');
            $rangeEnd   = $today->format('d M Y');

        return view('admin.student.detail-student', compact(
            'student',
            'attendance',
            'summary',
            'presentPercent',
            'totalDays',
            'last7Days',
            'rangeStart',
            'rangeEnd',
        ));
    }}

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

        Student::create([
            'student_number' => $request->student_number,
            'name'           => $request->name,
            'gender'         => $request->gender,
            'phone'          => $request->phone,
            'address'        => $request->address,
            'class_id'       => $request->class_id,
            'is_active'      => 1,
        ]);

        return redirect()->route('admin.student.index')
                         ->with('success', 'Student berhasil ditambahkan!');
    }

    public function delete($id)
    {
        $student = Student::findOrFail($id);

        // ubah status jadi tidak aktif
        $student->update([
            'is_active' => 0
        ]);

        return redirect()
            ->route('admin.student.index')
            ->with('success', 'Student has been deactivated.');
    }

    public function edit($id)
    {
        $student = Student::findOrFail($id);
        $classes = ClassModel::where('is_active', 1)->get();

        return view('admin.student.edit-student', compact('student', 'classes'));
    }

    public function update(Request $request, $id)
    {
        // validasi sederhana
        $data = $request->validate([
            'student_number' => ['required', 'string', Rule::unique('students','student_number')->ignore($id)],
            'name'           => 'required|string|max:255',
            'gender'         => 'required|in:male,female',
            'phone'          => 'nullable|string|max:30',
            'address'        => 'nullable|string',
            'class_id'       => 'nullable|exists:classes,id',
            'is_active'      => 'nullable|boolean',
        ]);

        try {
            $student = Student::findOrFail($id);

            // Pastikan data boolean ter-handle
            if (!isset($data['is_active'])) {
                $data['is_active'] = 1;
            }

            $student->update($data);

            return redirect()->route('admin.student.index')
                ->with('success', 'Student updated successfully.');
        } catch (\Throwable $e) {
            // Log error supaya mudah dibaca
            \Log::error('Student update failed: '.$e->getMessage(), ['id'=>$id, 'data'=>$data]);

            return back()->withInput()
                ->with('error', 'Update failed: '.$e->getMessage());
        }
    }
}
