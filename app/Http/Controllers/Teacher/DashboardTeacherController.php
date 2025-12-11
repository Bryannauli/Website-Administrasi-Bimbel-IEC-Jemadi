<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardTeacherController extends Controller
{
     public function index()
    {
        return view('teacher.dashboard');
    }

    public function analytics()
    {
        return view('teacher.analytics');
    }

    public function mySchedule()
    {
        return view('teacher.schedule');
    }
}
