<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function index()
    {
        return view('teacher.teachers.index');
    }

    public function show($id)
    {
        return view('teacher.teachers.attendance', compact('id'));
    }

    public function attendance()
    {
        return view('teacher.teachers.attendance');
    }

    public function classAttendance($classId)
    {
        return view('teacher.teachers.attendance-class', compact('classId'));
    }
}
