<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator; // Wajib import untuk paginasi manual collection
use App\Models\User;
use App\Models\Student;
use App\Models\ClassModel; // Pastikan nama model sesuai (ClassModel.php)

class AdminTrashController extends Controller
{
    /**
     * Menampilkan semua data yang di-soft delete (Unified Trash)
     */
    public function index(Request $request)
    {
        // 1. Ambil Data Teacher yang dihapus
        $teachers = User::onlyTrashed()
            ->where('is_teacher', true)
            ->select('id', 'name', 'deleted_at')
            ->get()
            ->map(function ($item) {
                $item->type = 'teacher'; // Inject label type
                return $item;
            });

        // 2. Ambil Data Student yang dihapus
        $students = Student::onlyTrashed()
            ->select('id', 'name', 'deleted_at')
            ->get()
            ->map(function ($item) {
                $item->type = 'student';
                return $item;
            });

        // 3. Ambil Data Class yang dihapus
        $classes = ClassModel::onlyTrashed()
            ->select('id', 'name', 'deleted_at')
            ->get()
            ->map(function ($item) {
                $item->type = 'class';
                return $item;
            });

        // 4. Gabungkan (Merge) dan Sortir berdasarkan waktu dihapus (Terbaru di atas)
        $mergedTrash = $teachers
            ->merge($students)
            ->merge($classes)
            ->sortByDesc('deleted_at')
            ->values(); // Reset keys agar urutan array bersih

        // 5. Konfigurasi Paginasi Manual
        $page = $request->get('page', 1); // Halaman saat ini
        $perPage = 10; // Jumlah item per halaman
        
        // Slice collection sesuai halaman
        $currentPageItems = $mergedTrash->slice(($page - 1) * $perPage, $perPage)->all();

        // Buat Object Paginator
        $logs = new LengthAwarePaginator(
            $currentPageItems,
            $mergedTrash->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        $totalCount = $mergedTrash->count();

        return view('admin.trash.trash', compact('logs', 'totalCount'));
    }

    /**
     * Helper untuk mendapatkan Model Class berdasarkan string type
     */
    private function getModelClass($type)
    {
        return match($type) {
            'teacher' => User::class,
            'student' => Student::class,
            'class'   => ClassModel::class,
            default   => null,
        };
    }

    /**
     * Restore Data
     */
    public function restore($type, $id)
    {
        $modelClass = $this->getModelClass($type);

        if (!$modelClass) {
            return back()->with('error', 'Invalid item type.');
        }

        // Cari data di tong sampah
        $item = $modelClass::onlyTrashed()->find($id);

        if ($item) {
            $item->restore(); // Kembalikan data
            return redirect()->route('admin.trash.trash')
                ->with('success', ucfirst($type) . " '{$item->name}' has been restored successfully.");
        }

        return back()->with('error', 'Data not found in trash.');
    }

    /**
     * Force Delete (Hapus Permanen)
     */
    public function forceDelete($type, $id)
    {
        $modelClass = $this->getModelClass($type);

        if (!$modelClass) {
            return back()->with('error', 'Invalid item type.');
        }

        $item = $modelClass::onlyTrashed()->find($id);

        if ($item) {
            $name = $item->name;
            $item->forceDelete(); // Hapus selamanya dari database

            return redirect()->route('admin.trash.trash')
                ->with('success', ucfirst($type) . " '{$name}' has been permanently deleted.");
        }

        return back()->with('error', 'Data not found in trash.');
    }
}