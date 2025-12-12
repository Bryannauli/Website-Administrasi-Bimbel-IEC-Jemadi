<div x-show="showAddStudentModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="showAddStudentModal = false"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full" x-data="{ selectedStudents: [] }">
            {{-- Search Form --}}
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 border-b border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg leading-6 font-bold text-gray-900">Enroll Students</h3>
                    <button @click="showAddStudentModal = false" class="text-gray-400 hover:text-gray-500"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                </div>
                <form action="{{ route('admin.classes.detailclass', $class->id) }}" method="GET" class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg></div>
                    <input type="text" name="search_student" value="{{ request('search_student') }}" class="block w-full pl-10 pr-20 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:bg-white focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Search Name/ID & Enter...">
                    @if(request('search_student')) <a href="{{ route('admin.classes.detailclass', $class->id) }}" class="absolute inset-y-0 right-0 pr-3 flex items-center text-xs text-red-500 font-bold hover:underline">CLEAR</a> @endif
                </form>
            </div>
            {{-- Enroll Form --}}
            <form action="{{ route('admin.classes.assignStudent', $class->id) }}" method="POST">
                @csrf
                <div class="px-6 py-2 max-h-64 overflow-y-auto custom-scrollbar bg-gray-50">
                    @if($availableStudents->isEmpty())
                        <div class="py-8 text-center text-gray-500 flex flex-col items-center"><p class="text-sm">No available students found.</p></div>
                    @else
                        <ul class="divide-y divide-gray-100">
                            @foreach($availableStudents as $student)
                                <li class="py-3 flex items-center hover:bg-white -mx-2 px-2 rounded-lg transition cursor-pointer" @click="if(selectedStudents.includes('{{ $student->id }}')) selectedStudents = selectedStudents.filter(id => id !== '{{ $student->id }}'); else selectedStudents.push('{{ $student->id }}');">
                                    <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" x-model="selectedStudents" class="hidden">
                                    <div class="flex-shrink-0 h-5 w-5 rounded border flex items-center justify-center transition-colors" :class="selectedStudents.includes('{{ $student->id }}') ? 'bg-blue-600 border-blue-600' : 'border-gray-300 bg-white'">
                                        <svg x-show="selectedStudents.includes('{{ $student->id }}')" class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <div class="ml-3"><p class="text-sm font-medium text-gray-900">{{ $student->name }}</p><p class="text-xs text-gray-500 font-mono">{{ $student->student_number }}</p></div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                <div class="bg-white px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100">
                    <button type="submit" x-bind:disabled="selectedStudents.length === 0" :class="selectedStudents.length === 0 ? 'opacity-50 cursor-not-allowed' : ''" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition-all">Enroll Selected (<span x-text="selectedStudents.length"></span>)</button>
                    <button type="button" @click="showAddStudentModal = false" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>