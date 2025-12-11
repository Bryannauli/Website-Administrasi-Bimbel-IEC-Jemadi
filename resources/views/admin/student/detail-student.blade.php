<x-app-layout>
    <x-slot name="header"></x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- BREADCRUMB (DYNAMIC CONTEXT AWARE) --}}
            <nav class="flex mb-5" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    
                    {{-- 1. Dashboard --}}
                    <li class="inline-flex items-center">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-blue-600">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                            Dashboard
                        </a>
                    </li>

                    {{-- LOGIKA CABANG BREADCRUMB --}}
                    @if(request('ref') == 'class' && $student->classModel)
                        {{-- JIKA DARI KELAS: Dashboard > Classes > Nama Kelas > Detail Siswa --}}
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                <a href="{{ route('admin.classes.index') }}" class="ml-1 text-sm font-medium text-gray-500 hover:text-blue-600 md:ml-2">Classes</a>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                <a href="{{ route('admin.classes.detailclass', $student->classModel->id) }}" class="ml-1 text-sm font-medium text-gray-500 hover:text-blue-600 md:ml-2">{{ $student->classModel->name }}</a>
                            </div>
                        </li>
                    @else
                        {{-- JIKA NORMAL: Dashboard > Students > Detail Siswa --}}
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                <a href="{{ route('admin.student.index') }}" class="ml-1 text-sm font-medium text-gray-500 hover:text-blue-600 md:ml-2">Students</a>
                            </div>
                        </li>
                    @endif

                    {{-- Nama Siswa (Current Page) --}}
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            <span class="ml-1 text-sm font-medium text-gray-900 md:ml-2">{{ $student->name }}</span>
                        </div>
                    </li>
                </ol>
            </nav>

            {{-- HEADER: Title & Edit Button --}}
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Student Profile</h2>
                
                {{-- FIX: Tombol Edit sekarang mewariskan 'ref' dan 'class_id' dari URL saat ini --}}
                <a href="{{ route('admin.student.edit', [
                        'id' => $student->id, 
                        'ref' => request('ref') == 'class' ? 'class' : 'detail', 
                        'class_id' => request('class_id')
                    ]) }}" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium transition-colors shadow-sm">
                    Edit Student
                </a>
            </div>

            {{-- 1. KARTU UTAMA: PROFIL --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8 relative overflow-hidden z-0">
                <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-bl-full -mr-8 -mt-8"></div>
                
                <div class="flex flex-col md:flex-row items-center justify-between relative gap-6">
                    <div class="flex items-center gap-6">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($student->name) }}&background=2563EB&color=fff&size=128&bold=true"
                            alt="{{ $student->name }}" class="w-20 h-20 md:w-24 md:h-24 rounded-full border-4 border-white shadow-md bg-white">

                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ $student->name }}</h1>
                            <p class="text-gray-500 font-medium">ID: {{ $student->student_number }}</p>
                            
                            <div class="flex items-center gap-2 mt-2">
                                {{-- Status Badge (Updated Style) --}}
                                @if($student->is_active)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 border border-green-200">Active</span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-500 border border-gray-200">Inactive</span>
                                @endif

                                @if($student->classModel)
                                    <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-bold uppercase tracking-wider border border-blue-100">
                                        {{ $student->classModel->name }}
                                    </span>
                                @else
                                    <span class="px-3 py-1 bg-red-50 text-red-600 rounded-full text-xs font-bold uppercase border border-red-100">
                                        No Class
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="text-right hidden md:block">
                        <p class="text-sm text-gray-500">Joined Date</p>
                        <p class="text-lg font-semibold text-gray-800">{{ $student->created_at->format('d M Y') }}</p>
                    </div>
                </div>
            </div>

            {{-- 2. LAYOUT GRID: INFO & STATISTIK --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                
                {{-- KOLOM KIRI: BIODATA --}}
                <div class="lg:col-span-1 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 h-fit">
                    <h3 class="text-lg font-bold text-gray-800 mb-6 border-b pb-2">Personal Info</h3>
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide font-semibold">Full Name</p>
                            <p class="text-sm font-medium text-gray-800">{{ $student->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide font-semibold">Gender</p>
                            <p class="text-sm font-medium text-gray-800 capitalize">{{ $student->gender }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide font-semibold">Phone</p>
                            <p class="text-sm font-medium text-gray-800">{{ $student->phone ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide font-semibold">Address</p>
                            <p class="text-sm font-medium text-gray-800">{{ $student->address ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                {{-- KOLOM KANAN: STATISTIK ABSENSI --}}
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-800">Attendance Overview</h3>
                        <div class="text-sm text-gray-500 bg-gray-50 px-3 py-1 rounded-lg">
                            Total Sessions: <strong>{{ $totalDays }}</strong>
                        </div>
                    </div>

                    {{-- A. KOTAK STATISTIK --}}
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
                        <div class="p-4 rounded-xl bg-blue-50 border border-blue-100 text-center">
                            <p class="text-xs text-blue-600 font-bold uppercase mb-1">Present</p>
                            <p class="text-2xl font-bold text-blue-700">{{ $summary['present'] }}</p>
                        </div>
                        <div class="p-4 rounded-xl bg-yellow-50 border border-yellow-100 text-center">
                            <p class="text-xs text-yellow-600 font-bold uppercase mb-1">Late</p>
                            <p class="text-2xl font-bold text-yellow-700">{{ $summary['late'] }}</p>
                        </div>
                        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-100 text-center">
                            <p class="text-xs text-emerald-600 font-bold uppercase mb-1">Permit</p>
                            <p class="text-2xl font-bold text-emerald-700">{{ $summary['permission'] }}</p>
                        </div>
                        <div class="p-4 rounded-xl bg-purple-50 border border-purple-100 text-center">
                            <p class="text-xs text-purple-600 font-bold uppercase mb-1">Sick</p>
                            <p class="text-2xl font-bold text-purple-700">{{ $summary['sick'] }}</p>
                        </div>
                        <div class="p-4 rounded-xl bg-red-50 border border-red-100 text-center">
                            <p class="text-xs text-red-600 font-bold uppercase mb-1">Absent</p>
                            <p class="text-2xl font-bold text-red-700">{{ $summary['absent'] }}</p>
                        </div>
                    </div>

                    {{-- B. CHART LINGKARAN --}}
                    <div class="flex-1 flex items-center justify-center py-4">
                        @php
                            $total = $totalDays > 0 ? $totalDays : 1;
                            $pPresent    = ($summary['present'] / $total) * 100;
                            $pLate       = ($summary['late'] / $total) * 100;
                            $pPermission = ($summary['permission'] / $total) * 100;
                            $pSick       = ($summary['sick'] / $total) * 100;
                            
                            $stop1 = $pPresent;
                            $stop2 = $stop1 + $pLate;
                            $stop3 = $stop2 + $pPermission;
                            $stop4 = $stop3 + $pSick;
                            
                            $attendanceRate = $totalDays > 0 ? round((($summary['present'] + $summary['late']) / $totalDays) * 100) : 0;

                            $chartAttribute = 'style="background: conic-gradient(
                                #2563eb 0% ' . $stop1 . '%,
                                #eab308 ' . $stop1 . '% ' . $stop2 . '%,
                                #10b981 ' . $stop2 . '% ' . $stop3 . '%,
                                #9333ea ' . $stop3 . '% ' . $stop4 . '%,
                                #dc2626 ' . $stop4 . '% 100%
                            );"';
                        @endphp

                        <div class="flex flex-col items-center">
                            <div class="relative w-48 h-48 rounded-full shadow-inner" {!! $chartAttribute !!}>
                                <div class="absolute inset-0 m-5 bg-white rounded-full flex flex-col items-center justify-center shadow-sm">
                                    <span class="text-gray-400 text-xs font-semibold uppercase">Rate</span>
                                    <span class="text-4xl font-extrabold text-gray-800">{{ $attendanceRate }}%</span>
                                </div>
                            </div>
                            
                            <div class="flex flex-wrap justify-center gap-x-4 gap-y-2 mt-6 text-xs text-gray-500 uppercase font-bold">
                                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span> Present</span>
                                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-yellow-500"></span> Late</span>
                                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Permit</span>
                                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-purple-600"></span> Sick</span>
                                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-red-600"></span> Absent</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- 3. RIWAYAT ABSENSI --}}
            <div class="mb-10">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-800">Attendance History</h3>
                    <span class="text-xs text-gray-400">&larr; Scroll left for earliest</span>
                </div>

                <div id="attendance-timeline" class="flex overflow-x-auto gap-4 pb-4 custom-scrollbar scroll-smooth" style="scrollbar-width: thin;">
                    @forelse ($attendance->reverse() as $record)
                        @php
                            $theme = match($record->status) {
                                'present'    => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-100'],
                                'late'       => ['bg' => 'bg-yellow-50', 'text' => 'text-yellow-700', 'border' => 'border-yellow-100'],
                                'permission' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-100'],
                                'sick'       => ['bg' => 'bg-purple-50', 'text' => 'text-purple-700', 'border' => 'border-purple-100'],
                                'absent'     => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'border' => 'border-red-100'],
                                default      => ['bg' => 'bg-gray-50', 'text' => 'text-gray-600', 'border' => 'border-gray-100'],
                            };
                        @endphp

                        <div class="min-w-[140px] bg-white border {{ $theme['border'] }} rounded-xl p-4 flex flex-col items-center justify-center shadow-sm hover:shadow-md transition-shadow flex-shrink-0">
                            <span class="text-xs text-gray-400 font-semibold uppercase mb-1">
                                {{ \Carbon\Carbon::parse($record->session->date)->format('D, d M') }}
                            </span>
                            <span class="text-lg font-bold {{ $theme['text'] }} mb-2">{{ ucfirst($record->status) }}</span>
                            <div class="w-8 h-8 rounded-full {{ $theme['bg'] }} flex items-center justify-center {{ $theme['text'] }}">
                                @if($record->status == 'present') <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                @elseif($record->status == 'late') <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                @elseif($record->status == 'absent') <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                @else <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="w-full text-center py-8 text-gray-400 bg-gray-50 rounded-xl border border-dashed border-gray-200">No attendance history yet.</div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const container = document.getElementById('attendance-timeline');
            if(container) container.scrollLeft = container.scrollWidth;
        });
    </script>
</x-app-layout>