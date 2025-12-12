<x-app-layout>
    <x-slot name="header"></x-slot>

    <div class="py-6 bg-[#F3F4FF] min-h-screen font-sans">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- 1. BREADCRUMB --}}
            <nav class="flex mb-5" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-blue-600">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            <a href="{{ route('admin.teacher.index') }}" class="ml-1 text-sm font-medium text-gray-500 hover:text-blue-600 md:ml-2">Teachers</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            <span class="ml-1 text-sm font-medium text-gray-900 md:ml-2">{{ $teacher->name }}</span>
                        </div>
                    </li>
                </ol>
            </nav>

            {{-- HEADER: Title & Actions --}}
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold bg-gradient-to-b from-blue-500 to-red-500 bg-clip-text text-transparent">Teacher Profile</h2>
                {{-- Dummy Edit Button --}}
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium transition-colors shadow-sm">
                    Edit Teacher
                </button>
            </div>

            {{-- 2. PROFILE CARD --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8 relative overflow-hidden z-0">
                <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-bl-full -mr-8 -mt-8"></div>
                
                <div class="flex flex-col md:flex-row items-center justify-between relative gap-6">
                    <div class="flex items-center gap-6">
                        {{-- Avatar --}}
                        <div class="w-20 h-20 md:w-24 md:h-24 rounded-full border-4 border-white shadow-md bg-indigo-100 flex items-center justify-center text-indigo-500 text-2xl font-bold overflow-hidden">
                            @if($teacher->profile_photo_path)
                                <img src="{{ Storage::url($teacher->profile_photo_path) }}" alt="{{ $teacher->name }}" class="w-full h-full object-cover">
                            @else
                                {{ substr($teacher->name, 0, 2) }}
                            @endif
                        </div>

                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ $teacher->name }}</h1>
                            <p class="text-gray-500 font-medium">ID: {{  $teacher->id }}</p>
                            
                            <div class="flex items-center gap-2 mt-2">
                                {{-- Status Badge --}}
                                @if($teacher->is_active ?? true)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 border border-green-200">Active</span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-500 border border-gray-200">Inactive</span>
                                @endif

                                {{-- Type Badge --}}
                                <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-bold uppercase tracking-wider border border-blue-100">
                                    {{ $type }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="text-right hidden md:block">
                        <p class="text-sm text-gray-500">Joined Date</p>
                        <p class="text-lg font-semibold text-gray-800">{{ $teacher->created_at->format('d M Y') }}</p>
                    </div>
                </div>
            </div>

            {{-- 3. GRID LAYOUT: INFO & STATS --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                
                {{-- KOLOM KIRI: PERSONAL INFO --}}
                <div class="lg:col-span-1 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 h-fit">
                    <h3 class="text-lg font-bold text-gray-800 mb-6 border-b pb-2">Personal Info</h3>
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide font-semibold">Full Name</p>
                            <p class="text-sm font-medium text-gray-800">{{ $teacher->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide font-semibold">Email</p>
                            <p class="text-sm font-medium text-gray-800">{{ $teacher->email }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide font-semibold">Phone</p>
                            <p class="text-sm font-medium text-gray-800">{{ $teacher->phone ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide font-semibold">Address</p>
                            <p class="text-sm font-medium text-gray-800">{{ $teacher->address ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide font-semibold">Assigned Class</p>
                            @if($teacher->formClasses->isNotEmpty())
                                @foreach($teacher->formClasses as $fc)
                                    <span class="inline-block mt-1 px-2 py-1 bg-indigo-50 text-indigo-700 rounded text-xs font-bold border border-indigo-100">{{ $fc->name }} (Wali)</span>
                                @endforeach
                            @elseif($teacher->localClasses->isNotEmpty())
                                @foreach($teacher->localClasses as $lc)
                                    <span class="inline-block mt-1 px-2 py-1 bg-purple-50 text-purple-700 rounded text-xs font-bold border border-purple-100">{{ $lc->name }} </span>
                                    <span class="inline-block mt-1 px-2 py-1 bg-purple-50 text-blue-700 rounded text-xs font-bold border border-purple-100"> {{ $class->classroom ?? 'No Classroom' }}</span>
                                @endforeach
                            @else
                                <p class="text-sm text-gray-500 italic">No class assigned</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- KOLOM KANAN: ATTENDANCE STATS --}}
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-800">Attendance Overview</h3>
                        <div class="text-sm text-gray-500 bg-gray-50 px-3 py-1 rounded-lg">
                            Total Sessions: <strong>{{ $totalDays }}</strong>
                        </div>
                    </div>

                    {{-- A. KOTAK STATISTIK --}}
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                        <div class="p-4 rounded-xl bg-blue-50 border border-blue-100 text-center">
                            <p class="text-xs text-blue-600 font-bold uppercase mb-1">Present</p>
                            <p class="text-2xl font-bold text-blue-700">{{ $present }}</p>
                        </div>
                        <div class="p-4 rounded-xl bg-red-50 border border-red-100 text-center">
                            <p class="text-xs text-red-600 font-bold uppercase mb-1">Absent</p>
                            <p class="text-2xl font-bold text-red-700">{{ $absent }}</p>
                        </div>
                        <div class="p-4 rounded-xl bg-purple-50 border border-purple-100 text-center">
                            <p class="text-xs text-purple-600 font-bold uppercase mb-1">Sick</p>
                            <p class="text-2xl font-bold text-purple-700">{{ $sick }}</p>
                        </div>
                        <div class="p-4 rounded-xl bg-yellow-50 border border-yellow-100 text-center">
                            <p class="text-xs text-yellow-600 font-bold uppercase mb-1">Late/Permit</p>
                            <p class="text-2xl font-bold text-yellow-700">{{ $late }}</p>
                        </div>
                    </div>

                    {{-- B. CHART LINGKARAN --}}
                    <div class="flex-1 flex items-center justify-center py-4">
                        @php
                            $pPresent = $totalDays > 0 ? ($present / $totalDays) * 100 : 0;
                            $pAbsent  = $totalDays > 0 ? ($absent / $totalDays) * 100 : 0;
                            $pSick    = $totalDays > 0 ? ($sick / $totalDays) * 100 : 0;
                            $pLate    = $totalDays > 0 ? ($late / $totalDays) * 100 : 0;

                            $stop1 = $pPresent;
                            $stop2 = $stop1 + $pAbsent;
                            $stop3 = $stop2 + $pSick;
                            
                            // Warna: Present(Biru), Absent(Merah), Sick(Ungu), Late(Kuning)
                            $chartGradient = "conic-gradient(
                                #2563eb 0% {$stop1}%, 
                                #dc2626 {$stop1}% {$stop2}%, 
                                #9333ea {$stop2}% {$stop3}%, 
                                #eab308 {$stop3}% 100%
                            )";
                        @endphp

                        <div class="flex flex-col items-center">
                            <div class="relative w-48 h-48 rounded-full shadow-inner" style="background: {{ $chartGradient }};">
                                <div class="absolute inset-0 m-5 bg-white rounded-full flex flex-col items-center justify-center shadow-sm">
                                    <span class="text-gray-400 text-xs font-semibold uppercase">Rate</span>
                                    <span class="text-4xl font-extrabold text-gray-800">{{ $percentage }}%</span>
                                </div>
                            </div>
                            
                            <div class="flex flex-wrap justify-center gap-x-4 gap-y-2 mt-6 text-xs text-gray-500 uppercase font-bold">
                                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span> Present</span>
                                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-red-600"></span> Absent</span>
                                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-purple-600"></span> Sick</span>
                                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-yellow-500"></span> Late</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 4. ATTENDANCE HISTORY (Horizontal Scroll) --}}
            <div class="mb-10">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-800">Attendance History (Last 7 Sessions)</h3>
                    <span class="text-xs text-gray-400">&larr; Scroll left for earliest</span>
                </div>

                <div id="attendance-timeline" class="flex overflow-x-auto gap-4 pb-4 custom-scrollbar scroll-smooth" style="scrollbar-width: thin;">
                    @forelse ($lastRecords as $record)
                        @php
                            $theme = match($record->status) {
                                'present'    => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-100'],
                                'late'       => ['bg' => 'bg-yellow-50', 'text' => 'text-yellow-700', 'border' => 'border-yellow-100'],
                                'permission' => ['bg' => 'bg-yellow-50', 'text' => 'text-yellow-700', 'border' => 'border-yellow-100'],
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
                                @if($record->status == 'present') 
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                @elseif(in_array($record->status, ['late', 'permission'])) 
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                @elseif($record->status == 'absent') 
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                @else 
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="w-full text-center py-8 text-gray-400 bg-gray-50 rounded-xl border border-dashed border-gray-200">No attendance history available.</div>
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