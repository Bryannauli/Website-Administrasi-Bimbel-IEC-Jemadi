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

    <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-3xl font-bold text-gray-800">19 November 2025</h2>
                <p class="text-sm text-gray-500 mt-1">Record Attendance</p>
            </div>
            
            <div class="flex items-center bg-blue-50 px-4 py-2 rounded-lg border border-blue-100">
                <div class="mr-4 text-center">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Start Time</p>
                    <p class="text-lg font-bold text-blue-700">17:00</p>
                </div>
                <div class="h-8 w-px bg-blue-200 mx-2"></div>
                <div class="ml-4 text-center">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">End Time</p>
                    <p class="text-lg font-bold text-blue-700">18:00</p>
                </div>
            </div>
        </div>
    </div>

    <form action="#" method="POST"> @csrf
        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
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
                            <td class="px-6 py-4 text-sm font-medium text-gray-800">Lee Hanjin</td>
                            <td class="px-6 py-4 text-center">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="radio" name="attendance_{{ $i }}" value="present" {{ $i == 1 ? 'checked' : '' }} class="w-5 h-5 text-blue-600 focus:ring-blue-500 cursor-pointer">
                                </label>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="radio" name="attendance_{{ $i }}" value="absent" class="w-5 h-5 text-red-500 focus:ring-red-500 cursor-pointer">
                                </label>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="radio" name="attendance_{{ $i }}" value="permitted" class="w-5 h-5 text-yellow-500 focus:ring-yellow-500 cursor-pointer">
                                </label>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="radio" name="attendance_{{ $i }}" value="sick" class="w-5 h-5 text-purple-500 focus:ring-purple-500 cursor-pointer">
                                </label>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="radio" name="attendance_{{ $i }}" value="late" class="w-5 h-5 text-orange-500 focus:ring-orange-500 cursor-pointer">
                                </label>
                            </td>
                        </tr>
                        @endfor
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end space-x-3">
                <button type="button" class="bg-white border border-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-100 transition shadow-sm font-medium">
                    Save Draft
                </button>
                
                <button type="submit" class="bg-blue-600 text-white px-8 py-2 rounded-lg hover:bg-blue-700 transition shadow-sm shadow-blue-200 font-medium">
                    Submit
                </button>
            </div>
        </div>
    </form>
</div>
@endsection