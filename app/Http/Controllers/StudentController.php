<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StudentController extends Controller
{
    // Halaman List Siswa
    public function index()
    {
        return view('admin.student.student'); 
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
