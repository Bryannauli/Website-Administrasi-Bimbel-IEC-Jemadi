<x-app-layout>
    <x-slot name="header"></x-slot>

    {{-- WRAPPER UTAMA --}}
    <div class="p-4 md:p-6 bg-[#EEF2FF] min-h-screen font-sans" x-data="{ 
        // State Modal
        showAddStudentModal: {{ request('search_student') ? 'true' : 'false' }},
        showHistoryModal: false,
        showStudentStatsModal: false,
        
        // Fungsi Confirm Remove
        confirmRemove(studentName, formId) {
            Swal.fire({
                title: 'Remove Student?',
                text: `Are you sure you want to remove ${studentName} from this class? They will become Unassigned.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, Remove'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }
    }">

        {{-- BREADCRUMB --}}
        <nav class="flex mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-blue-600">Dashboard</a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <a href="{{ route('admin.classes.index') }}" class="ml-1 text-sm font-medium text-gray-500 hover:text-blue-600 md:ml-2">Classes</a>
                    </div>
                </li>
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
                                    <span class="font-semibold text-gray-800">
                                        {{ \Carbon\Carbon::parse($class->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($class->end_time)->format('H:i') }}
                                    </span>
                                    <span class="text-gray-300">|</span>
                                    <span>
                                        @if($class->schedules->isNotEmpty())
                                            {{ $class->schedules->pluck('day_of_week')->implode(', ') }}
                                        @else
                                            No Schedule
                                        @endif
                                    </span>
                                </div>
                                <div class="flex items-center gap-2 text-sm text-gray-500">
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

            {{-- 2. ROW 1: TEACHER & TEACHER ATTENDANCE --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- A. LIST TEACHER --}}
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            Teachers Assigned
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Form Teacher --}}
                        @if($class->formTeacher)
                            <div class="p-3 rounded-xl border border-blue-100 bg-blue-50/20 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-sm shadow-sm">
                                        {{ substr($class->formTeacher->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-800 text-sm">{{ $class->formTeacher->name }}</h4>
                                        <p class="text-[10px] text-blue-600 font-bold uppercase tracking-wider">Form Teacher</p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="w-full p-3 rounded-xl border border-dashed border-gray-300 bg-gray-50 flex items-center justify-center gap-2 text-gray-400">
                                <span class="text-xs font-medium italic">No Form Teacher Assigned</span>
                            </div>
                        @endif

                        {{-- Local Teacher --}}
                        @if($class->localTeacher)
                            <div class="p-3 rounded-xl border border-purple-100 bg-purple-50/20 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 font-bold text-sm shadow-sm">
                                        {{ substr($class->localTeacher->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-800 text-sm">{{ $class->localTeacher->name }}</h4>
                                        <p class="text-[10px] text-purple-600 font-bold uppercase tracking-wider">Local Teacher</p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="w-full p-3 rounded-xl border border-dashed border-gray-300 bg-gray-50 flex items-center justify-center gap-2 text-gray-400">
                                <span class="text-xs font-medium italic">No Local Teacher Assigned</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- B. TEACHER ATTENDANCE (BLUE CARD) --}}
                <div class="lg:col-span-1 bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl shadow-lg shadow-blue-200 p-6 text-white flex flex-col justify-between relative overflow-hidden">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white opacity-10 rounded-full blur-xl"></div>
                    <div>
                        <div class="flex justify-between items-start">
                            <h4 class="text-blue-100 text-xs font-bold uppercase tracking-widest mb-2">Latest Activity</h4>
                            @if($lastSession)
                                <span class="bg-blue-800 bg-opacity-50 text-xs px-2 py-1 rounded border border-blue-400 border-opacity-30">
                                    {{ \Carbon\Carbon::parse($lastSession->date)->format('d M') }}
                                </span>
                            @endif
                        </div>

                        @php
                            $lastTeacherRecord = $lastSession ? $lastSession->teacherRecords->first() : null;
                        @endphp

                        @if($lastTeacherRecord && $lastTeacherRecord->teacher)
                            <h3 class="text-lg font-bold truncate" title="{{ $lastTeacherRecord->teacher->name }}">
                                {{ $lastTeacherRecord->teacher->name }}
                            </h3>
                            <div class="mt-3 bg-blue-800 bg-opacity-40 p-3 rounded-lg border border-blue-500 border-opacity-30">
                                <p class="text-blue-50 text-xs italic line-clamp-3">
                                    "{{ $lastTeacherRecord->comment ?? 'No teaching notes provided.' }}"
                                </p>
                            </div>
                        @else
                            <h3 class="text-xl font-bold">No Data Yet</h3>
                            <p class="text-blue-100 text-xs mt-2 opacity-80 leading-relaxed">
                                Teaching logs will appear here once attendance is submitted.
                            </p>
                        @endif
                    </div>

                    <button @click="showHistoryModal = true" 
                            class="mt-4 w-full py-2.5 bg-white text-blue-700 rounded-lg text-sm font-bold hover:bg-blue-50 transition shadow-sm flex items-center justify-center gap-2">
                        View Teaching Logs <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </div>
            </div>

            {{-- 3. ROW 2: STUDENTS & STUDENT ATTENDANCE --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- C. LIST STUDENTS TABLE --}}
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm p-6 border border-gray-100 flex flex-col min-h-[400px]">
                    <div class="flex justify-between items-center mb-5">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            Enrolled Students
                            <span class="bg-gray-100 text-gray-600 text-xs font-bold px-2 py-0.5 rounded-full ml-2">
                                {{ $class->students_count ?? 0 }}
                            </span>
                        </h3>
                        
                        <button @click="showAddStudentModal = true" class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold py-2 px-3 rounded-lg transition shadow-sm shadow-green-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Add Student
                        </button>
                    </div>

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
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 border border-green-200">
                                            Active
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('admin.student.detail', ['id' => $student->id, 'ref' => 'class', 'class_id' => $class->id]) }}" 
                                            class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded" title="View Profile">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            </a>
                                            <form id="remove-student-{{ $student->id }}" action="{{ route('admin.classes.unassignStudent', $student->id) }}" method="POST" style="display: none;">
                                                @csrf @method('PATCH')
                                            </form>
                                            <button @click="confirmRemove('{{ addslashes($student->name) }}', 'remove-student-{{ $student->id }}')" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded" title="Remove from Class">
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

                {{-- D. STUDENT ATTENDANCE (WHITE CARD - MINI TABLE) --}}
                <div class="lg:col-span-1 bg-white rounded-2xl shadow-sm p-6 border border-gray-100 flex flex-col h-full">
                    
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Attendance</h4>
                            <h3 class="text-xl font-bold text-gray-800">Last Session</h3>
                        </div>
                        @if($lastSession)
                            <div class="text-right">
                                @php
                                    $pCount = $lastSession->records->whereIn('status', ['present', 'late'])->count();
                                    $tCount = $lastSession->records->count();
                                    $sPerc = $tCount > 0 ? round(($pCount / $tCount) * 100) : 0;
                                @endphp
                                <span class="text-2xl font-bold {{ $sPerc >= 80 ? 'text-green-600' : ($sPerc >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ $sPerc }}%
                                </span>
                                <p class="text-[10px] text-gray-400">Present</p>
                            </div>
                        @endif
                    </div>

                    <div class="flex-1 overflow-y-auto custom-scrollbar pr-1 mb-4 max-h-[300px]">
                        @if($lastSession && $lastSession->records->count() > 0)
                            @php
                                $absentees = $lastSession->records->whereIn('status', ['absent', 'sick', 'permission']);
                            @endphp

                            @if($absentees->count() > 0)
                                <p class="text-xs font-bold text-red-500 mb-2 uppercase tracking-wide">Absentees List:</p>
                                <ul class="space-y-2">
                                @foreach($absentees as $record)
                                    <li class="flex items-center justify-between p-2 bg-gray-50 rounded-lg border border-gray-100">
                                        <span class="text-sm font-medium text-gray-700 truncate w-32" title="{{ $record->student->name ?? '-' }}">
                                            {{ $record->student->name ?? '-' }}
                                        </span>
                                        
                                        {{-- Badge Status Absen (Updated Colors) --}}
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded uppercase
                                            {{ $record->status == 'absent' ? 'bg-red-100 text-red-700 border border-red-200' : '' }}
                                            {{ $record->status == 'sick' ? 'bg-purple-100 text-purple-700 border border-purple-200' : '' }}
                                            {{ $record->status == 'permission' ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : '' }}">
                                            {{ $record->status }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                            @else
                                <div class="h-full flex flex-col items-center justify-center text-center py-6 bg-green-50 rounded-xl border border-dashed border-green-200">
                                    <div class="w-10 h-10 rounded-full bg-green-100 text-green-600 flex items-center justify-center mb-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <p class="text-sm font-bold text-green-700">Perfect Attendance!</p>
                                    <p class="text-xs text-green-600">All students present.</p>
                                </div>
                            @endif
                        @else
                            <div class="h-full flex flex-col items-center justify-center text-center p-4 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                                <p class="text-sm text-gray-500">No attendance data yet.</p>
                            </div>
                        @endif
                    </div>

                    <button @click="showStudentStatsModal = true" class="w-full py-2.5 bg-green-600 text-white rounded-lg text-sm font-bold hover:bg-green-700 transition shadow-sm shadow-green-200 flex items-center justify-center gap-2">
                        View Full Report <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- MODAL 1: ADD STUDENT --}}
        <div x-show="showAddStudentModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="showAddStudentModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full" x-data="{ selectedStudents: [] }">
                    {{-- Search Form --}}
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 border-b border-gray-100">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg leading-6 font-bold text-gray-900">Enroll Students</h3>
                            <button @click="showAddStudentModal = false" class="text-gray-400 hover:text-gray-500"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                        </div>
                        <form action="{{ route('admin.classes.detailclass', $class->id) }}" method="GET" class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg></div>
                            <input type="text" name="search_student" value="{{ request('search_student') }}" class="block w-full pl-10 pr-20 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:bg-white focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Search Name/ID & Enter...">
                            @if(request('search_student')) <a href="{{ route('admin.classes.detailclass', $class->id) }}" class="absolute inset-y-0 right-0 pr-3 flex items-center text-xs text-red-500 font-bold hover:underline">CLEAR</a> @endif
                        </form>
                    </div>
                    {{-- Enroll Form --}}
                    <form action="{{ route('admin.classes.assignStudent', $class->id) }}" method="POST">
                        @csrf
                        <div class="px-6 py-2 max-h-64 overflow-y-auto custom-scrollbar bg-gray-50">
                            @if($availableStudents->isEmpty())
                                <div class="py-8 text-center text-gray-500 flex flex-col items-center"><p class="text-sm">No available students found.</p></div>
                            @else
                                <ul class="divide-y divide-gray-100">
                                    @foreach($availableStudents as $student)
                                        <li class="py-3 flex items-center hover:bg-white -mx-2 px-2 rounded-lg transition cursor-pointer" @click="if(selectedStudents.includes('{{ $student->id }}')) selectedStudents = selectedStudents.filter(id => id !== '{{ $student->id }}'); else selectedStudents.push('{{ $student->id }}');">
                                            <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" x-model="selectedStudents" class="hidden">
                                            <div class="flex-shrink-0 h-5 w-5 rounded border flex items-center justify-center transition-colors" :class="selectedStudents.includes('{{ $student->id }}') ? 'bg-blue-600 border-blue-600' : 'border-gray-300 bg-white'">
                                                <svg x-show="selectedStudents.includes('{{ $student->id }}')" class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                            </div>
                                            <div class="ml-3"><p class="text-sm font-medium text-gray-900">{{ $student->name }}</p><p class="text-xs text-gray-500 font-mono">{{ $student->student_number }}</p></div>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                        <div class="bg-white px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100">
                            <button type="submit" x-bind:disabled="selectedStudents.length === 0" :class="selectedStudents.length === 0 ? 'opacity-50 cursor-not-allowed' : ''" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition-all">Enroll Selected (<span x-text="selectedStudents.length"></span>)</button>
                            <button type="button" @click="showAddStudentModal = false" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- MODAL 2: TEACHING HISTORY --}}
        <div x-show="showHistoryModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="showHistoryModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 border-b border-gray-100 flex justify-between items-center">
                        <div><h3 class="text-lg leading-6 font-bold text-gray-900">Teaching Logs</h3><p class="text-sm text-gray-500 mt-1">Activity history & attendance.</p></div>
                        <button @click="showHistoryModal = false" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                    </div>
                    <div class="max-h-[60vh] overflow-y-auto custom-scrollbar bg-gray-50 p-6">
                        @if($teachingLogs->isEmpty())
                            <div class="text-center py-10 text-gray-500">No logs found.</div>
                        @else
                            <div class="space-y-4">
                                @foreach($teachingLogs as $log)
                                    @php $mainTR = $log->teacherRecords->first(); @endphp
                                    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                                        <div class="flex justify-between items-start mb-2">
                                            <div class="flex items-center gap-3">
                                                <div class="bg-blue-50 text-blue-700 font-bold px-3 py-1.5 rounded-lg text-center"><span class="block text-xs uppercase">{{ \Carbon\Carbon::parse($log->date)->format('M') }}</span><span class="block text-xl leading-none">{{ \Carbon\Carbon::parse($log->date)->format('d') }}</span></div>
                                                <div>
                                                    <h4 class="font-bold text-gray-800 text-sm">{{ $mainTR->teacher->name ?? 'Unknown' }}</h4>
                                                    <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($log->date)->format('l') }}</span>
                                                </div>
                                            </div>
                                            @php
                                                $p = $log->records->where('status', 'present')->count();
                                                $t = $log->records->count();
                                                $pct = $t > 0 ? round(($p/$t)*100) : 0;
                                            @endphp
                                            <div class="text-right"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">{{ $pct }}% Present</span></div>
                                        </div>
                                        <div class="mt-3 pl-[72px]"><p class="text-sm text-gray-600 bg-gray-50 p-3 rounded-lg italic border border-gray-100">"{{ $mainTR->comment ?? '-' }}"</p></div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100"><button @click="showHistoryModal = false" class="w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">Close</button></div>
                </div>
            </div>
        </div>

{{-- MODAL 3: STUDENT ATTENDANCE MATRIX (Daily Report) --}}
        <div x-show="showStudentStatsModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                
                <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="showStudentStatsModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                {{-- Modal Lebar (max-w-6xl) agar muat banyak kolom tanggal --}}
                <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-6xl w-full">
                    
                    {{-- Header --}}
                    <div class="bg-white px-6 py-4 border-b border-gray-100 flex justify-between items-center sticky top-0 z-50">
                        <div>
                            <h3 class="text-lg leading-6 font-bold text-gray-900">Attendance Matrix</h3>
                            <p class="text-sm text-gray-500 mt-1">Showing last {{ $teachingLogs->count() }} sessions breakdown.</p>
                        </div>
                        <div class="flex items-center gap-4">
                            {{-- Legend Kecil --}}
                            <div class="hidden lg:flex items-center gap-4 text-xs text-gray-600 font-medium">
                                <span class="flex items-center gap-1.5">
                                    <span class="w-3 h-3 rounded-full bg-blue-600 shadow-sm"></span> Present
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <span class="w-3 h-3 rounded-full bg-yellow-500 shadow-sm"></span> Late
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <span class="w-3 h-3 rounded-full bg-purple-600 shadow-sm"></span> Sick
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <span class="w-3 h-3 rounded-full bg-emerald-600 shadow-sm"></span> Permit
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <span class="w-3 h-3 rounded-full bg-red-600 shadow-sm"></span> Absent
                                </span>
                            </div>
                            <button @click="showStudentStatsModal = false" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    </div>

                    {{-- Scrollable Container --}}
                    <div class="max-h-[75vh] overflow-auto custom-scrollbar relative bg-white">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-gray-50 text-gray-500 text-xs font-bold uppercase border-b border-gray-200 sticky top-0 z-20 shadow-sm">
                                <tr>
                                    {{-- Kolom Nama (Sticky Kiri) --}}
                                    <th class="px-4 py-3 bg-gray-50 sticky left-0 z-30 w-48 border-r border-gray-200 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]">
                                        Student Name
                                    </th>
                                    {{-- Kolom Summary % --}}
                                    <th class="px-2 py-3 text-center w-16 bg-gray-50 border-r border-gray-100">
                                        Rate
                                    </th>
                                    {{-- Loop Header Tanggal --}}
                                    @foreach($teachingLogs as $session)
                                        <th class="px-2 py-3 text-center min-w-[60px] whitespace-nowrap bg-gray-50">
                                            <div class="flex flex-col items-center">
                                                <span class="text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($session->date)->format('D') }}</span>
                                                <span class="text-xs text-gray-700">{{ \Carbon\Carbon::parse($session->date)->format('d/m') }}</span>
                                            </div>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm">
                                @foreach($studentStats as $stat)
                                    <tr class="hover:bg-gray-50 transition">
                                        {{-- Nama Siswa (Sticky Kiri) --}}
                                        <td class="px-4 py-3 bg-white sticky left-0 z-10 border-r border-gray-100 font-medium text-gray-900 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)] group-hover:bg-gray-50">
                                            <div class="truncate w-40" title="{{ $stat->name }}">{{ $stat->name }}</div>
                                            <div class="text-[10px] text-gray-400 font-mono">{{ $stat->student_number }}</div>
                                        </td>

                                        {{-- Rate % --}}
                                        <td class="px-2 py-3 text-center border-r border-gray-100 bg-gray-50/30">
                                            <span class="text-xs font-bold {{ $stat->percentage >= 80 ? 'text-green-600' : ($stat->percentage >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                                {{ $stat->percentage }}%
                                            </span>
                                        </td>

                                        {{-- Loop Status Per Tanggal --}}
                                        @foreach($teachingLogs as $session)
                                            @php
                                                $status = $attendanceMatrix[$stat->id][$session->id] ?? '-';
                                                
                                                // WARNA SOLID (Tegas & Kontras)
                                                // - Present: Biru Solid
                                                // - Late: Kuning Gelap/Oranye (biar teks putih terbaca)
                                                // - Sick: Ungu Solid
                                                // - Permission: Hijau Emerald Solid
                                                // - Absent: Merah Solid
                                                
                                                $cellContent = match($status) {
                                                    'present' => '<span class="inline-flex w-6 h-6 items-center justify-center rounded-full bg-blue-600 text-white shadow-sm" title="Present">
                                                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                                </span>',
                                                    
                                                    'late' => '<span class="inline-flex w-6 h-6 items-center justify-center rounded-full bg-yellow-500 text-white font-bold text-[10px] shadow-sm" title="Late">L</span>',
                                                    
                                                    'sick' => '<span class="inline-flex w-6 h-6 items-center justify-center rounded-full bg-purple-600 text-white font-bold text-[10px] shadow-sm" title="Sick">S</span>',
                                                    
                                                    'permission' => '<span class="inline-flex w-6 h-6 items-center justify-center rounded-full bg-emerald-600 text-white font-bold text-[10px] shadow-sm" title="Permission">P</span>',
                                                    
                                                    'absent' => '<span class="inline-flex w-6 h-6 items-center justify-center rounded-full bg-red-600 text-white font-bold text-[10px] shadow-sm" title="Absent">A</span>',
                                                    
                                                    default => '<span class="text-gray-200 text-lg">&bull;</span>'
                                                };
                                            @endphp

                                            <td class="px-2 py-3 text-center border-r border-gray-50 last:border-r-0">
                                                {!! $cellContent !!}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                        @if(count($studentStats) == 0)
                            <div class="flex flex-col items-center justify-center py-12 text-gray-400">
                                <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <p>No attendance data recorded yet.</p>
                            </div>
                        @endif
                    </div>

                    {{-- Footer --}}
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-end">
                        <button @click="showStudentStatsModal = false" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm transition">
                            Close Report
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</x-app-layout>