<div x-show="showHistoryModal" style="display: none;" 
    class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
    
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        
        {{-- BACKDROP --}}
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="showHistoryModal = false"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        {{-- MODAL CONTENT --}}
        <div x-data="{ 
            isCreating: false,
            // Cek apakah sesi hari ini sudah ada
            sessionTodayExists: {{ isset($sessionToday) && $sessionToday ? 'true' : 'false' }},
            sessionTodayId: {{ isset($sessionToday) && $sessionToday ? $sessionToday->id : 'null' }},
        }" 
            class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
            
            {{-- HEADER (Sticky) --}}
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 border-b border-gray-100 flex justify-between items-center sticky top-0 z-20">
                <div>
                    <h3 class="text-lg leading-6 font-bold text-gray-900" x-text="isCreating ? 'New Session Details' : 'Teaching Logs'"></h3>
                    <p class="text-sm text-gray-500 mt-1">
                        <span x-show="!isCreating">Activity history for <strong>{{ $class->name }}</strong>.</span>
                        <span x-show="isCreating">Fill in the details below.</span>
                    </p>
                </div>
                
                <button @click="showHistoryModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            {{-- BODY DENGAN FITUR SCROLL --}}
            <div class="max-h-[65vh] overflow-y-auto custom-scrollbar bg-gray-50 p-6">
                
                {{-- 1. TOMBOL "CREATE NEW" (Hanya muncul jika belum ada sesi hari ini) --}}
                <template x-if="!sessionTodayExists">
                    <button @click="isCreating = true" 
                        x-show="!isCreating"
                        x-transition
                        class="w-full py-3 mb-6 bg-white border-2 border-dashed border-blue-300 rounded-xl text-blue-600 font-bold hover:bg-blue-50 hover:border-blue-400 transition-all flex items-center justify-center gap-2 group shadow-sm">
                        <span class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        </span>
                        Create New Session
                    </button>
                </template>
                
                {{-- 1B. ALERT JIKA SESI HARI INI SUDAH ADA --}}
                <template x-if="sessionTodayExists && !isCreating">
                    <div class="w-full py-4 mb-6 bg-yellow-50 rounded-xl border border-yellow-200 text-yellow-700 font-medium text-sm text-center shadow-sm">
                        Session for today already exists. <br>
                        <a :href="`{{ route('teacher.classes.session.detail', ['classId' => $class->id, 'sessionId' => 'SESID_PLACEHOLDER']) }}`.replace('SESID_PLACEHOLDER', sessionTodayId)"
                           class="text-yellow-800 font-bold hover:underline mt-1 inline-block">
                           Go to Edit Session
                        </a>
                    </div>
                </template>

                {{-- 2. LIST HISTORY --}}
                <div x-show="!isCreating" x-transition>
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">History Timeline</h4>
                    
                    {{-- [CATATAN]: $classSessions harus dikirim sebagai ->get() dari Controller agar scroll berfungsi --}}
                    @if($classSessions->isEmpty())
                        <div class="text-center py-12 text-gray-400 bg-white rounded-xl border border-dashed border-gray-300">
                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5S19.832 5.477 21 6.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            </div>
                            <p class="text-sm italic">No teaching logs found for this class.</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($classSessions as $session)
                                @php
                                    $perc = $session->attendance_percentage ?? 0;
                                    $percColor = $perc >= 80 ? 'bg-green-100 text-green-800 border-green-200' : 
                                                ($perc >= 50 ? 'bg-yellow-100 text-yellow-800 border-yellow-200' : 
                                                'bg-red-100 text-red-800 border-red-200');
                                @endphp

                                <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow group">
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="flex items-center gap-3">
                                            {{-- Date Badge --}}
                                            <div class="bg-blue-100 text-blue-700 font-bold px-3 py-1.5 rounded-lg text-center border border-blue-200">
                                                <span class="block text-[10px] uppercase leading-none mb-0.5">{{ \Carbon\Carbon::parse($session->date)->format('M') }}</span>
                                                <span class="block text-xl leading-none">{{ \Carbon\Carbon::parse($session->date)->format('d') }}</span>
                                            </div>
                                            {{-- Teacher Info --}}
                                            <div>
                                                <h4 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                                                    {{ $session->teacher_name ?? 'Unknown' }}
                                                    @if(isset($session->teacher_id) && $session->teacher_id == auth()->id())
                                                        <span class="text-[9px] bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded border border-gray-200 font-normal italic">You</span>
                                                    @endif
                                                </h4>
                                                <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($session->date)->format('l') }}</span>
                                            </div>
                                        </div>
                                        {{-- Perc Badge --}}
                                        <div class="text-right">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $percColor }}">
                                                {{ $perc }}% Attendance
                                            </span>
                                        </div>
                                    </div>

                                    <div class="mt-3 pl-[72px]">
                                        {{-- Comment Box --}}
                                        <div class="relative">
                                            <div class="absolute -left-4 top-0 bottom-0 w-0.5 bg-gray-100"></div>
                                            <p class="text-sm text-gray-600 bg-gray-50 p-3 rounded-lg italic border border-gray-100 leading-relaxed">
                                                "{{ $session->comment ?? 'No teaching notes provided for this session.' }}"
                                            </p>
                                        </div>
                                        
                                        {{-- Quick Actions --}}
                                        <div class="mt-3 flex justify-end">
                                            <a href="{{ route('teacher.classes.session.detail', ['classId' => $class->id, 'sessionId' => $session->session_id]) }}" 
                                                class="inline-flex items-center text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline transition-colors gap-1 group/btn">
                                                Review Attendance
                                                <svg class="w-3.5 h-3.5 transform group-hover/btn:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                                            </a>
                                        </div>
                                    </div> 
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- 3. FORM CREATE SESSION (INLINE) --}}
                <div x-show="isCreating" x-transition class="bg-white p-5 rounded-xl border border-blue-200 shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-16 h-16 bg-blue-50 rounded-bl-full -mr-4 -mt-4 z-0 opacity-50"></div>

                    <div class="relative z-10">
                        <form action="{{ route('teacher.classes.session.store', $class->id) }}" method="POST">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Session Date</label>
                                    <input type="date" name="date" 
                                           class="w-full rounded-lg border-gray-300 text-gray-700 text-sm focus:ring-blue-500 focus:border-blue-500 shadow-sm" 
                                           value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" required>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5">Topics / Materials <span class="text-red-500">*</span></label>
                                    <textarea name="topics" rows="3" required 
                                              class="w-full rounded-lg border-gray-300 text-gray-700 text-sm focus:ring-blue-500 focus:border-blue-500 shadow-sm placeholder-gray-400" 
                                              placeholder="e.g. Simple Present Tense, Page 45..."></textarea>
                                </div>

                                <div class="flex gap-3 bg-blue-50 p-3 rounded-lg border border-blue-100 items-start">
                                    <svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <p class="text-[11px] text-blue-700 leading-snug mt-0.5 italic">
                                        Note: You will be redirected to the attendance record page immediately after clicking 'Start Session'.
                                    </p>
                                </div>

                                <div class="flex gap-3 pt-2">
                                    <button type="button" @click="isCreating = false" 
                                            class="flex-1 py-2.5 bg-white border border-gray-300 text-gray-700 font-bold rounded-lg hover:bg-gray-50 transition-colors text-sm shadow-sm">
                                        Cancel
                                    </button>
                                    <button type="submit" class="flex-1 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-md transition-colors text-sm flex justify-center items-center gap-2">
                                        Start Session
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            {{-- FOOTER MODAL --}}
            <div x-show="!isCreating" class="bg-gray-50 px-4 py-3 sm:px-6 flex justify-end border-t border-gray-100">
                <button @click="showHistoryModal = false" class="inline-flex justify-center rounded-lg border border-gray-300 px-5 py-2 bg-white text-sm font-bold text-gray-700 hover:bg-gray-50 transition-colors shadow-sm">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>