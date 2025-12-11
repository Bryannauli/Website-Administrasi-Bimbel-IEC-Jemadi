<x-app-layout>
    <x-slot name="header"></x-slot>

    <div class="p-4 md:p-6 bg-[#EEF2FF] min-h-screen font-sans">

        {{-- BREADCRUMB (CONSISTENT STYLE WITH STUDENT PAGE) --}}
        <nav class="flex mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                
                {{-- 1. Dashboard --}}
                <li class="inline-flex items-center">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-blue-600">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                        Dashboard
                    </a>
                </li>

                {{-- 2. Classes List --}}
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <a href="{{ route('admin.classes.index') }}" class="ml-1 text-sm font-medium text-gray-500 hover:text-blue-600 md:ml-2">Classes</a>
                    </div>
                </li>

                {{-- 3. Current Class Name --}}
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="ml-1 text-sm font-medium text-gray-900 md:ml-2">{{ $class->name }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="space-y-8">

            {{-- 1. INFO KELAS (TOP CARD) --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 relative overflow-hidden z-0">
                <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-bl-full -mr-8 -mt-8"></div>
                <div class="flex flex-col md:flex-row items-center justify-between relative gap-6">
                    <div class="flex items-center gap-5">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($class->name) }}&background=2563EB&color=fff&size=128&bold=true&length=2"
                            alt="{{ $class->name }}" 
                            class="w-16 h-16 md:w-20 md:h-20 rounded-2xl shadow-md border-4 border-white bg-blue-600">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ $class->name }}</h1>
                            <div class="flex flex-col gap-1 mt-1 mb-2">
                                <div class="flex items-center gap-2 text-sm text-gray-600">
                                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span class="font-semibold text-gray-800">
                                        {{ \Carbon\Carbon::parse($class->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($class->end_time)->format('H:i') }}
                                    </span>
                                    <span class="text-gray-300">|</span>
                                    <span>{{ $class->schedules->pluck('day_of_week')->implode(', ') }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-sm text-gray-500">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                    <span>{{ $class->classroom ?? 'No Classroom' }}</span>
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="px-2.5 py-0.5 {{ $class->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600' }} rounded text-xs font-bold uppercase tracking-wider">
                                    {{ $class->is_active ? 'Active' : 'Inactive' }}
                                </span>
                                <span class="px-2.5 py-0.5 bg-blue-50 text-blue-700 rounded text-xs font-bold uppercase tracking-wider border border-blue-100">
                                    {{ ucwords(str_replace('_', ' ', $class->category)) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="text-center md:text-right hidden md:block">
                        <p class="text-xs text-gray-400 uppercase font-bold tracking-wide mb-1">Academic Year</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $class->academic_year }}</p>
                        <p class="text-sm text-gray-500 font-medium bg-gray-50 px-2 py-1 rounded inline-block mt-1">
                            {{ $class->start_month }} - {{ $class->end_month }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- 2. SECTION GURU (Row 1) --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- A. LIST TEACHER --}}
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                            Teachers Assigned
                        </h3>
                        <span class="text-xs text-gray-400">(Max 2 Slots)</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Slot 1: Form Teacher --}}
                        @if($class->formTeacher)
                            <div class="p-3 rounded-xl border border-blue-100 bg-blue-50/20 flex items-center justify-between group hover:border-blue-300 transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-sm shadow-sm">
                                        {{ substr($class->formTeacher->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-800 text-sm">{{ $class->formTeacher->name }}</h4>
                                        <p class="text-[10px] text-blue-600 font-bold uppercase tracking-wider">Form Teacher</p>
                                    </div>
                                </div>
                                <div class="flex gap-1">
                                    <button class="p-1.5 text-gray-400 hover:text-blue-600 bg-white rounded shadow-sm border border-gray-100" title="Edit"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></button>
                                    <button class="p-1.5 text-gray-400 hover:text-red-600 bg-white rounded shadow-sm border border-gray-100" title="Unassign"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                                </div>
                            </div>
                        @else
                            <button class="w-full p-3 h-[66px] rounded-xl border border-dashed border-gray-300 bg-gray-50 flex items-center justify-center gap-2 text-gray-400 hover:border-blue-400 hover:text-blue-600 hover:bg-blue-50 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                <span class="text-xs font-bold">Assign Form Teacher</span>
                            </button>
                        @endif

                        {{-- Slot 2: Local Teacher --}}
                        @if($class->localTeacher)
                            <div class="p-3 rounded-xl border border-purple-100 bg-purple-50/20 flex items-center justify-between group hover:border-purple-300 transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 font-bold text-sm shadow-sm">
                                        {{ substr($class->localTeacher->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-800 text-sm">{{ $class->localTeacher->name }}</h4>
                                        <p class="text-[10px] text-purple-600 font-bold uppercase tracking-wider">Local Teacher</p>
                                    </div>
                                </div>
                                <div class="flex gap-1">
                                    <button class="p-1.5 text-gray-400 hover:text-purple-600 bg-white rounded shadow-sm border border-gray-100" title="Edit"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></button>
                                    <button class="p-1.5 text-gray-400 hover:text-red-600 bg-white rounded shadow-sm border border-gray-100" title="Unassign"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                                </div>
                            </div>
                        @else
                            <button class="w-full p-3 h-[66px] rounded-xl border border-dashed border-gray-300 bg-gray-50 flex items-center justify-center gap-2 text-gray-400 hover:border-purple-400 hover:text-purple-600 hover:bg-purple-50 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                <span class="text-xs font-bold">Assign Local Teacher</span>
                            </button>
                        @endif
                    </div>
                </div>

                {{-- B. ATTENDANCE TEACHER --}}
                <div class="lg:col-span-1 bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl shadow-lg shadow-blue-200 p-6 text-white flex flex-col justify-between">
                    <div>
                        <h4 class="text-blue-100 text-xs font-bold uppercase tracking-widest mb-2">Attendance</h4>
                        <h3 class="text-xl font-bold">Teacher Report</h3>
                        <p class="text-blue-100 text-xs mt-2 opacity-80 leading-relaxed">
                            Monitor teaching logs, materials taught, and teacher presence for this class.
                        </p>
                    </div>
                    <button class="mt-4 w-full py-2.5 bg-white text-blue-700 rounded-lg text-sm font-bold hover:bg-blue-50 transition shadow-sm flex items-center justify-center gap-2">
                        View Report <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </div>

            </div>

            {{-- 3. SECTION SISWA (Row 2) --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- C. LIST STUDENTS --}}
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm p-6 border border-gray-100 flex flex-col min-h-[400px]">
                    
                    {{-- Header --}}
                    <div class="flex justify-between items-center mb-5">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            Enrolled Students
                            <span class="bg-gray-100 text-gray-600 text-xs font-bold px-2 py-0.5 rounded-full ml-2">
                                {{ $class->students_count ?? 0 }}
                            </span>
                        </h3>
                        <button class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold py-2 px-3 rounded-lg transition shadow-sm shadow-green-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Add Student
                        </button>
                    </div>

                    {{-- Table --}}
                    <div class="overflow-x-auto flex-1 custom-scrollbar">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-gray-50 text-gray-400 text-xs font-medium border-b border-gray-100 sticky top-0 z-10">
                                <tr>
                                    <th class="px-4 py-3 font-normal w-12">No</th>
                                    <th class="px-4 py-3 font-normal">Student ID</th>
                                    <th class="px-4 py-3 font-normal">Name</th>
                                    <th class="px-4 py-3 font-normal">Status</th>
                                    <th class="px-4 py-3 font-normal text-center w-24">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm text-gray-800">
                                @forelse($class->students ?? [] as $index => $student)
                                <tr class="hover:bg-gray-50 transition group">
                                    <td class="px-4 py-3 text-gray-400 text-xs">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $student->student_number }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-900 group-hover:text-blue-600 transition-colors">{{ $student->name }}</td>
                                    <td class="px-4 py-3">
                                        {{-- STATUS (Standard Pill) --}}
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 border border-green-200">
                                            Active
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        {{-- ACTION BUTTONS (HAPUS 'opacity-0' AGAR SELALU MUNCUL) --}}
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('admin.student.detail', ['id' => $student->id, 'ref' => 'class', 'class_id' => $class->id]) }}" 
                                            class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded" title="View Profile">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            </a>
                                            <button class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded" title="Remove from Class">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-12 text-center text-gray-400 italic bg-gray-50 rounded-lg border border-dashed border-gray-200">
                                        No students enrolled in this class yet.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- D. ATTENDANCE STUDENT --}}
                <div class="lg:col-span-1 bg-white rounded-2xl shadow-sm p-6 border border-gray-100 flex flex-col">
                    <div class="mb-4">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Attendance</h4>
                        <h3 class="text-xl font-bold text-gray-800">Student Stats</h3>
                    </div>

                    {{-- Placeholder Chart/Info --}}
                    <div class="flex-1 flex flex-col items-center justify-center text-center p-6 bg-gray-50 rounded-xl border border-dashed border-gray-200 mb-4">
                        <div class="w-12 h-12 rounded-full bg-green-100 text-green-600 flex items-center justify-center mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        </div>
                        <p class="text-sm text-gray-600 font-medium">Track daily presence & absence.</p>
                        <p class="text-xs text-gray-400 mt-1">Detailed report available.</p>
                    </div>

                    <button class="w-full py-2.5 bg-green-600 text-white rounded-lg text-sm font-bold hover:bg-green-700 transition shadow-sm shadow-green-200 flex items-center justify-center gap-2">
                        View Report <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </button>
                </div>

            </div>
            
        </div>
    </div>
</x-app-layout>