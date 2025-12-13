<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;       
use App\Models\ClassModel; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;


class AdminTeacherController extends Controller
{
    /**
     * Menampilkan Daftar Semua Guru (Master Data)
     * Route: /admin/teachers
     */
    public function index(Request $request)
    {
        // 1. Ambil Input Filter
        $search = $request->input('search');
        $status = $request->input('status'); // Filter Baru
        $sort   = $request->input('sort');   // Filter Baru

        // 2. Query Dasar
        $query = User::where('is_teacher', 1);

        // --- Logika Search ---
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // --- Logika Filter Status ---
        if ($status === 'active') {
            $query->where('is_active', 1);
        } elseif ($status === 'inactive') {
            $query->where('is_active', 0);
        }

        // --- Logika Sorting ---
        switch ($sort) {
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'name_asc':
            default:
                $query->orderBy('name', 'asc');
                break;
        }
        
        // 3. Pagination
        $teachers = $query->paginate(10)->withQueryString();

        // 4. Statistik (Untuk Cards)
        // Note: Kita gunakan query terpisah agar angka di Card tidak berubah saat difilter
        $totalTeachers = User::where('is_teacher', 1)->count();
        $totalActive   = User::where('is_teacher', 1)->where('is_active', 1)->count();
        $totalInactive = User::where('is_teacher', 1)->where('is_active', 0)->count();

        // 5. Data Pendukung (Hanya untuk Modal Add)
        $classes = ClassModel::orderBy('name')->get();

        return view('admin.teacher.teacher', compact(
            'teachers', 
            'totalTeachers', 
            'totalActive', 
            'totalInactive', 
            'classes'
        ));
    }


    public function create()
    {
        $classes = ClassModel::all(); 
        return view('admin.teacher.add', compact('classes'));
    }

    public function store(Request $request)
    {
        // 1. Validasi Sederhana
        $request->validate([
            'name'      => 'required|string|max:255',
            'username'  => 'required|string|max:255|unique:users',
            'email'     => 'nullable|email|unique:users', // Nullable
            'phone'     => 'nullable|string|max:20',      // Nullable
            'password'  => 'required|min:8',
            'address'   => 'nullable|string',
        ]);

        // 2. Simpan User Baru (Default Active, Default is_teacher)
        $teacher = User::create([
            'name'       => $request->name,
            'username'   => $request->username,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'password'   => Hash::make($request->password),
            'address'    => $request->address,
            'role'       => 'teacher', 
            'is_teacher' => true,      
            'is_active'  => true, // Default langsung Aktif
        ]);

        // Tidak ada lagi logika assign class di sini. Murni create user.

        return redirect()->route('admin.teacher.index')->with('success', 'Teacher added successfully!');
    }
    
    public function show($id)
    {
        $teacher = User::with(['formClasses', 'localClasses'])->where('is_teacher', 1)->findOrFail($id);

        $isForm = $teacher->formClasses->isNotEmpty();
        $isLocal = $teacher->localClasses->isNotEmpty();
        
        if ($isForm && $isLocal) $type = 'Form & Local Teacher';
        elseif ($isForm) $type = 'Form Teacher';
        elseif ($isLocal) $type = 'Local Teacher';
        else $type = '-';
        
        // Dummy Data Stats (Sama seperti sebelumnya)
        $present = 22; $late = 3; $sick = 1; $absent = 2; $permission = 0;
        $totalDays = $present + $late + $sick + $absent + $permission;
        $percentage = $totalDays > 0 ? round((($present + $late) / $totalDays) * 100) : 0;
        $summary = ['present' => $present, 'late' => $late, 'sick' => $sick, 'absent' => $absent, 'permission' => $permission];
        
        $dummyRecords = collect([]);
        for ($i = 0; $i < 15; $i++) {
            $mockRecord = new \stdClass();
            $statuses = ['present', 'present', 'present', 'late', 'absent', 'sick']; 
            $mockRecord->status = $statuses[array_rand($statuses)];
            $mockSession = new \stdClass();
            $mockSession->date = now()->subDays($i)->format('Y-m-d'); 
            $mockRecord->session = $mockSession;
            $dummyRecords->push($mockRecord);
        }
        $attendance = $dummyRecords; 
        $lastRecords = $dummyRecords->take(7); 

        return view('admin.teacher.show', compact('teacher', 'type', 'totalDays', 'percentage', 'summary', 'lastRecords', 'attendance', 'present', 'late', 'sick', 'absent', 'permission'));
    }

    public function update(Request $request, User $teacher)
    {
        // 1. Validasi Rules (HAPUS PHOTO)
        $rules = [
            'name'      => 'required|string|max:255',
            'username'  => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($teacher->id)],
            'email'     => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($teacher->id)],
            'phone'     => 'required|string|max:20', 
            'status'    => 'required|boolean', 
            'address'   => 'nullable|string',
            'password'  => 'nullable|string|min:8', 
        ];

        $validatedData = $request->validate($rules);

        // 2. Data Update (TANPA PHOTO)
        $dataToUpdate = [
            'name'      => $validatedData['name'],
            'username'  => $validatedData['username'],
            'email'     => $validatedData['email'],
            'phone'     => $validatedData['phone'],
            'is_active' => $validatedData['status'], 
            'address'   => $validatedData['address'] ?? null,
        ];

        if (!empty($request->password)) {
            $dataToUpdate['password'] = Hash::make($request->password);
        }

        $teacher->update($dataToUpdate);
        return redirect()->route('admin.teacher.show', $teacher->id)->with('success', 'Data guru berhasil diperbarui!');
    }

    public function toggleStatus(User $teacher)
    {
        // Toggle status: Jika 1 menjadi 0, jika 0 menjadi 1
        $newStatus = $teacher->is_active == 1 ? 0 : 1;
        $statusText = $newStatus == 1 ? 'Aktif' : 'Nonaktif';

        $teacher->update([
            'is_active' => $newStatus,
        ]);

        return back()->with('success', "Status guru {$teacher->name} berhasil diubah menjadi {$statusText}.");
    }
}
