<x-app-layout>
    <x-slot name="header"></x-slot>

    <div class="p-4 md:p-6 bg-[#EEF2FF] min-h-screen font-sans">

        {{-- Breadcrumb --}}
        <nav class="text-sm font-medium text-gray-400 mb-6 flex items-center gap-2 overflow-x-auto whitespace-nowrap">
            <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition">Dashboard</a>
            <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            <a href="{{ route('admin.classes.index') }}" class="hover:text-blue-600 transition">Classes</a>
            <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            <span class="text-gray-600 font-semibold">{{ $class->name }}</span>
        </nav>

        {{-- LAYOUT UTAMA --}}
        <div class="space-y-6">

            {{-- 1. INFO KELAS (CARD BESAR) --}}
            <div class="bg-white rounded-[2rem] shadow-sm p-0 flex overflow-hidden relative min-h-[180px]">
                <div class="w-2 md:w-3 bg-red-500 h-full absolute left-0 top-0"></div>
                <div class="p-6 md:p-8 w-full flex flex-col md:flex-row justify-between gap-4">
                    <div class="flex flex-col justify-center md:pl-4">
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 tracking-tight">{{ $class->name }}</h1>
                        <div class="mt-4 space-y-2">
                            <div class="flex items-center text-gray-400 text-sm font-medium">
                                <svg class="w-5 h-5 mr-3 text-orange-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span>{{ $class->start_month }} - {{ $class->end_month }} {{ $class->academic_year }}</span>
                            </div>
                            <div class="flex items-center text-gray-400 text-sm font-medium">
                                <svg class="w-5 h-5 mr-3 text-orange-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>
                                    @if($class->schedules->isNotEmpty()) {{ $class->schedules->pluck('day_of_week')->implode(', ') }} @endif
                                    ({{ \Carbon\Carbon::parse($class->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($class->end_time)->format('H:i') }})
                                </span>
                            </div>
                            <div class="flex items-center text-gray-400 text-sm font-medium">
                                <svg class="w-5 h-5 mr-3 text-orange-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                <span>{{ $class->classroom }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-row md:flex-col justify-between md:items-end gap-2">
                        <span class="px-5 py-1.5 font-semibold text-sm rounded-xl {{ $class->is_active ? 'bg-[#EEEBFF] text-[#5D5FEF]' : 'bg-gray-100 text-gray-500' }}">{{ $class->is_active ? 'Active' : 'Inactive' }}</span>
                        <span class="px-5 py-1.5 bg-blue-50 text-blue-600 font-semibold text-sm rounded-xl capitalize">{{ str_replace('_', ' ', $class->category) }}</span>
                    </div>
                </div>
            </div>

            {{-- 2. SECTION TENGAH: TABEL GURU & SISWA --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- === TABEL TEACHERS === --}}
                <div class="bg-white rounded-2xl shadow-sm p-5 border border-gray-100 flex flex-col h-full">
                    
                    {{-- Header Controls --}}
                    <div class="flex justify-between items-center mb-5">
                        <div class="flex items-center gap-2">
                            <h3 class="text-lg font-bold text-gray-800">Teachers</h3>
                            <span class="bg-blue-100 text-blue-600 text-xs font-bold px-2.5 py-0.5 rounded-full">
                                {{ ($class->form_teacher_id ? 1 : 0) + ($class->local_teacher_id ? 1 : 0) }}
                            </span>
                        </div>
                        <button class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold py-2 px-4 rounded-lg transition shadow-sm shadow-blue-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Add Teacher
                        </button>
                    </div>

                    {{-- Table --}}
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-gray-50 text-gray-400 text-xs font-medium border-b border-gray-100">
                                <tr>
                                    <th class="px-4 py-4 font-normal w-12">No</th>
                                    <th class="px-4 py-4 font-normal">Name</th>
                                    <th class="px-4 py-4 font-normal">Type</th>
                                    <th class="px-4 py-4 font-normal">Status</th>
                                    <th class="px-4 py-4 font-normal text-center w-32">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm text-gray-800">
                                @php $rowNo = 1; @endphp

                                {{-- Row 1: Form Teacher --}}
                                @if($class->formTeacher)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-4 text-gray-500">{{ $rowNo++ }}</td>
                                    <td class="px-4 py-4 font-medium text-gray-900">{{ $class->formTeacher->name }}</td>
                                    <td class="px-4 py-4 text-gray-500">Form Teacher</td>
                                    <td class="px-4 py-4">
                                        <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-lg text-xs font-semibold">Active</span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center justify-center gap-3">
                                            <button class="text-gray-400 hover:text-gray-600 transition" title="View"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></button>
                                            <button class="text-gray-400 hover:text-red-500 transition" title="Delete"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                                            <button class="text-gray-400 hover:text-blue-600 transition" title="Edit"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></button>
                                        </div>
                                    </td>
                                </tr>
                                @endif

                                {{-- Row 2: Local Teacher --}}
                                @if($class->localTeacher)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-4 text-gray-500">{{ $rowNo++ }}</td>
                                    <td class="px-4 py-4 font-medium text-gray-900">{{ $class->localTeacher->name }}</td>
                                    <td class="px-4 py-4 text-gray-500">Local Teacher</td>
                                    <td class="px-4 py-4">
                                        <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-lg text-xs font-semibold">Active</span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center justify-center gap-3">
                                            <button class="text-gray-400 hover:text-gray-600 transition" title="View"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></button>
                                            <button class="text-gray-400 hover:text-red-500 transition" title="Delete"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                                            <button class="text-gray-400 hover:text-blue-600 transition" title="Edit"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></button>
                                        </div>
                                    </td>
                                </tr>
                                @endif

                                @if(!$class->formTeacher && !$class->localTeacher)
                                    <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400 italic">No teachers assigned yet.</td></tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    {{-- TOMBOL ATTENDANCE REPORT TEACHER --}}
                    <div class="mt-auto pt-6 border-t border-gray-100">
                        <button class="w-full flex items-center justify-center gap-2 bg-[#F4F7FF] text-blue-700 font-semibold py-3 rounded-xl hover:bg-blue-100 transition text-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                            View Teacher Attendance Report
                        </button>
                    </div>
                </div>

                {{-- === TABEL STUDENTS === --}}
                <div class="bg-white rounded-2xl shadow-sm p-5 border border-gray-100 flex flex-col h-full">
                    
                    {{-- Header Controls --}}
                    <div class="flex justify-between items-center mb-5">
                        <div class="flex items-center gap-2">
                            <h3 class="text-lg font-bold text-gray-800">Students</h3>
                            <span class="bg-blue-100 text-blue-600 text-xs font-bold px-2.5 py-0.5 rounded-full">
                                {{ $class->students_count ?? 0 }}
                            </span>
                        </div>
                        <button class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold py-2 px-4 rounded-lg transition shadow-sm shadow-blue-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Add Student
                        </button>
                    </div>

                    {{-- Table --}}
                    <div class="overflow-x-auto max-h-[300px] overflow-y-auto pr-1 custom-scrollbar">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-gray-50 text-gray-400 text-xs font-medium border-b border-gray-100 sticky top-0 z-10">
                                <tr>
                                    <th class="px-4 py-4 font-normal w-12">No</th>
                                    <th class="px-4 py-4 font-normal">Name</th>
                                    <th class="px-4 py-4 font-normal">Status</th>
                                    <th class="px-4 py-4 font-normal text-center w-32">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm text-gray-800">
                                @forelse($class->students ?? [] as $index => $student)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-4 text-gray-500">{{ $index + 1 }}</td>
                                    <td class="px-4 py-4 font-medium text-gray-900">{{ $student->name }}</td>
                                    <td class="px-4 py-4">
                                        <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-lg text-xs font-semibold">Active</span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center justify-center gap-3">
                                            <button class="text-gray-400 hover:text-gray-600 transition" title="View"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></button>
                                            <button class="text-gray-400 hover:text-red-500 transition" title="Delete"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                                            <button class="text-gray-400 hover:text-blue-600 transition" title="Edit"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-gray-400 italic bg-gray-50 rounded-lg">
                                        No students enrolled in this class.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- TOMBOL ATTENDANCE REPORT STUDENT --}}
                    <div class="mt-auto pt-6 border-t border-gray-100">
                        <button class="w-full flex items-center justify-center gap-2 bg-[#F4F7FF] text-blue-700 font-semibold py-3 rounded-xl hover:bg-blue-100 transition text-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                            View Student Attendance Report
                        </button>
                    </div>
                </div>

            </div>

            {{-- 3. SECTION BAWAH: PROGRESS CLASS (TETAP SAMA) --}}
            <div class="bg-white rounded-[2rem] shadow-sm p-6 md:p-8">
                <h3 class="text-xl font-bold text-gray-800 mb-6">Class Progress</h3>
                
                <div class="flex flex-col md:flex-row items-center justify-center md:justify-start gap-8 md:gap-12">
                    <div class="relative w-40 h-40 flex-shrink-0">
                        @php
                            $completed = $class->completed_sessions ?? 0;
                            $total = $class->total_sessions ?? 20; 
                            $percent = $total > 0 ? round(($completed / $total) * 100) : 0;
                            $circumference = 2 * 3.14159 * 58;
                            $strokeDashoffset = $circumference - ($percent / 100) * $circumference;
                        @endphp
                        <svg class="w-full h-full transform -rotate-90">
                            <circle cx="80" cy="80" r="58" stroke="#E5E7EB" stroke-width="16" fill="transparent"></circle>
                            <circle cx="80" cy="80" r="58" 
                                stroke="#3B82F6" 
                                stroke-width="16" 
                                fill="transparent"
                                stroke-dasharray="{{ $circumference }}" 
                                stroke-dashoffset="{{ $strokeDashoffset }}" 
                                stroke-linecap="round">
                            </circle>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-2xl font-bold text-blue-600">{{ $percent }}%</span>
                        </div>
                    </div>

                    <div class="flex flex-col space-y-4 w-full md:w-auto">
                        <div class="flex items-center">
                            <span class="w-3 h-3 rounded-full bg-blue-500 mr-2"></span>
                            <span class="text-blue-600 font-bold text-lg">{{ $percent }}%</span>
                            <span class="text-gray-500 ml-2 text-sm font-medium">Sessions Completed</span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-3 h-3 rounded-full bg-gray-200 mr-2"></span>
                            <span class="text-gray-400 font-medium text-sm">Remaining Sessions</span>
                        </div>
                        <div class="mt-2 pt-2 md:pt-0">
                            <span class="bg-[#EEEBFF] text-[#5D5FEF] px-4 py-2 rounded-xl text-sm font-bold block md:inline-block text-center md:text-left">
                                {{ $completed }}/{{ $total }}
                                <span class="font-normal text-gray-500 ml-1">Completed Sessions</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>