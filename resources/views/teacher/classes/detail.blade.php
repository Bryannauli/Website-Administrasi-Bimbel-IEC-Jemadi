{{-- resources/views/teacher/classes/detail.blade.php --}}

<x-app-layout>
    <x-slot name="header"></x-slot>

    {{-- WRAPPER UTAMA DENGAN ALPINE JS --}}
    <div class="bg-[#EEF2FF] min-h-screen font-sans" x-data="{ 
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
                
                {{-- 3. INFO KELAS (TOP CARD) --}}
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

                    {{-- B. ATTENDANCE & ACTION CARD (LOGIC CREATE/EDIT TODAY) --}}
                    <div class="lg:col-span-1">
                        <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl shadow-lg shadow-blue-200 p-6 text-white relative overflow-hidden group h-full flex flex-col justify-between">
                            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl group-hover:opacity-20 transition duration-500"></div>
                            
                            <div class="relative z-10">
                                <h3 class="text-lg font-bold mb-2">Class Attendance</h3>
                                <p class="text-blue-100 text-xs mb-6 opacity-90 leading-relaxed">
                                    @if(isset($sessionToday) && $sessionToday)
                                        Session for today ({{ \Carbon\Carbon::parse($sessionToday->date)->format('d M Y') }}) is already created. Edit now or view history.
                                    @else
                                        Start recording today's attendance or view past sessions.
                                    @endif
                                </p>
                            </div>
                            
                            {{-- BUTTON LOGIC --}}
                            <div class="relative z-10 mt-auto flex flex-col gap-2">
                                
                                @if(isset($sessionToday) && $sessionToday)
                                    {{-- JIKA SUDAH ADA SESI HARI INI: Tombol Edit --}}
                                    <a href="{{ route('teacher.classes.session.detail', ['classId' => $class->id, 'sessionId' => $sessionToday->id]) }}" 
                                        class="w-full py-3.5 bg-yellow-500 text-white rounded-xl text-sm font-bold hover:bg-yellow-600 transition shadow-lg shadow-yellow-900/10 flex items-center justify-center gap-2 group-hover:scale-[1.02]">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        Edit Today's Attendance
                                    </a>
                                @else
                                    {{-- JIKA BELUM ADA SESI HARI INI: Tombol Create --}}
                                    <button @click="showHistoryModal = true; $nextTick(() => { document.getElementById('createSessionBtn').click(); })" 
                                        class="w-full py-3.5 bg-white text-blue-700 rounded-xl text-sm font-bold hover:bg-blue-50 transition shadow-lg shadow-blue-900/10 flex items-center justify-center gap-2 group-hover:scale-[1.02]">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        Create Today's Session
                                    </button>
                                @endif

                                {{-- Tombol View History --}}
                                <button @click="showHistoryModal = true" 
                                    class="w-full py-2 bg-blue-800/80 text-blue-200 rounded-xl text-xs font-bold hover:bg-blue-800 transition">
                                    View Full History
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ========================================================= --}}
                {{-- ROW 2: STUDENTS & SIDEBAR WIDGETS                         --}}
                {{-- ========================================================= --}}
                <div class="flex flex-col lg:grid lg:grid-cols-3 gap-6">

                    {{-- D. SIDEBAR: ATTENDANCE REPORT (Mobile: Order-1) --}}
                    <div class="order-1 lg:order-2 lg:col-span-1 space-y-6">
                        
                        {{-- 1. ATTENDANCE WIDGET (REPORT STYLE) --}}
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col h-full overflow-hidden">
                            @php
                                // $allSessions masih Eloquent Collection (karena butuh 'records' relation untuk widget ini)
                                // Sesuai controller yang direfactor
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

                                    {{-- List Absentees / Status --}}
                                    <div class="flex-1">
                                        @if($totalRec === 0)
                                            <div class="h-full flex flex-col items-center justify-center text-center py-8 bg-yellow-50 rounded-xl border border-dashed border-yellow-200 mt-2">
                                                <div class="w-12 h-12 rounded-full bg-yellow-100 text-yellow-600 flex items-center justify-center mb-2 shadow-sm">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                </div>
                                                <p class="text-sm font-bold text-yellow-800">Attendance Pending</p>
                                                <p class="text-xs text-yellow-600 px-4">Please input attendance for this session.</p>
                                            </div>

                                        @elseif($absentees->count() > 0)
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
                                {{-- Empty State --}}
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

                    {{-- C. STUDENT LIST (Main Content) --}}
                    <div class="order-2 lg:order-1 lg:col-span-2 bg-white rounded-2xl shadow-sm p-6 border border-gray-100 flex flex-col min-h-[400px]">
                        <div class="flex justify-between items-center mb-5">
                            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                                Enrolled Students
                                {{-- Gunakan count dari enrolledStudents agar akurat --}}
                                <span class="bg-gray-100 text-gray-600 text-xs font-bold px-2 py-0.5 rounded-full ml-2">
                                    {{ $enrolledStudents->count() }}
                                </span>
                            </h3>
                        </div>

                        {{-- WRAPPER TABEL --}}
                        <div class="overflow-x-auto overflow-y-auto max-h-[500px] flex-1 custom-scrollbar border border-gray-50 rounded-lg">
                            <table class="w-full text-left border-collapse relative">
                                <thead class="bg-gray-50 text-gray-400 text-xs font-medium border-b border-gray-100 sticky top-0 z-10 shadow-sm">
                                    <tr>
                                        <th class="px-4 py-3 font-normal w-12 text-center bg-gray-50">No</th>
                                        <th class="px-4 py-3 font-normal bg-gray-50">Student ID</th>
                                        <th class="px-4 py-3 font-normal bg-gray-50">Student Name</th>
                                        <th class="px-4 py-3 font-normal text-center bg-gray-50">Attendance</th>
                                        <th class="px-4 py-3 font-normal text-center bg-gray-50">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-sm text-gray-800 bg-white">
                                    {{-- [UPDATE] Gunakan $enrolledStudents (yang sudah bersih dari Deleted) --}}
                                    @forelse($enrolledStudents as $index => $student)
                                        <tr class="transition group {{ $student->is_active ? 'hover:bg-gray-50' : 'bg-red-50 hover:bg-red-100' }}">
                                            
                                            {{-- NOMOR --}}
                                            {{-- Karena $index dari collection filter mungkin loncat, kita pakai loop iteration atau counter manual --}}
                                            <td class="px-4 py-3 text-center text-gray-400 text-xs">{{ $loop->iteration }}</td>
                                            
                                            {{-- ID --}}
                                            <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $student->student_number }}</td>

                                            {{-- NAME --}}
                                            <td class="px-4 py-3 font-medium transition-colors {{ $student->is_active ? 'text-gray-900' : 'text-red-800 line-through decoration-red-500' }}">
                                                {{ $student->name }}
                                            </td>

                                            {{-- ATTENDANCE (%) --}}
                                            <td class="px-4 py-3 text-center">
                                                @if($student->is_active)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold 
                                                        {{ $student->percentage >= 80 ? 'bg-green-50 text-green-700' : ($student->percentage >= 50 ? 'bg-yellow-50 text-yellow-700' : 'bg-red-50 text-red-700') }}">
                                                        {{ $student->percentage }}%
                                                    </span>
                                                @else
                                                    <span class="text-xs text-gray-400">-</span>
                                                @endif
                                            </td>

                                            {{-- STATUS (Pill Style) --}}
                                            <td class="px-4 py-3 text-center">
                                                @if($student->is_active)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-700 border border-green-200">Active</span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700 border border-red-200">Inactive</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic bg-gray-50">
                                                No students enrolled.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
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
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 relative overflow-hidden group hover:border-blue-300 hover:shadow-md transition-all flex flex-col justify-between">
                            
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

                            <div>
                                @if($midSession)
                                    <a href="{{ route('teacher.classes.assessment.detail', ['classId' => $class->id, 'assessmentId' => $midSession->id]) }}" 
                                       class="w-full inline-flex justify-center items-center px-4 py-3 bg-blue-600 text-white text-sm font-bold rounded-xl hover:bg-blue-700 transition shadow-lg shadow-blue-200 gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        {{ $midStatus == 'draft' ? 'Input Grades' : 'View Grades' }}
                                    </a>
                                @else
                                    <button disabled class="w-full inline-flex justify-center items-center px-4 py-3 bg-gray-100 text-gray-400 text-sm font-bold rounded-xl cursor-not-allowed gap-2 border border-gray-200">
                                        Not Scheduled
                                    </button>
                                @endif
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
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 relative overflow-hidden group hover:border-indigo-300 hover:shadow-md transition-all flex flex-col justify-between">
                            
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

                            <div>
                                @if($finalSession)
                                    <a href="{{ route('teacher.classes.assessment.detail', ['classId' => $class->id, 'assessmentId' => $finalSession->id]) }}" 
                                        class="w-full inline-flex justify-center items-center px-4 py-3 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition shadow-lg shadow-indigo-200 gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        {{ $finalStatus == 'draft' ? 'Input Grades' : 'View Grades' }}
                                    </a>
                                @else
                                    <button disabled class="w-full inline-flex justify-center items-center px-4 py-3 bg-gray-100 text-gray-400 text-sm font-bold rounded-xl cursor-not-allowed gap-2 border border-gray-200">
                                        Not Scheduled
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
        </div>

        {{-- INCLUDE PARTIALS --}}
        {{-- Pass classSessions (yang sekarang dari View) ke partial --}}
        @include('teacher.classes.partials.activity-history-modal', ['classSessions' => $classSessions, 'class' => $class, 'sessionToday' => $sessionToday ?? null])
        
        {{-- Pass studentStats & attendanceMatrix ke partial Modal Matrix --}}
        @include('teacher.classes.partials.attendance-modal', ['allSessions' => $allSessions, 'studentStats' => $studentStats, 'attendanceMatrix' => $attendanceMatrix])

    </div>
</x-app-layout>