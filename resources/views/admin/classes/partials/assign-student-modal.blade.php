<div x-show="showAddStudentModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="showAddStudentModal = false"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        {{-- Modal Content --}}
        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full" 
             x-data="{ 
                selectedStudents: [], 
                // Ambil semua ID siswa yang tampil di hasil pencarian saat ini untuk fitur Select All
                allIds: {{ $availableStudents->pluck('id') }},
                
                // Fungsi Toggle Select All
                toggleAll() {
                    if (this.selectedStudents.length === this.allIds.length) {
                        this.selectedStudents = []; // Uncheck All
                    } else {
                        this.selectedStudents = [...this.allIds]; // Check All
                    }
                }
             }">
            
            {{-- Header & Search --}}
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 border-b border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg leading-6 font-bold text-gray-900">Enroll Students</h3>
                    <button @click="showAddStudentModal = false" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                
                {{-- Form Pencarian --}}
                <form action="{{ route('admin.classes.detailclass', $class->id) }}" method="GET" class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>
                    <input type="text" name="search_student" value="{{ request('search_student') }}" 
                           class="block w-full pl-10 pr-20 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:bg-white focus:ring-blue-500 focus:border-blue-500 sm:text-sm shadow-sm" 
                           placeholder="Search Name or ID...">
                    
                    @if(request('search_student')) 
                        <a href="{{ route('admin.classes.detailclass', $class->id) }}" class="absolute inset-y-0 right-0 pr-3 flex items-center text-xs text-red-500 font-bold hover:underline">CLEAR</a> 
                    @endif
                </form>
            </div>

            {{-- Form Enroll --}}
            <form action="{{ route('admin.classes.assignStudent', $class->id) }}" method="POST">
                @csrf
                
                {{-- List Container --}}
                <div class="px-6 py-2 bg-gray-50 border-b border-gray-100">
                    
                    {{-- BULK ACTION BAR --}}
                    @if(!$availableStudents->isEmpty())
                    <div class="flex items-center justify-between py-2 mb-2 border-b border-gray-200">
                        <div class="flex items-center">
                            {{-- Checkbox Select All --}}
                            <input type="checkbox" 
                                   @click="toggleAll()" 
                                   :checked="selectedStudents.length > 0 && selectedStudents.length === allIds.length"
                                   class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 cursor-pointer">
                            <span class="ml-2 text-xs font-bold text-gray-600 uppercase tracking-wide">Select All</span>
                        </div>
                        <span class="text-xs text-gray-500 font-medium">
                            <span x-text="selectedStudents.length"></span> / {{ $availableStudents->count() }} Selected
                        </span>
                    </div>
                    @endif

                    {{-- Scrollable List --}}
                    <div class="max-h-64 overflow-y-auto custom-scrollbar">
                        @if($availableStudents->isEmpty())
                            <div class="py-8 text-center text-gray-500 flex flex-col items-center">
                                <svg class="w-10 h-10 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                <p class="text-sm">No available students found.</p>
                                @if(request('search_student'))
                                    <p class="text-xs text-gray-400 mt-1">Try different keywords.</p>
                                @endif
                            </div>
                        @else
                            <ul class="divide-y divide-gray-100">
                                @foreach($availableStudents as $student)
                                    <li class="py-2 flex items-center hover:bg-white -mx-2 px-2 rounded-lg transition cursor-pointer group" 
                                        @click="if(selectedStudents.includes('{{ $student->id }}')) selectedStudents = selectedStudents.filter(id => id !== '{{ $student->id }}'); else selectedStudents.push('{{ $student->id }}');">
                                        
                                        {{-- Checkbox Individual --}}
                                        <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" 
                                               x-model="selectedStudents" 
                                               class="hidden">
                                        
                                        {{-- Custom Checkbox UI --}}
                                        <div class="flex-shrink-0 h-5 w-5 rounded border flex items-center justify-center transition-colors shadow-sm" 
                                             :class="selectedStudents.includes('{{ $student->id }}') ? 'bg-blue-600 border-blue-600' : 'border-gray-300 bg-white group-hover:border-blue-400'">
                                            <svg x-show="selectedStudents.includes('{{ $student->id }}')" class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        </div>
                                        
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900 group-hover:text-blue-600 transition-colors">{{ $student->name }}</p>
                                            <p class="text-xs text-gray-500 font-mono">{{ $student->student_number }}</p>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>

                {{-- Footer Actions --}}
                <div class="bg-white px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100">
                    <button type="submit" 
                            x-bind:disabled="selectedStudents.length === 0" 
                            :class="selectedStudents.length === 0 ? 'opacity-50 cursor-not-allowed bg-gray-400' : 'bg-blue-600 hover:bg-blue-700 shadow-blue-200'"
                            class="w-full inline-flex justify-center items-center rounded-lg border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition-all">
                        <span>Enroll Selected</span>
                        <span x-show="selectedStudents.length > 0" class="ml-2 bg-white bg-opacity-20 px-2 py-0.5 rounded text-xs" x-text="selectedStudents.length"></span>
                    </button>
                    <button type="button" @click="showAddStudentModal = false" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>