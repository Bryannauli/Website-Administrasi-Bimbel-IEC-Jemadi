<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\User;       // Model Guru
use App\Models\ClassModel; // Model Kelas


class TeacherAdminController extends Controller
{
    /**
     * Menampilkan Daftar Semua Guru (Master Data)
     * Route: /admin/teachers
     */
    public function index(Request $request)
    {
        // 1. Ambil Input Filter
        $search = $request->input('search');
        $type = $request->input('type');
        $year = $request->input('year');       
        $classId = $request->input('class_id'); 

        // 2. Query Dasar: Ambil user dengan role 'teacher'
        $query = User::query()->where('role', 'teacher');

        // --- Logika Filter ---
        
        // Filter Search (Nama atau ID/NIP)
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%") 
                  ->orWhere('id', $search);
            });
        }

        // Filter Type (Form Teacher / Local Teacher)
        if ($type) {
            if ($type == 'Form Teacher') {
                $query->whereHas('formClasses'); 
            } elseif ($type == 'Local Teacher') {
                $query->whereHas('localClasses');
            }
        }

        // Filter Class & Year
        if ($classId && $classId != 'no_class') {
            $query->where(function($q) use ($classId) {
                $q->where('form_class_id', $classId)
                  ->orWhereHas('formClasses', function($fc) use ($classId) {
                      $fc->where('id', $classId);
                  })
                  ->orWhereHas('localClasses', function($lc) use ($classId) {
                      $lc->where('id', $classId);
                  });
            });
        }

        // 3. Pagination & Sorting
        $teachers = $query->orderBy('name')->paginate(10)->withQueryString();

        // 4. Hitung Statistik (Untuk Card di atas)
        $totalTeachers = User::where('role', 'teacher')->count();
        $totalActive   = User::where('role', 'teacher')->where('is_active', 1)->count();
        $totalInactive = User::where('role', 'teacher')->where('is_active', 0)->count();

        // 5. Data Pendukung Filter
        $classes = ClassModel::orderBy('name')->get();
        
        $years = ClassModel::select('academic_year')
                    ->distinct()
                    ->orderBy('academic_year', 'desc')
                    ->pluck('academic_year');

        $types = ['Form Teacher', 'Local Teacher'];

        // Kirim variabel ke view 'admin.teacher.index'
        return view('admin.teacher.index', compact(
            'teachers', 
            'totalTeachers', 
            'totalActive', 
            'totalInactive', 
            'classes', 
            'years', 
            'types'
        ));
    }


    public function create()
{
    // Ambil semua data kelas untuk dropdown
    $classes = ClassModel::all(); 

    // Kirim data $classes ke view
    return view('admin.teacher.add', compact('classes'));
}

    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'name'      => 'required|string|max:255',
            'username'  => 'required|string|max:255|unique:users',
            'email'     => 'required|email|unique:users',
            'phone'     => 'required|string|max:20',
            'password'  => 'required|min:8',
            'address'   => 'nullable|string',
            'type'      => 'required|in:Form Teacher,Local Teacher',
            'status'    => 'required|boolean',
            'photo'     => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'class_id'  => 'nullable|exists:classes,id', // Validasi kelas jika dipilih
        ]);

        // 2. Upload Foto (Jika ada)
        $photoPath = null;
        if ($request->hasFile('photo')) {
            // Simpan di folder: storage/app/public/profile-photos
            $path = $request->file('photo')->store('profile-photos', 'public');
            $photoPath = $path; 
            // Catatan: Pastikan sudah jalankan 'php artisan storage:link'
        }

        // 3. Simpan ke Tabel Users
        $teacher = User::create([
            'name'              => $request->name,
            'username'          => $request->username,
            'email'             => $request->email,
            'phone'             => $request->phone,
            'password'          => Hash::make($request->password),
            'address'           => $request->address,
            'role'              => 'teacher', // Set otomatis sebagai teacher
            'type'              => $request->type, // Opsional: simpan tipe di user juga jika perlu
            'is_active'         => $request->status,
            'profile_photo_path'=> $photoPath,
        ]);

        // 4. Logika Assign Kelas (Jika kelas dipilih)
        if ($request->filled('class_id')) {
            $class = ClassModel::find($request->class_id);
            
            if ($class) {
                if ($request->type == 'Form Teacher') {
                    // Update kolom wali kelas di tabel classes
                    $class->update(['form_teacher_id' => $teacher->id]);
                } elseif ($request->type == 'Local Teacher') {
                    // Update kolom guru pendamping di tabel classes
                    $class->update(['local_teacher_id' => $teacher->id]);
                }
            }
        }

        // 5. Redirect kembali dengan pesan sukses
        return redirect()->route('admin.teacher.index')->with('success', 'Teacher added successfully!');
    }
    /**
     * Menampilkan Detail Guru & Statistik Absensi
     * Route: /admin/teachers/{id}
     */
    // public function show($id)
    // {
    //     // 1. Ambil Data Guru
    //     $teacher = User::with(['formClasses', 'localClasses'])->where('role', 'teacher')->findOrFail($id);

    //     // 2. Logic Tipe Guru
    //     $isForm = $teacher->formClasses->isNotEmpty();
    //     $isLocal = $teacher->localClasses->isNotEmpty();
        
    //     if ($isForm && $isLocal) $type = 'Form & Local Teacher';
    //     elseif ($isForm) $type = 'Form Teacher';
    //     elseif ($isLocal) $type = 'Local Teacher';
    //     else $type = '-';

    //     // 3. Statistik Absensi (Bulan Ini)
    //     $currentMonth = now()->month;
    //     $currentYear = now()->year;

    //     $attendanceQuery = TeacherAttendanceRecord::where('teacher_id', $id)
    //         ->whereHas('session', function($q) use ($currentMonth, $currentYear) {
    //             $q->whereMonth('date', $currentMonth)
    //               ->whereYear('date', $currentYear);
    //         });

    //     $present = (clone $attendanceQuery)->where('status', 'present')->count();
    //     $absent  = (clone $attendanceQuery)->where('status', 'absent')->count();
    //     $sick    = (clone $attendanceQuery)->where('status', 'sick')->count();
    //     $late    = (clone $attendanceQuery)->whereIn('status', ['late', 'permission'])->count();

    //     $totalDays = $present + $absent + $sick + $late;
    //     $percentage = $totalDays > 0 ? round(($present / $totalDays) * 100) : 0;

    //     // 4. Data 7 Hari Terakhir
    //     $lastRecords = TeacherAttendanceRecord::with('session')
    //                     ->where('teacher_id', $id)
    //                     ->join('teacher_attendance_sessions', 'teacher_attendance_records.attendance_session_id', '=', 'teacher_attendance_sessions.id')
    //                     ->orderBy('teacher_attendance_sessions.date', 'desc')
    //                     ->select('teacher_attendance_records.*')
    //                     ->limit(7)
    //                     ->get()
    //                     ->reverse();

    //     return view('admin.teacher.show', compact(
    //         'teacher', 'type', 'present', 'absent', 'sick', 'late', 'totalDays', 'percentage', 'lastRecords'
    //     ));
    // }
}