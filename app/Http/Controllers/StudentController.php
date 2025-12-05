<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;



class StudentController extends Controller
{
    // Halaman List Siswa
    public function index(Request $request)
    {
        // ==========================================
        // 1. PANGGIL STORED PROCEDURE (SUMMARY)
        // ==========================================
        try {
            $result = DB::select('CALL get_student_summary()');

            $total_students = $result[0]->total_students ?? 0;
            $total_active   = $result[0]->total_active ?? 0;
            $total_inactive = $result[0]->total_inactive ?? 0;

        } catch (\Exception $e) {
            $total_students = 0;
            $total_active   = 0;
            $total_inactive = 0;
        }


        // ==========================================
        // 2. QUERY DATA STUDENTS DARI DATABASE
        // ==========================================
        $query = \App\Models\Student::query();

        // SEARCH
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'LIKE', "%$search%")
                ->orWhere('student_id', 'LIKE', "%$search%");
            });
        }

        // PAGINATION
        $students = $query->paginate(5);


        // ==========================================
        // 3. RETURN VIEW
        // ==========================================
        return view('admin.student.student', compact(
            'students',
            'total_students',
            'total_active',
            'total_inactive'
        ));
    }


    // Halaman Form Tambah Siswa
    public function add()
    {
        return view('admin.student.add-student');
    }

    // Halaman Detail Siswa
    public function detail($id)
    {
        // Nanti $id digunakan untuk mengambil data dari database
        return view('admin.student.detail-student');
    }
    
    public function store(Request $request)
    {
        // Logika simpan ke database akan ada di sini
        // Untuk sementara redirect kembali ke index
        return redirect()->route('admin.student.index');
    }
}
