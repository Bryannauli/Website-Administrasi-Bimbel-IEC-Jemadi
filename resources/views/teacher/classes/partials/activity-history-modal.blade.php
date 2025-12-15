<div x-show="showHistoryModal" style="display: none;" 
     class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
    
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        
        {{-- BACKDROP --}}
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="showHistoryModal = false"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        {{-- MODAL CONTENT --}}
        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
            
            {{-- HEADER --}}
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 border-b border-gray-100 flex justify-between items-center sticky top-0 z-10">
                <div>
                    <h3 class="text-lg leading-6 font-bold text-gray-900">Teaching Logs</h3>
                    <p class="text-sm text-gray-500 mt-1">Activity history & attendance.</p>
                </div>
                <button @click="showHistoryModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            {{-- CONTENT --}}
            <div class="max-h-[60vh] overflow-y-auto custom-scrollbar bg-gray-50 p-6">
                
                {{-- TOMBOL CREATE SESSION (KHUSUS TEACHER) --}}
                <button @click="showHistoryModal = false; showCreateSessionModal = true" 
                    class="w-full py-3 mb-6 bg-white border-2 border-dashed border-blue-300 rounded-xl text-blue-600 font-bold hover:bg-blue-50 hover:border-blue-400 transition-all flex items-center justify-center gap-2 group">
                    <span class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </span>
                    Create New Session
                </button>

                @if($classSessions->isEmpty())
                    <div class="text-center py-10 text-gray-500">No teaching logs found.</div>
                @else
                    <div class="space-y-4">
                        @foreach($classSessions as $session)
                            @php
                                // Kalkulasi Manual (Karena Teacher pakai Eloquent, bukan View DB Flat)
                                $records = $session->records ?? collect([]);
                                $totalRec = $records->count();
                                $presentRec = $records->whereIn('status', ['present', 'late'])->count();
                                $perc = $totalRec > 0 ? round(($presentRec / $totalRec) * 100) : 0;
                                
                                // Logic Warna Badge
                                $percColor = $perc >= 80 ? 'bg-green-100 text-green-800' : 
                                            ($perc >= 50 ? 'bg-yellow-100 text-yellow-800' : 
                                            'bg-red-100 text-red-800');
                                            
                                $teacherName = $session->teacher->name ?? 'Unknown';
                            @endphp

                            {{-- CARD STYLE: MIRIP ADMIN --}}
                            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                                
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex items-center gap-3">
                                        {{-- TANGGAL (STYLE ADMIN) --}}
                                        <div class="bg-blue-50 text-blue-700 font-bold px-3 py-1.5 rounded-lg text-center">
                                            <span class="block text-xs uppercase">{{ \Carbon\Carbon::parse($session->date)->format('M') }}</span>
                                            <span class="block text-xl leading-none">{{ \Carbon\Carbon::parse($session->date)->format('d') }}</span>
                                        </div>
                                        
                                        <div>
                                            {{-- NAMA GURU --}}
                                            <h4 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                                                {{ $teacherName }}
                                                @if($session->teacher_id == auth()->id())
                                                    <span class="text-[9px] bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded border border-gray-200 font-normal">You</span>
                                                @endif
                                            </h4>
                                            <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($session->date)->format('l') }}</span>
                                        </div>
                                    </div>

                                    {{-- BADGE PERSENTASE (STYLE ADMIN) --}}
                                    <div class="text-right">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $percColor }}">
                                            {{ $perc }}% Present
                                        </span>
                                    </div>
                                </div>

                                {{-- COMMENT & BUTTON AREA --}}
                                <div class="mt-3 pl-[72px]">
                                    <p class="text-sm text-gray-600 bg-gray-50 p-3 rounded-lg italic border border-gray-100">
                                        "{{ $session->comment ?? 'No topic recorded.' }}"
                                    </p>
                                    
                                    {{-- TOMBOL MANAGE ATTENDANCE (KHUSUS TEACHER) --}}
                                    <div class="mt-2 text-right">
                                        {{-- Pastikan route ini ada di web.php, misalnya: route('teacher.classes.session.show', [...]) --}}
                                        {{-- Jika belum ada route khusus edit, arahkan ke detail kelas atau biarkan '#' --}}
                                        <a href="#" class="inline-flex items-center text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline transition-colors gap-1">
                                            Manage Attendance
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                                        </a>
                                    </div>
                                </div> 
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            
            {{-- FOOTER --}}
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100">
                <button @click="showHistoryModal = false" class="w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>