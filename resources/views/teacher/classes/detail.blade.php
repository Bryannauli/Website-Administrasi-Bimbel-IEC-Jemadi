<x-app-layout>
    <x-slot name="header"></x-slot>

    {{-- WRAPPER UTAMA DENGAN ALPINE JS --}}
    <div class="bg-[#EEF2FF] min-h-screen font-sans" x-data="{ 
        showCreateSessionModal: false,
        showHistoryModal: false,
        showStudentStatsModal: false
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

            {{-- 1. BREADCRUMB --}}
            <nav class="flex mb-5" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('teacher.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-blue-600">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            <a href="{{ route('teacher.classes.index') }}" class="ml-1 text-sm font-medium text-gray-500 hover:text-blue-600 md:ml-2">Classes</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            <span class="ml-1 text-sm font-medium text-gray-900 md:ml-2 truncate max-w-xs">{{ $class->name }}</span>
                        </div>
                    </li>
                </ol>
            </nav>

            {{-- 2. HEADER TITLE --}}
            <div class="mb-6">
                <h2 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent inline-block">
                    My Class: {{ $class->name }}
                </h2>
                <p class="text-gray-500 text-sm mt-1">Manage attendance, view students, and input grades.</p>
            </div>

            <div class="space-y-6">
                
                {{-- 3. INFO KELAS (TOP CARD - MIRROR ADMIN) --}}
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
                                        <div class="flex flex-wrap gap-1">
                                            @forelse($class->schedules as $schedule)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold border 
                                                    {{ $schedule->teacher_type == 'form' ? 'bg-blue-100 text-blue-700 border-blue-200' : 'bg-purple-100 text-purple-700 border-purple-200' }}">
                                                    {{ substr($schedule->day_of_week, 0, 3) }} 
                                                    <span class="mx-0.5 opacity-50">|</span> 
                                                    {{ $schedule->teacher_type == 'form' ? 'F' : 'L' }}
                                                </span>
                                            @empty
                                                <span class="text-gray-400 italic text-sm">No Schedule</span>
                                            @endforelse
                                        </div>
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

                {{-- ========================================================= --}}
                {{-- ROW 1: TEACHERS (Left) & ATTENDANCE ACTION (Right)        --}}
                {{-- ========================================================= --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    {{-- A. TEACHERS ASSIGNED (READ ONLY) --}}
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
                                <div class="p-3 rounded-xl border border-dashed border-gray-300 bg-gray-50 flex items-center justify-center gap-2">
                                    <span class="text-xs font-medium italic text-gray-400">No Form Teacher Assigned</span>
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
                                <div class="p-3 rounded-xl border border-dashed border-gray-300 bg-gray-50 flex items-center justify-center gap-2">
                                    <span class="text-xs font-medium italic text-gray-400">No Local Teacher Assigned</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- B. ATTENDANCE & ACTION CARD (SINGLE BUTTON) --}}
                    <div class="lg:col-span-1">
                        <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl shadow-lg shadow-blue-200 p-6 text-white relative overflow-hidden group h-full flex flex-col justify-between">
                            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl group-hover:opacity-20 transition duration-500"></div>
                            
                            <div class="relative z-10">
                                <h3 class="text-lg font-bold mb-2">Class Attendance</h3>
                                <p class="text-blue-100 text-xs mb-6 opacity-90 leading-relaxed">
                                    Manage your daily class sessions and view attendance history in one place.
                                </p>
                            </div>
                            
                            {{-- SINGLE BUTTON: Buka Modal History (yang didalamnya ada tombol Create) --}}
                            <div class="relative z-10 mt-auto">
                                <button @click="showHistoryModal = true" 
                                    class="w-full py-3.5 bg-white text-blue-700 rounded-xl text-sm font-bold hover:bg-blue-50 transition shadow-lg shadow-blue-900/10 flex items-center justify-center gap-2.5 group-hover:scale-[1.02]">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                    View & Create Session
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ========================================================= --}}
                {{-- ROW 2: STUDENTS (Left) & SIDEBAR WIDGETS (Right)          --}}
                {{-- ========================================================= --}}
                <div class="flex flex-col lg:grid lg:grid-cols-3 gap-6">

                    {{-- D. SIDEBAR: ATTENDANCE REPORT (Mobile: Order-1) --}}
                    <div class="order-1 lg:order-2 lg:col-span-1 space-y-6">
                        
                        {{-- 1. ATTENDANCE WIDGET (REPORT STYLE) --}}
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col h-full overflow-hidden">
                            @php
                                $lastSessionStats = $allSessions->first(); 
                            @endphp

                            @if($lastSessionStats)
                                @php
                                    $totalRec = $lastSessionStats->records->count();
                                    $presentRec = $lastSessionStats->records->whereIn('status', ['present', 'late'])->count();
                                    $perc = $totalRec > 0 ? round(($presentRec / $totalRec) * 100) : 0;
                                    $absentees = $lastSessionStats->records->whereIn('status', ['absent', 'sick', 'permission']);
                                @endphp

                                <div class="p-6 flex-1 flex flex-col">
                                    {{-- Header Widget --}}
                                    <div class="flex justify-between items-start mb-4">
                                        <div>
                                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Attendance</h4>
                                            <h3 class="text-xl font-bold text-gray-900">Last Session</h3>
                                            <p class="text-xs text-gray-400 mt-0.5">{{ \Carbon\Carbon::parse($lastSessionStats->date)->format('d M Y') }}</p>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-3xl font-bold {{ $perc >= 80 ? 'text-green-600' : ($perc >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                                {{ $perc }}%
                                            </span>
                                            <p class="text-[10px] text-gray-400 font-bold uppercase">Present</p>
                                        </div>
                                    </div>

                                    {{-- List Absentees --}}
                                    <div class="flex-1">
                                        @if($absentees->count() > 0)
                                            <p class="text-xs font-bold text-red-500 mb-3 uppercase tracking-wide">Absentees List:</p>
                                            <ul class="space-y-2 max-h-[250px] overflow-y-auto custom-scrollbar pr-1">
                                                @foreach($absentees as $record)
                                                    @php
                                                        $badgeColor = match($record->status) {
                                                            'absent' => 'bg-red-100 text-red-700 border-red-200',
                                                            'sick' => 'bg-purple-100 text-purple-700 border-purple-200',
                                                            'permission' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                                            default => 'bg-gray-100 text-gray-600'
                                                        };
                                                    @endphp
                                                    <li class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-100">
                                                        <span class="text-sm font-semibold text-gray-700 truncate w-32" title="{{ $record->student->name ?? '-' }}">
                                                            {{ $record->student->name ?? '-' }}
                                                        </span>
                                                        <span class="text-[10px] font-bold px-2 py-1 rounded uppercase border {{ $badgeColor }}">
                                                            {{ $record->status }}
                                                        </span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            {{-- State: Perfect Attendance --}}
                                            <div class="h-full flex flex-col items-center justify-center text-center py-8 bg-green-50 rounded-xl border border-dashed border-green-200 mt-2">
                                                <div class="w-12 h-12 rounded-full bg-green-100 text-green-600 flex items-center justify-center mb-2 shadow-sm">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                </div>
                                                <p class="text-sm font-bold text-green-800">Perfect Attendance!</p>
                                                <p class="text-xs text-green-600">All students present.</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Footer Button --}}
                                <div class="p-4 border-t border-gray-100 bg-gray-50/50">
                                    <button @click="showStudentStatsModal = true" 
                                        class="w-full py-3 bg-green-600 text-white rounded-xl text-sm font-bold hover:bg-green-700 transition shadow-md shadow-green-200 flex items-center justify-center gap-2">
                                        View Full Report 
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </button>
                                </div>

                            @else
                                {{-- Empty State (Belum ada sesi sama sekali) --}}
                                <div class="p-8 text-center flex flex-col items-center justify-center h-full">
                                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <h3 class="text-sm font-bold text-gray-900">No Data Yet</h3>
                                    <p class="text-xs text-gray-500 mt-1">Create a session to see attendance stats.</p>
                                </div>
                            @endif
                        </div>

                    </div>

                    {{-- C. STUDENT LIST (Main Content) (Mobile: Order-2) --}}
                    <div class="order-2 lg:order-1 lg:col-span-2 bg-white rounded-2xl shadow-sm p-6 border border-gray-100 flex flex-col">
                        <div class="flex justify-between items-center mb-5">
                            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                                Enrolled Students
                                <span class="bg-gray-100 text-gray-600 text-xs font-bold px-2 py-0.5 rounded-full ml-2">
                                    {{ $students->total() }}
                                </span>
                            </h3>
                            
                            <form method="GET" action="{{ url()->current() }}" class="flex items-center text-xs">
                                <select name="per_page" onchange="this.form.submit()" class="border-gray-200 bg-gray-50 rounded-lg text-xs py-1.5 pl-2 pr-7 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 Rows</option>
                                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 Rows</option>
                                </select>
                            </form>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead class="bg-gray-50 text-gray-400 text-xs font-medium border-b border-gray-100">
                                    <tr>
                                        <th class="px-4 py-3 font-normal w-12 text-center">No</th>
                                        {{-- 1. KOLOM ID NUMBER (Dipindah ke kiri) --}}
                                        <th class="px-4 py-3 font-normal">Student ID</th>
                                        {{-- 2. KOLOM NAMA (Dipindah ke kanan) --}}
                                        <th class="px-4 py-3 font-normal">Student Name</th>
                                        <th class="px-4 py-3 font-normal text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-sm text-gray-800">
                                    @forelse($students as $student)
                                        <tr class="hover:bg-gray-50 transition-colors group">
                                            <td class="px-4 py-3 text-center text-gray-400 text-xs">{{ $loop->iteration + $students->firstItem() - 1 }}</td>
                                            
                                            {{-- 1. DATA ID NUMBER --}}
                                            <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $student->student_number }}</td>

                                            {{-- 2. DATA NAMA (Dengan Avatar) --}}
                                            <td class="px-4 py-3">
                                                <div class="flex items-center">
                                                    <div class="h-8 w-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs mr-3 border border-blue-200">
                                                        {{ substr($student->name, 0, 1) }}
                                                    </div>
                                                    <span class="font-medium text-gray-900">{{ $student->name }}</span>
                                                </div>
                                            </td>

                                            <td class="px-4 py-3 text-center">
                                                @if($student->is_active)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-green-50 text-green-700 border border-green-100">Active</span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-red-50 text-red-700 border border-red-100">Inactive</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-10 text-center text-gray-400 italic bg-gray-50 rounded-lg border border-dashed border-gray-200">
                                                No students enrolled.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        @if($students->hasPages())
                            <div class="pt-4 mt-auto border-t border-gray-100">
                                {{ $students->links() }}
                            </div>
                        @endif
                    </div>

                </div>
                
                {{-- 4. ACADEMIC ASSESSMENTS (BOTTOM FULL WIDTH) --}}
                <div class="mt-8">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        Assessment Details
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        {{-- MID TERM CARD --}}
                        @php
                            $midSession = $assessments->where('type', 'mid')->first();
                            $midStatus = $midSession->status ?? 'draft'; 
                            
                            $midColor = match($midStatus) {
                                'submitted' => 'bg-blue-100 text-blue-700 border-blue-200',
                                'final'     => 'bg-purple-100 text-purple-700 border-purple-200',
                                default     => 'bg-gray-100 text-gray-600 border-gray-200',
                            };
                        @endphp
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 relative overflow-hidden group hover:border-blue-300 hover:shadow-md transition-all">
                            
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h4 class="text-lg font-bold text-gray-800">Mid Term</h4>
                                    <p class="text-xs text-gray-400 mt-1">
                                        {{ $midSession && $midSession->date ? \Carbon\Carbon::parse($midSession->date)->format('d M Y') : 'Date not set' }}
                                    </p>
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide border {{ $midColor }}">
                                    {{ ucfirst($midStatus) }}
                                </span>
                            </div>

                            <div class="flex items-center gap-3">
                                @if($midSession)
                                    <a href="{{ route('teacher.classes.assessment.detail', ['classId' => $class->id, 'assessmentId' => $midSession->id]) }}" 
                                       class="flex-1 inline-flex justify-center items-center px-4 py-2.5 bg-blue-600 text-white text-sm font-bold rounded-lg hover:bg-blue-700 transition shadow-blue-200 gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        {{ $midStatus == 'draft' ? 'Input Grades' : 'View Grades' }}
                                    </a>
                                @else
                                    <button disabled class="flex-1 inline-flex justify-center items-center px-4 py-2.5 bg-gray-100 text-gray-400 text-sm font-bold rounded-lg cursor-not-allowed gap-2">
                                        Not Scheduled
                                    </button>
                                @endif
                                
                                <button disabled class="p-2.5 text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed" title="Print Report Card">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                </button>
                            </div>
                        </div>

                        {{-- FINAL TERM CARD --}}
                        @php
                            $finalSession = $assessments->where('type', 'final')->first();
                            $finalStatus = $finalSession->status ?? 'draft'; 

                            $finalColor = match($finalStatus) {
                                'submitted' => 'bg-indigo-100 text-indigo-700 border-indigo-200',
                                'final'     => 'bg-purple-100 text-purple-700 border-purple-200',
                                default     => 'bg-gray-100 text-gray-600 border-gray-200',
                            };
                        @endphp
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 relative overflow-hidden group hover:border-indigo-300 hover:shadow-md transition-all">
                            
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h4 class="text-lg font-bold text-gray-800">Final Term</h4>
                                    <p class="text-xs text-gray-400 mt-1">
                                        {{ $finalSession && $finalSession->date ? \Carbon\Carbon::parse($finalSession->date)->format('d M Y') : 'Date not set' }}
                                    </p>
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide border {{ $finalColor }}">
                                    {{ ucfirst($finalStatus) }}
                                </span>
                            </div>

                            <div class="flex items-center gap-3">
                                @if($finalSession)
                                    <a href="{{ route('teacher.classes.assessment.detail', ['classId' => $class->id, 'assessmentId' => $finalSession->id]) }}" 
                                       class="flex-1 inline-flex justify-center items-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-lg hover:bg-indigo-700 transition shadow-indigo-200 gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        {{ $finalStatus == 'draft' ? 'Input Grades' : 'View Grades' }}
                                    </a>
                                @else
                                    <button disabled class="flex-1 inline-flex justify-center items-center px-4 py-2.5 bg-gray-100 text-gray-400 text-sm font-bold rounded-lg cursor-not-allowed gap-2">
                                        Not Scheduled
                                    </button>
                                @endif
                                
                                <button disabled class="p-2.5 text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed" title="Print Certificate">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- INCLUDE PARTIALS --}}
        @include('teacher.classes.partials.activity-history-modal', ['classSessions' => $classSessions, 'class' => $class])
        @include('teacher.classes.partials.attendance-modal', ['allSessions' => $allSessions, 'studentStats' => $studentStats, 'attendanceMatrix' => $attendanceMatrix])

        {{-- MODAL CREATE SESSION (INLINE) --}}
        <div x-show="showCreateSessionModal" 
             class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
            
            <div x-show="showCreateSessionModal" 
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
                 class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>

            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div x-show="showCreateSessionModal" 
                         @click.away="showCreateSessionModal = false"
                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                         class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100">
                        
                        <form action="{{ route('teacher.classes.session.store', $class->id) }}" method="POST">
                            @csrf
                            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                <div class="flex items-start gap-4">
                                    <div class="flex-shrink-0 w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                    <div class="w-full">
                                        <h3 class="text-lg font-bold text-gray-900" id="modal-title">Create Session</h3>
                                        <p class="text-sm text-gray-500 mt-1">Initialize attendance for today's class.</p>
                                        
                                        <div class="mt-5 space-y-4">
                                            <div>
                                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1.5">Date</label>
                                                <input type="date" name="date" readonly 
                                                       class="w-full rounded-lg border-gray-200 bg-gray-50 text-gray-500 text-sm focus:ring-0 cursor-not-allowed" 
                                                       value="{{ date('Y-m-d') }}">
                                            </div>
                                            <div>
                                                <label for="topics" class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1.5">Topic / Material <span class="text-red-500">*</span></label>
                                                <textarea name="topics" id="topics" rows="3" required 
                                                          class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm placeholder-gray-400 transition" 
                                                          placeholder="e.g. Simple Present Tense, Introduction..."></textarea>
                                            </div>
                                            
                                            <div class="rounded-lg bg-yellow-50 p-3 border border-yellow-100 flex gap-3">
                                                <svg class="w-5 h-5 text-yellow-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                <p class="text-xs text-yellow-700 leading-relaxed">
                                                    You will be marked as <strong>Present</strong> automatically when creating this session.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-2">
                                <button type="submit" class="inline-flex w-full justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-blue-700 sm:w-auto transition-colors">
                                    Start Session
                                </button>
                                <button type="button" @click="showCreateSessionModal = false" class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-4 py-2.5 text-sm font-bold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition-colors">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>