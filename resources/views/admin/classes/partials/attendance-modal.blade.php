{{-- PERUBAHAN 1: Tambahkan x-init untuk watch state modal --}}
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

            {{-- PERUBAHAN 2: Tambahkan ID pada container scroll --}}
            <div id="attendance-matrix-container" class="max-h-[75vh] overflow-auto custom-scrollbar relative bg-white">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 text-gray-500 text-xs font-bold uppercase border-b border-gray-200 sticky top-0 z-20 shadow-sm">
                        <tr>
                            {{-- Kolom Nama (Sticky Kiri) --}}
                            <th class="px-4 py-3 bg-gray-50 sticky left-0 z-30 w-48 border-r border-gray-200 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)] align-bottom">
                                Student Name
                            </th>
                            {{-- Kolom Summary % --}}
                            <th class="px-2 py-3 text-center w-16 bg-gray-50 border-r border-gray-100 align-bottom">
                                Rate
                            </th>
                            {{-- Loop Header Tanggal & Guru --}}
                            @foreach($teachingLogs as $session)
                                <th class="px-2 py-2 text-center min-w-[80px] whitespace-nowrap bg-gray-50 align-top group hover:bg-gray-100 transition-colors">
                                    <div class="flex flex-col items-center justify-between h-full gap-1">
                                        {{-- Tanggal --}}
                                        <div class="flex flex-col items-center">
                                            <span class="text-[10px] text-gray-400 font-normal">{{ \Carbon\Carbon::parse($session->date)->format('D') }}</span>
                                            <span class="text-xs text-gray-800">{{ \Carbon\Carbon::parse($session->date)->format('d/m') }}</span>
                                        </div>
                                        
                                        {{-- Nama Guru --}}
                                        @php
                                            $teacherName = $session->teacher_name ?? '-'; 
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
                            <tr class="transition group {{ $stat->is_active ? 'hover:bg-gray-50' : 'bg-red-50 hover:bg-red-100' }}">
                                
                                {{-- LOGIC 2: Background Sticky Column (TD) --}}
                                {{-- Perhatikan class bg-white diganti logic --}}
                                <td class="px-4 py-3 sticky left-0 z-10 border-r border-gray-100 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)] 
                                    {{ $stat->is_active ? 'bg-white group-hover:bg-gray-50' : 'bg-red-50 group-hover:bg-red-100' }}">
                                    
                                    {{-- Nama & Nomor Siswa --}}
                                    <div class="truncate w-40 {{ $stat->is_active ? 'text-gray-900 font-medium' : 'text-red-800 line-through decoration-red-400' }}" title="{{ $stat->name }}">
                                        {{ $stat->name }}
                                        
                                        {{-- Opsional: Label Quit --}}
                                        @if(!$stat->is_active)
                                            <span class="ml-1 text-[9px] text-red-600 bg-white border border-red-200 px-1 rounded">QUIT</span>
                                        @endif
                                    </div>
                                    <div class="text-[10px] font-mono {{ $stat->is_active ? 'text-gray-400' : 'text-red-400' }}">
                                        {{ $stat->student_number }}
                                    </div>
                                </td>

                                {{-- Rate % --}}
                                <td class="px-2 py-3 text-center border-r border-gray-100 {{ $stat->is_active ? 'bg-gray-50/30' : 'bg-red-50/30' }}">
                                    <span class="text-xs font-bold {{ $stat->percentage >= 80 ? 'text-green-600' : ($stat->percentage >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                        {{ $stat->percentage }}%
                                    </span>
                                </td>

                                {{-- Loop Status Per Tanggal --}}
                                @foreach($teachingLogs as $session)
                                    @php
                                        $status = $attendanceMatrix[$stat->student_id][$session->session_id] ?? '-';
                                        
                                        // (Bagian Logic Icon tetap sama seperti sebelumnya)
                                        $cellContent = match($status) {
                                            'present' => '<span class="inline-flex w-6 h-6 items-center justify-center rounded-full bg-blue-600 text-white shadow-sm" title="Present"><svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></span>',
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

        {{-- Footer dengan Tombol Print & Close --}}
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-end items-center gap-3">
                
                {{-- Tombol Print Report (Baru) --}}
                <a href="" 
                target="_blank"
                class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm transition flex items-center gap-2 decoration-0">
                    
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 00-2 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Print Report
                </a>

                {{-- Tombol Close Report (Lama) --}}
                <button @click="showStudentStatsModal = false" 
                        class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm transition">
                    Close Report
                </button>
            </div>
        </div> 
</div>