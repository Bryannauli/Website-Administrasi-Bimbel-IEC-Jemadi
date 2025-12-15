<div x-show="showHistoryModal" style="display: none;" 
     class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
    
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        
        {{-- BACKDROP --}}
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="showHistoryModal = false"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        {{-- MODAL CONTENT --}}
        <div x-data="{ isCreating: false }" 
             class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
            
            {{-- HEADER --}}
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 border-b border-gray-100 flex justify-between items-center sticky top-0 z-10">
                <div>
                    {{-- Ubah Judul Dinamis --}}
                    <h3 class="text-lg leading-6 font-bold text-gray-900" x-text="isCreating ? 'New Session Details' : 'Teaching Logs'"></h3>
                    <p class="text-sm text-gray-500 mt-1">
                        <span x-show="!isCreating">Activity history for <strong>{{ $class->name }}</strong>.</span>
                        <span x-show="isCreating">Fill in the details below.</span>
                    </p>
                </div>
                
                {{-- Tombol X di Pojok Kanan Atas (Selalu Menutup Modal) --}}
                <button @click="showHistoryModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div class="max-h-[70vh] overflow-y-auto custom-scrollbar bg-gray-50 p-6">
                
                {{-- 1. TOMBOL "CREATE NEW" (Hanya muncul jika TIDAK sedang membuat) --}}
                <button @click="isCreating = true" 
                    x-show="!isCreating"
                    x-transition
                    class="w-full py-3 mb-6 bg-white border-2 border-dashed border-blue-300 rounded-xl text-blue-600 font-bold hover:bg-blue-50 hover:border-blue-400 transition-all flex items-center justify-center gap-2 group shadow-sm">
                    <span class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </span>
                    Create New Session
                </button>

                {{-- 2. FORM CREATE SESSION (INLINE) --}}
                <div x-show="isCreating" x-transition class="bg-white p-5 rounded-xl border border-blue-200 shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-16 h-16 bg-blue-50 rounded-bl-full -mr-4 -mt-4 z-0"></div>

                    <div class="relative z-10">
                        <form action="{{ route('teacher.classes.session.store', $class->id) }}" method="POST">
                            @csrf
                            <div class="space-y-4">
                                {{-- Date Input --}}
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Session Date</label>
                                    <input type="date" name="date" 
                                           class="w-full rounded-lg border-gray-300 text-gray-700 text-sm focus:ring-blue-500 focus:border-blue-500 shadow-sm" 
                                           value="{{ date('Y-m-d') }}">
                                </div>

                                {{-- Topic Input --}}
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Topic / Material <span class="text-red-500">*</span></label>
                                    <textarea name="topics" rows="2" required 
                                              class="w-full rounded-lg border-gray-300 text-gray-700 text-sm focus:ring-blue-500 focus:border-blue-500 shadow-sm placeholder-gray-400" 
                                              placeholder="e.g. Introduction to Grammar..."></textarea>
                                </div>

                                {{-- Info Box --}}
                                <div class="flex gap-3 bg-yellow-50 p-3 rounded-lg border border-yellow-100 items-start">
                                    <svg class="w-5 h-5 text-yellow-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <p class="text-xs text-yellow-700 leading-snug mt-0.5">
                                        You will be marked as <strong>Present</strong> automatically.
                                    </p>
                                </div>

                                {{-- ACTION BUTTONS (Disini letak Cancel & Save) --}}
                                <div class="flex gap-3 pt-2">
                                    {{-- Tombol Cancel (Hanya kembali ke list, tidak tutup modal) --}}
                                    <button type="button" @click="isCreating = false" 
                                            class="flex-1 py-2.5 bg-white border border-gray-300 text-gray-700 font-bold rounded-lg hover:bg-gray-50 transition-colors text-sm">
                                        Cancel
                                    </button>

                                    {{-- Tombol Submit --}}
                                    <button type="submit" class="flex-1 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-md transition-colors text-sm flex justify-center items-center gap-2">
                                        Start Session
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- 3. LIST HISTORY (Hanya muncul jika TIDAK sedang membuat) --}}
                <div x-show="!isCreating" x-transition>
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">History Timeline</h4>
                    
                    @if($classSessions->isEmpty())
                        <div class="text-center py-8 text-gray-400 bg-white rounded-xl border border-dashed border-gray-300">
                            <p class="text-sm">No teaching logs found.</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($classSessions as $session)
                                @php
                                    $records = $session->records ?? collect([]);
                                    $totalRec = $records->count();
                                    $presentRec = $records->whereIn('status', ['present', 'late'])->count();
                                    $perc = $totalRec > 0 ? round(($presentRec / $totalRec) * 100) : 0;
                                    
                                    $percColor = $perc >= 80 ? 'bg-green-100 text-green-800' : 
                                                ($perc >= 50 ? 'bg-yellow-100 text-yellow-800' : 
                                                'bg-red-100 text-red-800');
                                                
                                    $teacherName = $session->teacher->name ?? 'Unknown';
                                @endphp

                                <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="flex items-center gap-3">
                                            <div class="bg-blue-100 text-blue-700 font-bold px-3 py-1.5 rounded-lg text-center">
                                                <span class="block text-xs uppercase">{{ \Carbon\Carbon::parse($session->date)->format('M') }}</span>
                                                <span class="block text-xl leading-none">{{ \Carbon\Carbon::parse($session->date)->format('d') }}</span>
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                                                    {{ $teacherName }}
                                                    @if($session->teacher_id == auth()->id())
                                                        <span class="text-[9px] bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded border border-gray-200 font-normal">You</span>
                                                    @endif
                                                </h4>
                                                <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($session->date)->format('l') }}</span>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $percColor }}">
                                                {{ $perc }}% Present
                                            </span>
                                        </div>
                                    </div>

                                    <div class="mt-3 pl-[72px]">
                                        <p class="text-sm text-gray-600 bg-gray-50 p-3 rounded-lg italic border border-gray-100">
                                            "{{ $session->comment ?? 'No topic recorded.' }}"
                                        </p>
                                        <div class="mt-2 text-right">
                                            <a href="{{ route('teacher.classes.session.detail', ['classId' => $class->id, 'sessionId' => $session->id]) }}" 
                                                class="inline-flex items-center text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline transition-colors gap-1">
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
            </div>
            
            {{-- FOOTER MODAL (Tombol Close) --}}
            {{-- HANYA MUNCUL JIKA TIDAK SEDANG MEMBUAT SESI --}}
            <div x-show="!isCreating" class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100">
                <button @click="showHistoryModal = false" class="w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>