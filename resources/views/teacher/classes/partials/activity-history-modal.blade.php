<div x-show="showHistoryModal" 
     style="display: none;" 
     class="fixed inset-0 z-50 overflow-y-auto" 
     role="dialog" 
     aria-modal="true">
    
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" 
             @click="showHistoryModal = false"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        {{-- Modal Panel --}}
        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
            
            {{-- Header --}}
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 border-b border-gray-100 flex justify-between items-center">
                <div>
                    <h3 class="text-lg leading-6 font-bold text-gray-900">Teaching Logs</h3>
                    <p class="text-sm text-gray-500 mt-1">Full history of class sessions.</p>
                </div>
                
                {{-- Tombol Close X --}}
                <button @click="showHistoryModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            {{-- Action Bar (FITUR BARU UNTUK TEACHER) --}}
            <div class="bg-blue-50 px-6 py-4 border-b border-blue-100 flex items-center justify-between">
                <div>
                    <h4 class="text-sm font-bold text-blue-900">New Session?</h4>
                    <p class="text-xs text-blue-700">Record today's attendance & topic.</p>
                </div>
                {{-- Tombol ini menutup modal history dan membuka modal create session --}}
                <button @click="showHistoryModal = false; showCreateSessionModal = true" 
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-sm gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Create Session
                </button>
            </div>

            {{-- List Content --}}
            <div class="max-h-[55vh] overflow-y-auto custom-scrollbar bg-gray-50 p-6">
                @if($classSessions->isEmpty())
                    <div class="text-center py-10 flex flex-col items-center justify-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <p class="text-gray-500 font-medium">No sessions recorded yet.</p>
                        <p class="text-xs text-gray-400 mt-1">Start by creating a new session above.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($classSessions as $session)
                            @php
                                // Logika Hitung Persentase Manual (karena ini Collection, bukan View SQL admin)
                                $total = $session->records->count();
                                $present = $session->records->whereIn('status', ['present', 'late'])->count();
                                $percentage = $total > 0 ? round(($present / $total) * 100) : 0;
                            @endphp

                            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow group">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex items-center gap-3">
                                        {{-- Date Badge --}}
                                        <div class="bg-white border border-gray-200 text-gray-700 font-bold px-3 py-1.5 rounded-lg text-center shadow-sm group-hover:border-blue-200 group-hover:bg-blue-50 group-hover:text-blue-700 transition-colors">
                                            <span class="block text-xs uppercase">{{ \Carbon\Carbon::parse($session->date)->format('M') }}</span>
                                            <span class="block text-xl leading-none">{{ \Carbon\Carbon::parse($session->date)->format('d') }}</span>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-gray-800 text-sm">{{ $session->teacher->name ?? 'Unknown' }}</h4> 
                                            <span class="text-xs text-gray-500 font-medium">{{ \Carbon\Carbon::parse($session->date)->format('l') }}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="text-right flex flex-col items-end gap-1">
                                        {{-- Attendance Percentage --}}
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide
                                            {{ $percentage >= 80 ? 'bg-green-100 text-green-700 border border-green-200' : ($percentage >= 50 ? 'bg-yellow-100 text-yellow-700 border border-yellow-200' : 'bg-red-100 text-red-700 border border-red-200') }}">
                                            {{ $percentage }}% Present
                                        </span>
                                        
                                        {{-- Link Detail (Icon Mata) --}}
                                        <a href="{{ route('teacher.classes.session.detail', [$class->id, $session->id]) }}" 
                                           class="text-xs text-blue-600 hover:text-blue-800 hover:underline font-bold flex items-center gap-1 mt-1">
                                            Details <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                        </a>
                                    </div>
                                </div>
                                <div class="mt-2 pl-[70px]">
                                    <p class="text-sm text-gray-600 bg-gray-50 p-3 rounded-lg border border-gray-100 italic">
                                        "{{ $session->comment ?? 'No topic recorded.' }}"
                                    </p>
                                </div> 
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Footer --}}
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100">
                <button @click="showHistoryModal = false" class="w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>