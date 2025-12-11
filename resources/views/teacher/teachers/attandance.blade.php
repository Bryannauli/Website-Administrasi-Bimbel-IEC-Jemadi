<!-- resources/views/teacher/teachers/attendance.blade.php -->
@extends('layouts.teacher')

@section('title', 'Teachers Attendance Record')

@section('breadcrumb')
<nav class="flex items-center space-x-2 text-sm">
    <a href="{{ route('teacher.dashboard') }}" class="text-gray-600 hover:text-gray-900">Home</a>
    <i class="fas fa-chevron-right text-xs text-gray-400"></i>
    <a href="{{ route('teacher.teachers.index') }}" class="text-gray-600 hover:text-gray-900">Teachers</a>
    <i class="fas fa-chevron-right text-xs text-gray-400"></i>
    <span class="text-gray-900 font-medium">Teachers Attendance Record</span>
</nav>
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Class Info Card -->
    <div class="border-l-4 border-red-500 bg-white rounded-lg p-6 shadow-sm">
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Vocabulary</h2>
                <div class="space-y-1 text-sm text-gray-600">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-calendar text-gray-400 w-4"></i>
                        <span>March 20, 2021</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-clock text-gray-400 w-4"></i>
                        <span>08.00 - 09.00 AM</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-door-open text-gray-400 w-4"></i>
                        <span>E-101</span>
                    </div>
                </div>
            </div>
            <div class="flex flex-col space-y-2">
                <span class="bg-green-100 text-green-600 px-4 py-1 rounded-full text-sm font-medium text-center">Present</span>
                <span class="bg-blue-100 text-blue-600 px-4 py-1 rounded-full text-sm font-medium text-center">Pre-Level</span>
            </div>
        </div>
    </div>

    <!-- Filter Session -->
    <div class="bg-white rounded-lg p-4 shadow-sm">
        <div class="relative inline-block">
            <select class="px-4 py-2 border border-gray-300 rounded-lg pr-10 focus:outline-none focus:ring-2 focus:ring-blue-500 appearance-none">
                <option>Session No.1</option>
                <option>Session No.2</option>
                <option>Session No.3</option>
            </select>
            <i class="fas fa-chevron-down absolute right-3 top-3 text-gray-400 pointer-events-none"></i>
        </div>
    </div>

    <!-- Attendance Records Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">No</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Session No.</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Student Number</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Name</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm text-gray-800">1</td>
                        <td class="px-6 py-4 text-sm text-gray-800">1</td>
                        <td class="px-6 py-4 text-sm text-gray-600">2025-10-12</td>
                        <td class="px-6 py-4 text-sm text-gray-800">1213</td>
                        <td class="px-6 py-4 text-sm text-gray-800">Kim Minju</td>
                        <td class="px-6 py-4">
                            <span class="bg-blue-100 text-blue-600 px-3 py-1 rounded-full text-xs font-medium">Present</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button class="text-gray-600 hover:text-blue-600">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm text-gray-800">2</td>
                        <td class="px-6 py-4 text-sm text-gray-800">1</td>
                        <td class="px-6 py-4 text-sm text-gray-600">2025-10-11</td>
                        <td class="px-6 py-4 text-sm text-gray-800">1213</td>
                        <td class="px-6 py-4 text-sm text-gray-800">Lee Sangwon</td>
                        <td class="px-6 py-4">
                            <span class="bg-red-100 text-red-600 px-3 py-1 rounded-full text-xs font-medium">Absent</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button class="text-gray-600 hover:text-blue-600">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
            <button class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50">
                Previous
            </button>
            <span class="text-sm text-gray-600">Page 1 of 1</span>
            <button class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50">
                Next
            </button>
        </div>
    </div>
</div>
@endsection