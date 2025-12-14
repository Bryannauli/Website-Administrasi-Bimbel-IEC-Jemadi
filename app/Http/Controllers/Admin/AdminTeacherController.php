<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

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

        // ============================================================
        // 4. Hitung Statistik (MENGGUNAKAN STORED PROCEDURE) - UPDATED
        // ============================================================
        
        // Panggil Procedure
        DB::statement('CALL p_get_teacher_global_stats(@total, @active, @inactive)');
        
        // Ambil Hasil dari Variabel MySQL
        $stats = DB::select('SELECT @total AS total, @active AS active, @inactive AS inactive');
        
        $totalTeachers = $stats[0]->total;
        $totalActive   = $stats[0]->active;
        $totalInactive = $stats[0]->inactive;
        // ============================================================

        // Data pendukung
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
    public function detail(Request $request, $id)
    {
        $teacher = User::with(['formClasses', 'localClasses'])
                        ->where('is_teacher', 1)
                        ->findOrFail($id);

        // 1. Ambil Parameter Navigasi
        $ref = $request->input('ref'); 
        $refClassId = $request->input('class_id'); 
        
        // Cek apakah class ID valid jika ref=class
        $refClass = null;
        if ($ref === 'class' && $refClassId) {
            $refClass = ClassModel::find($refClassId);
        }

        // 1. Filter Date (Default: Awal bulan ini s/d Akhir bulan ini)
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate   = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // 2. QUERY DARI VIEW (Lebih Ringan & Bersih)
        // Kita tidak perlu melakukan JOIN manual lagi di sini
        $query = DB::table('v_teacher_teaching_history')
            ->where('teacher_id', $id)
            ->whereBetween('date', [$startDate, $endDate]);

        // 3. AMBIL DATA HISTORY (Timeline)
        // Gunakan clone() agar query object dasar tidak berubah
        $history = $query->clone()
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'asc') // Urutkan jam jika tanggal sama
            ->get();

        // 4. HITUNG STATISTIK (Aggregate dari View)
        // Query Count jauh lebih cepat pada View yang terindeks dibanding Join manual berulang
        $totalSessions = $query->clone()->count();
        $totalClasses  = $query->clone()->distinct('class_id')->count('class_id');

        $summary = [
            'total_sessions' => $totalSessions,
            'unique_classes' => $totalClasses,
        ];

        return view('admin.teacher.detail-teacher', compact(
            'teacher', 'history', 'summary', 'startDate', 'endDate',
            'ref', 'refClass'
        ));
    }

    /**
     * Update Data Guru
     */
    public function update(Request $request, User $teacher)
    {
        // 1. Validasi
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:255',
            'username'  => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($teacher->id)],
            'email'     => ['nullable', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($teacher->id)],
            'phone'     => 'nullable|string|max:20', 
            'status'    => 'required|boolean', 
            'address'   => 'nullable|string',
            // Validasi Password (Nullable, hanya dicek jika diisi)
            'password'  => 'nullable|string|min:8', 
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput()
                        ->with('edit_error', $teacher->id);
        }

        // 2. Siapkan Data Update Dasar
        $dataToUpdate = [
            'name'      => $request->name,
            'username'  => $request->username,
            'email'     => $request->email,
            'phone'     => $request->phone,
            'is_active' => $request->status, 
            'address'   => $request->address,
        ];

        // 3. Cek apakah Password diisi? (Fitur Reset Password)
        if ($request->filled('password')) {
            $dataToUpdate['password'] = Hash::make($request->password);
        }

        // 4. Update
        $teacher->update($dataToUpdate);

        return redirect()->route('admin.teacher.show', $teacher->id)->with('success', 'Teacher updated successfully!');
    }

    /**
     * Toggle Status Guru (Active <-> Inactive)
     */
    public function toggleStatus($id)
    {
        $teacher = User::where('is_teacher', 1)->findOrFail($id);

        // PROTEKSI: Tidak boleh menonaktifkan diri sendiri
        if ($teacher->id == Auth::id()) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }
        
        // Toggle is_active
        $teacher->update([
            'is_active' => !$teacher->is_active
        ]);

        $statusText = $teacher->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Teacher has been {$statusText}.");
    }

    public function delete($id)
    {
        try {
            $teacher = User::where('is_teacher', 1)->findOrFail($id);
            
            // PROTEKSI: Tidak boleh menghapus diri sendiri
            if ($teacher->id == Auth::id()) {
                return back()->with('error', 'You cannot delete your own account.');
            }

            $teacher->delete(); // Soft Delete

            return redirect()->route('admin.teacher.index')
                ->with('success', 'Teacher has been moved to trash.');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete teacher: ' . $e->getMessage());
        }
    }

    /**
     * Toggle Role User: teacher <-> admin
     */
    public function toggleRole($id)
    {
        $user = User::findOrFail($id);
        $currentRole = $user->role;
        
        // PROTEKSI: Tidak boleh mengubah role akun yang sedang login
        if ($user->id == Auth::id()) {
            return back()->with('error', 'You cannot change your own account role.');
        }

        // Tentukan Role Baru
        $newRole = ($currentRole === 'admin') ? 'teacher' : 'admin';
        
        // Update Role
        $user->update(['role' => $newRole]);

        $roleText = ucfirst($newRole); // Admin atau Teacher
        return back()->with('success', "User role has been changed to {$roleText} successfully.");
    }
}