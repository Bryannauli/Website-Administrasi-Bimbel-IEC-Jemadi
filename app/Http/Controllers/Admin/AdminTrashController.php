<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User; // Untuk Teacher
use App\Models\Student;
use App\Models\ClassModel; // Ganti jika nama model kelas Anda berbeda (misalnya App\Models\Classes)

class AdminTrashController extends Controller
{
    /**
     * Menampilkan halaman sampah terpadu (Student, Teacher, Class)
     */
    public function index(Request $request)
    {
        // 1. Mengambil data dari semua model yang di-soft deleted
        // Ambil hanya kolom yang dibutuhkan dan tambahkan field 'type'
        $trashedTeachers = User::where('is_teacher', true)
                                ->onlyTrashed()
                                ->get(['id', 'name', 'deleted_at'])
                                ->map(fn ($item) => array_merge($item->toArray(), ['type' => 'teacher']));
                                
        $trashedStudents = Student::onlyTrashed()
                                ->get(['id', 'name', 'deleted_at'])
                                ->map(fn ($item) => array_merge($item->toArray(), ['type' => 'student']));

        $trashedClasses = ClassModel::onlyTrashed()
                                ->get(['id', 'name', 'deleted_at'])
                                ->map(fn ($item) => array_merge($item->toArray(), ['type' => 'class']));

        // 2. Menggabungkan dan mengurutkan berdasarkan deleted_at
        $allTrashed = collect()
                      ->merge($trashedTeachers)
                      ->merge($trashedStudents)
                      ->merge($trashedClasses)
                      ->sortByDesc('deleted_at');

        $totalCount = $allTrashed->count();
        
        // 3. Paginasi Manual untuk koleksi gabungan
        $perPage = 15;
        $page = $request->get('page', 1);
        $offset = ($page * $perPage) - $perPage;
        
        $paginatedItems = $allTrashed->slice($offset, $perPage)->all();
        $logs = new \Illuminate\Pagination\LengthAwarePaginator($paginatedItems, $totalCount, $perPage, $page, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);
        
        return view('admin.trash.trash', compact('logs', 'totalCount'));
    }
    
    // Helper function untuk mendapatkan Model Class berdasarkan type
    private function getModelClass($type)
    {
        return match($type) {
            'teacher' => User::class,
            'student' => Student::class,
            'class' => ClassModel::class, // Sesuaikan jika nama model Anda berbeda
            default => null,
        };
    }

    /**
     * Mengembalikan data dari trash (Restore)
     */
    public function restore(Request $request, $type, $id)
    {
        $modelClass = $this->getModelClass($type);
        if (!$modelClass) return back()->with('error', 'Invalid model type.');

        $item = $modelClass::withTrashed()->findOrFail($id);
        $item->restore();

        return redirect()->route('admin.trash.trash')->with('success', ucfirst($type) . ' ' . $item->name . ' has been restored successfully.');
    }

    /**
     * Menghapus permanen data dari trash (Force Delete)
     */
    public function forceDelete(Request $request, $type, $id)
    {
        $modelClass = $this->getModelClass($type);
        if (!$modelClass) return back()->with('error', 'Invalid model type.');

        $item = $modelClass::withTrashed()->findOrFail($id);
        $itemName = $item->name;
        
        // Cek apakah item adalah teacher/user sebelum forceDelete
        if ($type === 'teacher') {
            // Jika guru memiliki relasi/data lain, pastikan relasi tersebut di-handle
            // Misalnya: detach class sebelum delete
        }
        
        $item->forceDelete();

        return redirect()->route('admin.trash.trash')->with('success', ucfirst($type) . ' ' . $itemName . ' has been permanently deleted.');
    }
}