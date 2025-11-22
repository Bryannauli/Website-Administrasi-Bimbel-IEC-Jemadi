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


}