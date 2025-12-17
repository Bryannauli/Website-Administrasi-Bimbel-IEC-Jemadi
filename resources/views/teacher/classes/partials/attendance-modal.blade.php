<div x-show="showStudentStatsModal" 
    x-init="$watch('showStudentStatsModal', value => {
        if (value) {
            // Tunggu elemen render ($nextTick) lalu scroll ke kanan
            $nextTick(() => {
                const container = document.getElementById('attendance-matrix-container');
                if(container) {
                    container.scrollLeft = container.scrollWidth;
                }
            });
        }
    })"
    style="display: none;" 
    class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
    
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="showStudentStatsModal = false"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        {{-- Modal Lebar (max-w-6xl) agar muat banyak kolom tanggal --}}
        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-6xl w-full">
            
            {{-- Header --}}
            <div class="bg-white px-6 py-4 border-b border-gray-100 flex justify-between items-center sticky top-0 z-50">
                <div>
                    <h3 class="text-lg leading-6 font-bold text-gray-900">Attendance Matrix</h3>
                    <p class="text-sm text-gray-500 mt-1">Recap of all {{ $allSessions->count() }} sessions (Oldest &rarr; Newest).</p>
                </div>
                <div class="flex items-center gap-4">
                    {{-- Legend Kecil --}}
                    <div class="hidden lg:flex items-center gap-4 text-xs text-gray-600 font-medium">
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-blue-600 shadow-sm"></span> Present</span>
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-yellow-500 shadow-sm"></span> Late</span>
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-purple-600 shadow-sm"></span> Sick</span>
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-emerald-600 shadow-sm"></span> Permit</span>
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-red-600 shadow-sm"></span> Absent</span>
                    </div>
                    <button @click="showStudentStatsModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>

            {{-- Container Scroll --}}
            <div id="attendance-matrix-container" class="max-h-[75vh] overflow-auto custom-scrollbar relative bg-white scroll-smooth">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 text-gray-500 text-xs font-bold uppercase border-b border-gray-200 sticky top-0 z-20 shadow-sm">
                        <tr>
                            {{-- Sticky Name Column --}}
                            <th class="px-4 py-3 bg-gray-50 sticky left-0 z-30 w-48 border-r border-gray-200 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)] align-bottom">
                                Student Name
                            </th>
                            <th class="px-2 py-3 text-center w-16 bg-gray-50 border-r border-gray-100 align-bottom">
                                Rate
                            </th>
                            
                            {{-- Loop Header Sesi (Sort Oldest -> Newest) --}}
                            @foreach($allSessions->sortBy('date') as $session)
                                <th class="px-2 py-2 text-center min-w-[80px] whitespace-nowrap bg-gray-50 align-top group hover:bg-gray-100 transition-colors">
                                    <div class="flex flex-col items-center justify-between h-full gap-1">
                                        <div class="flex flex-col items-center">
                                            <span class="text-[10px] text-gray-400 font-normal">{{ \Carbon\Carbon::parse($session->date)->format('D') }}</span>
                                            <span class="text-xs text-gray-800">{{ \Carbon\Carbon::parse($session->date)->format('d/m') }}</span>
                                        </div>
                                        
                                        @php
                                            // Handle Teacher Name (Optional in Teacher View, but good for consistency)
                                            $teacherName = $session->teacher->name ?? '-';
                                            $shortName = ($teacherName !== '-') ? explode(' ', trim($teacherName))[0] : '-';
                                        @endphp
                                        <div class="mt-1 px-1.5 py-0.5 rounded bg-blue-50 text-blue-600 text-[9px] border border-blue-100 truncate max-w-[70px]" title="{{ $teacherName }}">
                                            {{ $shortName }}
                                        </div>
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @foreach($studentStats as $stat)
                            {{-- LOGIC 1: Background Baris (TR) --}}
                            <tr class="transition group 
                                @if($stat->deleted_at) bg-gray-100 text-gray-500
                                @elseif(!$stat->is_active) bg-red-50 text-red-800
                                @else hover:bg-gray-50 text-gray-900
                                @endif">
                                
                                {{-- LOGIC 2: Background Sticky Column (TD) --}}
                                <td class="px-4 py-3 sticky left-0 z-10 border-r border-gray-100 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)] 
                                    @if($stat->deleted_at) bg-gray-100 group-hover:bg-gray-200
                                    @elseif(!$stat->is_active) bg-red-50 group-hover:bg-red-100
                                    @else bg-white group-hover:bg-gray-50
                                    @endif">
                                    
                                    <div class="flex flex-col justify-center h-full">
                                        {{-- 1. NAMA --}}
                                        <div class="truncate w-40 font-medium text-sm leading-tight {{ $stat->deleted_at ? 'text-gray-500' : ($stat->is_active ? 'text-gray-900' : 'text-red-800 line-through decoration-red-400') }}" 
                                             title="{{ $stat->name }}">
                                            {{ $stat->name }}
                                        </div>

                                        {{-- 2. STATUS TAG (Baris Baru) --}}
                                        @if($stat->deleted_at || !$stat->is_active)
                                            <div class="mt-1">
                                                @if($stat->deleted_at)
                                                    {{-- DELETED --}}
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-red-600 text-white border border-red-700 uppercase tracking-wide">
                                                        DELETED
                                                    </span>
                                                    <span class="text-[9px] text-gray-400 ml-1">
                                                        {{ \Carbon\Carbon::parse($stat->deleted_at)->format('d/m/y') }}
                                                    </span>
                                                @elseif(!$stat->is_active)
                                                    {{-- OUT --}}
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-white text-red-600 border border-red-200 uppercase tracking-wide">
                                                        OUT
                                                    </span>
                                                @endif
                                            </div>
                                        @endif

                                        {{-- 3. NOMOR SISWA --}}
                                        <div class="text-[10px] font-mono opacity-70 mt-0.5">
                                            {{ $stat->student_number }}
                                        </div>
                                    </div>
                                </td>

                                {{-- Rate % --}}
                                <td class="px-2 py-3 text-center border-r border-gray-100 opacity-90">
                                    <span class="text-xs font-bold {{ $stat->percentage >= 80 ? 'text-green-600' : ($stat->percentage >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                        {{ $stat->percentage }}%
                                    </span>
                                </td>

                                {{-- Loop Status Matrix --}}
                                @foreach($allSessions->sortBy('date') as $session)
                                    @php
                                        // Pastikan menggunakan ID yang benar untuk akses array
                                        $realStudentId = $stat->student_id; 
                                        
                                        $status = $attendanceMatrix[$realStudentId][$session->id] ?? '-';
                                        
                                        $cellContent = match($status) {
                                            'present' => '<span class="inline-flex w-6 h-6 items-center justify-center rounded-full bg-blue-600 text-white shadow-sm" title="Present"><svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></span>',
                                            'late' => '<span class="inline-flex w-6 h-6 items-center justify-center rounded-full bg-yellow-500 text-white font-bold text-[10px] shadow-sm" title="Late">L</span>',
                                            'sick' => '<span class="inline-flex w-6 h-6 items-center justify-center rounded-full bg-purple-600 text-white font-bold text-[10px] shadow-sm" title="Sick">S</span>',
                                            'permission' => '<span class="inline-flex w-6 h-6 items-center justify-center rounded-full bg-emerald-600 text-white font-bold text-[10px] shadow-sm" title="Permission">P</span>',
                                            'absent' => '<span class="inline-flex w-6 h-6 items-center justify-center rounded-full bg-red-600 text-white font-bold text-[10px] shadow-sm" title="Absent">A</span>',
                                            default => '<span class="text-gray-200 text-lg">&bull;</span>'
                                        };
                                    @endphp

                                    <td class="px-2 py-3 text-center border-r border-gray-100 last:border-r-0">
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
                        <p>No student data available.</p>
                    </div>
                @endif
            </div>

            <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-end">
                <button @click="showStudentStatsModal = false" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm transition">
                    Close Report
                </button>
            </div>
        </div> 
    </div>
</div>