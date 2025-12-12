<div x-show="showAssignTeacherModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="showAssignTeacherModal = false"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full"
             x-data="{ 
                searchTeacher: '',
                selectedTeacherId: null,
                selectedTeacherName: ''
             }">
            
            {{-- Header --}}
            <div class="bg-white px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900">
                    Assign <span x-text="assignTeacherRole === 'form' ? 'Form' : 'Local'"></span> Teacher
                </h3>
                <button @click="showAssignTeacherModal = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form action="{{ route('admin.classes.assignTeacher', $class->id) }}" method="POST">
                @csrf
                @method('PATCH')
                
                <input type="hidden" name="type" :value="assignTeacherRole">
                <input type="hidden" name="teacher_id" :value="selectedTeacherId">

                <div class="p-6 bg-gray-50 space-y-4">
                    {{-- SEARCH INPUT --}}
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </div>
                        <input type="text" x-model="searchTeacher" 
                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg bg-white focus:ring-blue-500 focus:border-blue-500 sm:text-sm shadow-sm" 
                               placeholder="Search teacher name...">
                    </div>

                    {{-- TEACHER LIST --}}
                    <div class="max-h-60 overflow-y-auto custom-scrollbar border border-gray-200 rounded-lg bg-white">
                        <ul class="divide-y divide-gray-100">
                            @foreach($teachers as $teacher)
                                <li x-show="'{{ strtolower(addslashes($teacher->name)) }}'.includes(searchTeacher.toLowerCase())"
                                    @click="selectedTeacherId = '{{ $teacher->id }}'; selectedTeacherName = '{{ addslashes($teacher->name) }}'"
                                    :class="selectedTeacherId == '{{ $teacher->id }}' ? 'bg-blue-50 border-l-4 border-blue-600' : 'hover:bg-gray-50'"
                                    class="px-4 py-3 cursor-pointer transition-all flex items-center justify-between group">
                                    
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600 group-hover:bg-blue-100 group-hover:text-blue-600 transition-colors">
                                            {{ substr($teacher->name, 0, 1) }}
                                        </div>
                                        <span class="text-sm font-medium text-gray-700" :class="selectedTeacherId == '{{ $teacher->id }}' ? 'text-blue-700 font-bold' : ''">
                                            {{ $teacher->name }}
                                        </span>
                                    </div>

                                    <svg x-show="selectedTeacherId == '{{ $teacher->id }}'" class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- SELECTED INFO FEEDBACK --}}
                    <div x-show="selectedTeacherId" class="p-3 bg-blue-50 rounded-lg border border-blue-100 flex items-center gap-2" x-transition>
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="text-xs text-blue-700 font-medium">
                            Selected: <span class="font-bold" x-text="selectedTeacherName"></span>
                        </p>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 bg-white border-t border-gray-100 flex justify-end gap-3">
                    <button type="button" @click="showAssignTeacherModal = false" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="submit" 
                            :disabled="!selectedTeacherId"
                            :class="!selectedTeacherId ? 'opacity-50 cursor-not-allowed bg-gray-400' : 'bg-blue-600 hover:bg-blue-700'"
                            class="px-6 py-2 text-white rounded-lg text-sm font-bold transition shadow-sm">
                        Confirm
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>