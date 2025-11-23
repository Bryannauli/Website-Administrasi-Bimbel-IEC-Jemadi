<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ClassController extends Controller
{
    public function index(Request $request)
    {
        // Data Dummy sesuai gambar
        $dummyClasses = collect([
            (object)[
                'id' => 1,
                'name' => 'Vocabulary',
                'start_month' => '7 November 2025',
                'end_month' => '30 December 2025',
                'academic_year' => '2025/2026',
                'status' => 'active'
            ],
            (object)[
                'id' => 2,
                'name' => 'Speaking',
                'start_month' => '7 November 2025',
                'end_month' => '30 December 2025',
                'academic_year' => '2025/2026',
                'status' => 'active'
            ],
            (object)[
                'id' => 3,
                'name' => 'Grammar',
                'start_month' => '10 January 2026',
                'end_month' => '20 March 2026',
                'academic_year' => '2025/2026',
                'status' => 'inactive'
            ],
            (object)[
                'id' => 4,
                'name' => 'Listening',
                'start_month' => '7 November 2025',
                'end_month' => '30 December 2025',
                'academic_year' => '2025/2026',
                'status' => 'active'
            ],
        ]);

        // Logika Search Sederhana
        if ($request->has('search') && $request->search != '') {
            $searchTerm = strtolower($request->search);
            $dummyClasses = $dummyClasses->filter(function ($item) use ($searchTerm) {
                return str_contains(strtolower($item->name), $searchTerm);
            });
        }

        // Pagination Manual
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 5;
        $currentItems = $dummyClasses->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $classes = new LengthAwarePaginator($currentItems, count($dummyClasses), $perPage);
        $classes->setPath($request->url());

        return view('admin.classes.index', compact('classes'));
    }

    public function create()
    {
        return view('admin.classes.create'); // Nanti dibuat
    }

public function update(Request $request, $id)
{
    $class = Classes::findOrFail($id);

    $class->update($request->all());

    return back()->with('success', 'Class updated!');
}
 public function class($id)
    {
        // Data Dummy sesuai screenshot
        $schedules = collect([
            (object)[
                'id' => 1,
                'class_name' => 'Vocabulary',
                'days' => 'Monday & Tuesday',
                'time' => '16.00 - 17.30',
                'teacher' => 'Kim Geonwoo',
                'room' => 'E-101',
                'category' => 'Pre-Level',
                'status' => 'Active'
            ],
            (object)[
                'id' => 2,
                'class_name' => 'Vocabulary',
                'days' => 'Wednesday',
                'time' => '14.00 - 15.30',
                'teacher' => 'Lee Sangwon',
                'room' => 'E-102',
                'category' => 'Level',
                'status' => 'Active'
            ],
            (object)[
                'id' => 3,
                'class_name' => 'Vocabulary',
                'days' => 'Wednesday',
                'time' => '14.00 - 15.30',
                'teacher' => 'Lee Sangwon',
                'room' => 'E-102',
                'category' => 'Step',
                'status' => 'Active'
            ],
            (object)[
                'id' => 4,
                'class_name' => 'Vocabulary',
                'days' => 'Wednesday',
                'time' => '14.00 - 15.30',
                'teacher' => 'Lee Sangwon',
                'room' => 'E-102',
                'category' => 'Private',
                'status' => 'Active'
            ],
        ]);

        // Pagination Manual
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 5;
        $currentItems = $schedules->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $paginatedSchedules = new LengthAwarePaginator($currentItems, count($schedules), $perPage);
        $paginatedSchedules->setPath(request()->url());

        return view('admin.classes.class', compact('paginatedSchedules'));
    }

public function detailClass($id)
    {
        // Data Dummy untuk tampilan Detail (Sesuai gambar image_c09202.png)
        $class = (object)[
            'id' => $id,
            'name' => 'Vocabulary',
            'category' => 'English',
            'date' => 'March 20, 2021',
            'time' => '08.00 - 09.00 AM',
            'room' => 'E-101',
            'status' => 'Active',
            'level' => 'Pre-Level',
            'students_count' => 10,
            'teachers_count' => 2,
            'progress_percent' => 60,
            'completed_sessions' => 11,
            'total_sessions' => 20
        ];

        return view('admin.classes.detailclass', compact('class'));
    }
     public function students($id)
    {
        // 1. Data Dummy Kelas (Header)
        $class = (object)[
            'id' => $id,
            'name' => 'Vocabulary',
            'category' => 'English',
            'date' => 'March 20, 2021',
            'time' => '08.00 - 09.00 AM',
            'room' => 'E-101',
            'status' => 'Active',
            'level' => 'Pre-Level',
        ];

        // 2. Data Dummy Siswa (Untuk Tabel)
        $allStudents = collect([
            (object)['id' => 1, 'number' => '1', 'name' => 'Sanghyeon', 'gender' => 'Male', 'status' => 'Active'],
            (object)['id' => 2, 'number' => '2', 'name' => 'Kim Minju', 'gender' => 'Female', 'status' => 'Active'],
            (object)['id' => 3, 'number' => '3', 'name' => 'Park Ji-hyo', 'gender' => 'Female', 'status' => 'Active'],
            (object)['id' => 4, 'number' => '4', 'name' => 'Lee Know', 'gender' => 'Male', 'status' => 'Inactive'],
            (object)['id' => 5, 'number' => '5', 'name' => 'Hanni Pham', 'gender' => 'Female', 'status' => 'Active'],
        ]);

        // Pagination Manual untuk Siswa
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 5;
        $currentItems = $allStudents->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $students = new LengthAwarePaginator($currentItems, count($allStudents), $perPage);
        $students->setPath(request()->url());

        return view('admin.classes.students', compact('class', 'students'));
    }

}