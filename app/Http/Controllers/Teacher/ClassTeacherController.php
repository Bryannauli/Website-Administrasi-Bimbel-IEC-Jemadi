<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClassTeacherController extends Controller
{
    public function index()
    {
        return view('teacher.classes.index');
    }

    public function show($id)
    {
        return view('teacher.classes.show');
    }

    public function detail($id)
    {
        return view('teacher.classes.detail');
    }
}
