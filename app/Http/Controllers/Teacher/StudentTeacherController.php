<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StudentTeacherController extends Controller
{
    public function index()
    {
        return view('teacher.students.index');
    }

    public function show($id)
    {
        return view('teacher.students.detail ', compact('id'));
    }

    public function marks()
    {
        return view('teacher.students.marks');
    }

    public function attendance()
    {
        return view('teacher.students.attendance');
    }
}
