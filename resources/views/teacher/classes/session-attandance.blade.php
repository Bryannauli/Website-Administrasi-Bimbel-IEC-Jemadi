<!-- resources/views/teacher/classes/session-attendance.blade.php -->
@extends('layouts.teacher')

@section('title', 'Session Attendance')

@section('breadcrumb')
<nav class="flex items-center space-x-2 text-sm">
    <a href="{{ route('teacher.dashboard') }}" class="text-gray-600 hover:text-gray-900">Dashboard</a>
    <i class="fas fa-chevron-right text-xs text-gray-400"></i>
    <a href="{{ route('teacher.classes.index') }}" class="text-gray-600 hover:text-gray-900">Class</a>
    <i class="fas fa-chevron-right text-xs text-gray-400"></i>
    <span class="text-gray-900 font-medium">Attendance</span>
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

    <!-- Session Info Card -->
    <div class="bg-white rounded-lg p-6 shadow-sm">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Session No. 12</h3>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-gray-600 mb-1">Topic: <span class="font-medium text-gray-800">Verb</span></p>
                <p class="text-gray-600">Date: <span class="font-medium text-gray-800">19 November 2025</span></p>
            </div>
            <div class="text-right">
                <p class="text-gray-600 mb-1">Start Time: <span class="font-medium text-gray-800">17.00</span></p>
                <p class="text-gray-600">End Time: <span class="font-medium text-gray-800">18.00</span></p>
            </div>
        </div>
    </div>

    <!-- Attendance Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">No</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">ID</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Name</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Present</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Absent</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Permitted</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Sick</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Late</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @for($i = 1; $i <= 6; $i++)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm text-gray-800">{{ $i }}</td>
                        <td class="px-6 py-4 text-sm text-gray-800">07467</td>
                        <td class="px-6 py-4 text-sm text-gray-800">Lee Hanjin</td>
                        <td class="px-6 py-4 text-center">
                            <label class="inline-flex items-center">
                                <input type="radio" name="attendance_{{ $i }}" value="present" {{ $i == 1 ? 'checked' : '' }} class="w-5 h-5 text-blue-600 focus:ring-blue-500">
                            </label>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <label class="inline-flex items-center">
                                <input type="radio" name="attendance_{{ $i }}" value="absent" class="w-5 h-5 text-blue-600 focus:ring-blue-500">
                            </label>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <label class="inline-flex items-center">
                                <input type="radio" name="attendance_{{ $i }}" value="permitted" class="w-5 h-5 text-blue-600 focus:ring-blue-500">
                            </label>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <label class="inline-flex items-center">
                                <input type="radio" name="attendance_{{ $i }}" value="sick" class="w-5 h-5 text-blue-600 focus:ring-blue-500">
                            </label>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <label class="inline-flex items-center">
                                <input type="radio" name="attendance_{{ $i }}" value="late" class="w-5 h-5 text-blue-600 focus:ring-blue-500">
                            </label>
                        </td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>

        <!-- Submit Button -->
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
            <button type="submit" class="bg-blue-600 text-white px-8 py-2 rounded-lg hover:bg-blue-700 transition">
                Submit
            </button>
        </div>
    </div>
</div>
@endsection