<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class AdminTeacherController extends Controller
{
    /**
     * Menampilkan Daftar Guru
     */
    public function index(Request $request)
    {
        // 1. Ambil Input Filter
        $search = $request->input('search');
        $status = $request->input('status');
        $sort   = $request->input('sort');

        // 2. Query Dasar (User yang ditandai sebagai guru)
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
            case 'name_desc': $query->orderBy('name', 'desc'); break;
            case 'newest':    $query->orderBy('created_at', 'desc'); break;
            case 'oldest':    $query->orderBy('created_at', 'asc'); break;
            case 'name_asc': 
            default:          $query->orderBy('name', 'asc'); break;
        }
        
        // 3. Pagination
        $teachers = $query->paginate(10)->withQueryString();

        // 4. Statistik (Query terpisah agar angka tetap statis meski difilter)
        $totalTeachers = User::where('is_teacher', 1)->count();
        $totalActive   = User::where('is_teacher', 1)->where('is_active', 1)->count();
        $totalInactive = User::where('is_teacher', 1)->where('is_active', 0)->count();

        // Data pendukung (jika masih diperlukan untuk filter dropdown di masa depan)
        $classes = ClassModel::orderBy('name')->get();

        return view('admin.teacher.teacher', compact(
            'teachers', 
            'totalTeachers', 
            'totalActive', 
            'totalInactive',
            'classes'
        ));
    }

    /**
     * Menyimpan Data Guru Baru
     */
    public function store(Request $request)
    {
        // 1. Validasi (Email & Phone Nullable)
        $request->validate([
            'name'      => 'required|string|max:255',
            'username'  => 'required|string|max:255|unique:users',
            'password'  => 'required|min:8',
            'email'     => 'nullable|email|unique:users',
            'phone'     => 'nullable|string|max:20',
            'address'   => 'nullable|string',
        ]);

        // 2. Simpan User (Default Active & is_teacher)
        User::create([
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

        return redirect()->route('admin.teacher.index')->with('success', 'Teacher added successfully!');
    }
    
    /**
     * Menampilkan Detail Guru
     */
    public function show($id)
    {
        $teacher = User::with(['formClasses', 'localClasses'])->where('is_teacher', 1)->findOrFail($id);

        // Logic Label Tipe Guru
        $isForm = $teacher->formClasses->isNotEmpty();
        $isLocal = $teacher->localClasses->isNotEmpty();
        
        if ($isForm && $isLocal) $type = 'Form & Local Teacher';
        elseif ($isForm) $type = 'Form Teacher';
        elseif ($isLocal) $type = 'Local Teacher';
        else $type = '-';
        
        // --- DUMMY DATA ATTENDANCE ---
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
        // -----------------------------

        return view('admin.teacher.show', compact('teacher', 'type', 'totalDays', 'percentage', 'summary', 'lastRecords', 'attendance', 'present', 'late', 'sick', 'absent', 'permission'));
    }

    /**
     * Update Data Guru (Tanpa Password & Foto)
     */
    public function update(Request $request, User $teacher)
    {
        // 1. Gunakan Validator Manual agar bisa custom redirect
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:255',
            // Unique username & email kecuali punya diri sendiri
            'username'  => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($teacher->id)],
            'email'     => ['nullable', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($teacher->id)],
            'phone'     => 'nullable|string|max:20', 
            'status'    => 'required|boolean', 
            'address'   => 'nullable|string',
        ]);

        // 2. Jika Gagal, Redirect Back dengan Flag 'edit_error'
        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput()
                        ->with('edit_error', $teacher->id); // <--- KUNCI PERBAIKANNYA
        }

        // 3. Jika Sukses, Lakukan Update
        $teacher->update([
            'name'      => $request->name,
            'username'  => $request->username,
            'email'     => $request->email,
            'phone'     => $request->phone,
            'is_active' => $request->status, 
            'address'   => $request->address,
        ]);

        return redirect()->route('admin.teacher.show', $teacher->id)->with('success', 'Teacher updated successfully!');
    }
}