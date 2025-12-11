@extends('layouts.teacher')

@section('title', 'Session Attendance')

@section('breadcrumb')
<nav class="flex items-center space-x-2 text-sm">
    <a href="{{ route('teacher.dashboard') }}" class="text-gray-600 hover:text-gray-900">Dashboard</a>
    <i class="fas fa-chevron-right text-xs text-gray-400"></i>
    <a href="{{ route('teacher.classes.index') }}" class="text-gray-600 hover:text-gray-900">Class</a>
    <i class="fas fa-chevron-right text-xs text-gray-400"></i>
    <a href="{{ route('teacher.classes.detail', $class->id) }}" class="text-gray-600 hover:text-gray-900">Detail</a>
    <i class="fas fa-chevron-right text-xs text-gray-400"></i>
    <span class="text-gray-900 font-medium">Attendance</span>
</nav>
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-3xl font-bold text-gray-800">
                    {{ \Carbon\Carbon::parse($session->date)->format('d F Y') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Recording attendance for <span class="font-semibold">{{ $class->name }}</span></p>
            </div>
            
            <div class="flex items-center bg-blue-50 px-4 py-2 rounded-lg border border-blue-100">
                <div class="mr-4 text-center">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Start Time</p>
                    <p class="text-lg font-bold text-blue-700">
                        {{ \Carbon\Carbon::parse($class->start_time)->format('H:i') }}
                    </p>
                </div>
                <div class="h-8 w-px bg-blue-200 mx-2"></div>
                <div class="ml-4 text-center">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">End Time</p>
                    <p class="text-lg font-bold text-blue-700">
                        {{ \Carbon\Carbon::parse($class->end_time)->format('H:i') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('teacher.classes.session.update', [$class->id, $session->id]) }}" method="POST">
        @csrf
        @method('PUT') <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">No</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Student No.</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Name</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Present</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Absent</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Permitted</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Sick</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Late</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($students as $index => $student)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm text-gray-800">{{ $index + 1 }}</td>
                            
                            {{-- MENAMPILKAN STUDENT NUMBER --}}
                            <td class="px-6 py-4 text-sm text-gray-800 font-mono">
                                {{ $student->student_number ?? '-' }}
                            </td>
                            
                            <td class="px-6 py-4 text-sm font-medium text-gray-800">
                                {{ $student->name }}
                            </td>

                            {{-- LOGIKA RADIO BUTTON --}}
                            {{-- Note: name="attendance[{{ $student->id }}]" --}}
                            
                            <td class="px-6 py-4 text-center">
                                <label class="inline-flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded-full transition">
                                    <input type="radio" name="attendance[{{ $student->id }}]" value="present" 
                                        {{ ($student->current_status == 'present' || $student->current_status == null) ? 'checked' : '' }}
                                        class="w-5 h-5 text-blue-600 focus:ring-blue-500 cursor-pointer">
                                </label>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <label class="inline-flex items-center cursor-pointer hover:bg-red-50 p-2 rounded-full transition">
                                    <input type="radio" name="attendance[{{ $student->id }}]" value="absent" 
                                        {{ $student->current_status == 'absent' ? 'checked' : '' }}
                                        class="w-5 h-5 text-red-500 focus:ring-red-500 cursor-pointer">
                                </label>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <label class="inline-flex items-center cursor-pointer hover:bg-yellow-50 p-2 rounded-full transition">
                                    <input type="radio" name="attendance[{{ $student->id }}]" value="permitted" 
                                        {{ $student->current_status == 'permitted' ? 'checked' : '' }}
                                        class="w-5 h-5 text-yellow-500 focus:ring-yellow-500 cursor-pointer">
                                </label>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <label class="inline-flex items-center cursor-pointer hover:bg-purple-50 p-2 rounded-full transition">
                                    <input type="radio" name="attendance[{{ $student->id }}]" value="sick" 
                                        {{ $student->current_status == 'sick' ? 'checked' : '' }}
                                        class="w-5 h-5 text-purple-500 focus:ring-purple-500 cursor-pointer">
                                </label>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <label class="inline-flex items-center cursor-pointer hover:bg-orange-50 p-2 rounded-full transition">
                                    <input type="radio" name="attendance[{{ $student->id }}]" value="late" 
                                        {{ $student->current_status == 'late' ? 'checked' : '' }}
                                        class="w-5 h-5 text-orange-500 focus:ring-orange-500 cursor-pointer">
                                </label>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                No active students found in this class.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end space-x-3">
                <a href="{{ route('teacher.classes.detail', $class->id) }}" class="bg-white border border-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-100 transition shadow-sm font-medium">
                    Cancel
                </a>
                
                <button type="submit" class="bg-blue-600 text-white px-8 py-2 rounded-lg hover:bg-blue-700 transition shadow-sm shadow-blue-200 font-medium">
                    Save Attendance
                </button>
            </div>
        </div>
    </form>
</div>
@endsection