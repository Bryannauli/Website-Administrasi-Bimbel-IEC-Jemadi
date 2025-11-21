<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class StudentController extends Controller
{
    // Halaman List Siswa
   public function index(Request $request)
    {
        // ==========================================
        // DATA DUMMY (PENGGANTI DATABASE)
        // ==========================================
        $dummyStudents = collect([
            (object)[
                'id' => 1,
                'student_id' => 'S-2025001',
                'name' => 'Ahmad Yusuf',
                'email' => 'ahmad@example.com',
                'phone' => '0812-3456-7890',
                'gender' => 'Man',
                'class_name' => 'Vocabulary - Basic',
                'status' => 'active',
                'avatar_color' => 'bg-purple-100 text-purple-600',
                'avatar_initial' => 'AY'
            ],
            (object)[
                'id' => 2,
                'student_id' => 'S-2025002',
                'name' => 'Budi Santoso',
                'email' => 'budi@example.com',
                'phone' => '0812-9876-5432',
                'gender' => 'Man',
                'class_name' => 'Grammar - Intermediate',
                'status' => 'inactive',
                'avatar_color' => 'bg-pink-100 text-pink-600',
                'avatar_initial' => 'BS'
            ],
            (object)[
                'id' => 3,
                'student_id' => 'S-2025003',
                'name' => 'Citra Dewi',
                'email' => 'citra@example.com',
                'phone' => '0813-4567-8901',
                'gender' => 'Woman',
                'class_name' => 'Vocabulary - Basic',
                'status' => 'active',
                'avatar_color' => 'bg-blue-100 text-blue-600',
                'avatar_initial' => 'CD'
            ],
            (object)[
                'id' => 4,
                'student_id' => 'S-2025004',
                'name' => 'Dian Sastro',
                'email' => 'dian@example.com',
                'phone' => '0813-1122-3344',
                'gender' => 'Woman',
                'class_name' => 'Speaking - Advanced',
                'status' => 'pending',
                'avatar_color' => 'bg-yellow-100 text-yellow-600',
                'avatar_initial' => 'DS'
            ],
            (object)[
                'id' => 5,
                'student_id' => 'S-2025005',
                'name' => 'Eko Prasetyo',
                'email' => 'eko@example.com',
                'phone' => '0812-5566-7788',
                'gender' => 'Man',
                'class_name' => 'Grammar - Basic',
                'status' => 'active',
                'avatar_color' => 'bg-green-100 text-green-600',
                'avatar_initial' => 'EP'
            ],
             (object)[
                'id' => 6,
                'student_id' => 'S-2025006',
                'name' => 'Fani Rahma',
                'email' => 'fani@example.com',
                'phone' => '0812-9988-7766',
                'gender' => 'Woman',
                'class_name' => 'Listening - Intermediate',
                'status' => 'active',
                'avatar_color' => 'bg-red-100 text-red-600',
                'avatar_initial' => 'FR'
            ],
        ]);

        // ==========================================
        // LOGIKA SEARCH & PAGINATION MANUAL
        // ==========================================
        
        // 1. Filter Search (Jika ada input search)
        if ($request->has('search') && $request->search != '') {
            $searchTerm = strtolower($request->search);
            $dummyStudents = $dummyStudents->filter(function ($student) use ($searchTerm) {
                return str_contains(strtolower($student->name), $searchTerm) || 
                       str_contains(strtolower($student->student_id), $searchTerm);
            });
        }

        // 2. Pagination Manual (Agar links() di view tetap bekerja)
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 5; // Tampilkan 5 data per halaman
        $currentItems = $dummyStudents->slice(($currentPage - 1) * $perPage, $perPage)->all();
        
        $students = new LengthAwarePaginator($currentItems, count($dummyStudents), $perPage);
        $students->setPath($request->url()); // Set URL agar link pagination benar

        return view('admin.student.student', compact('students')); 
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
