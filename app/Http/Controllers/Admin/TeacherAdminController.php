<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TeacherAttendanceRecordController;

use Illuminate\Http\Request;
use App\Models\User;       // Model Guru
use App\Models\ClassModel; // Model Kelas
use App\Models\TeacherAttendanceRecord;


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
  public function show($id)
    {
        // 1. Ambil Data Guru (TETAP DARI DATABASE)
        // Kita butuh ini agar Nama, ID, Foto, dan Email tetap asli
        $teacher = User::with(['formClasses', 'localClasses'])->where('role', 'teacher')->findOrFail($id);

        // 2. Logic Tipe Guru (TETAP)
        $isForm = $teacher->formClasses->isNotEmpty();
        $isLocal = $teacher->localClasses->isNotEmpty();
        
        if ($isForm && $isLocal) $type = 'Form & Local Teacher';
        elseif ($isForm) $type = 'Form Teacher';
        elseif ($isLocal) $type = 'Local Teacher';
        else $type = '-';

        // ==========================================================
        // 3. DUMMY DATA ATTENDANCE (DATA PALSU UNTUK TAMPILAN)
        // ==========================================================
        
        // Angka-angka statistik manual (bisa diubah sesuka hati)
        $present = 22;
        $late    = 3;
        $sick    = 1;
        $absent  = 2;
        $permission = 0;

        $totalDays = $present + $late + $sick + $absent + $permission;
        
        // Hitung persentase kehadiran (Present + Late)
        $percentage = $totalDays > 0 ? round((($present + $late) / $totalDays) * 100) : 0;

        // Data Summary untuk dikirim ke View
        $summary = [
            'present' => $present,
            'late' => $late,
            'sick' => $sick,
            'absent' => $absent,
            'permission' => $permission
        ];

        // 4. Generate Dummy History (Pura-pura ada data 30 hari terakhir)
        // Kita pakai Collection agar fungsi di view seperti reverse() tetap jalan
        $dummyRecords = collect([]);

        for ($i = 0; $i < 15; $i++) {
            // Buat objek pura-pura (Mock Object)
            $mockRecord = new \stdClass();
            
            // Random status biar terlihat variatif
            $statuses = ['present', 'present', 'present', 'late', 'absent', 'sick']; 
            $mockRecord->status = $statuses[array_rand($statuses)];
            
            // Buat objek session di dalamnya
            $mockSession = new \stdClass();
            $mockSession->date = now()->subDays($i)->format('Y-m-d'); // Tanggal mundur
            
            $mockRecord->session = $mockSession;

            $dummyRecords->push($mockRecord);
        }

        // Variabel untuk Timeline (scroll) dan Last 7 Days
        $attendance = $dummyRecords; 
        $lastRecords = $dummyRecords->take(7); // Ambil 7 data teratas

        // ==========================================================
        // END DUMMY DATA
        // ==========================================================

return view('admin.teacher.show', compact(
            'teacher', 
            'type', 
            'totalDays', 
            'percentage', 
            'summary', 
            'lastRecords', 
            'attendance',
            // Tambahkan variabel ini agar terbaca di View:
            'present', 
            'late', 
            'sick', 
            'absent', 
            'permission'
        ));
    }
}