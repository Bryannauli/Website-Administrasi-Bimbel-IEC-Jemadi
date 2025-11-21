<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    /**
     * Menampilkan daftar assessment.
     * Route: /admin/assessment
     */
    public function index()
    {
        // Nanti di sini kita ambil data dari database
        // $assessments = Assessment::all(); 
        
        // Untuk sekarang, kita return view saja
        return view('admin.assessment.index');
    }
      public function show()
    {
    return view('admin.assessment.show');
    }

    /**
     * Menampilkan form untuk membuat assessment baru.
     * Route: /admin/assessment/create
     */
    public function create()
    {
        // Return view form create (nanti kita buat view ini)
        // return view('admin.assessment.create');
        
        // Sementara redirect dulu atau tampilkan teks
        return "Halaman Create Assessment (Belum dibuat)";
    }
}