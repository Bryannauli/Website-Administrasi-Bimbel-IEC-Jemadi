<x-app-layout>
    <x-slot name="header"></x-slot>

    {{-- x-data UTAMA --}}
    <div class="py-6" x-data="{ 
        showAddModal: false, 
        showEditModal: false,
        
        // Data form untuk Edit
        editForm: {
            id: null,
            category: '',
            name: '',
            classroom: '',
            start_month: '',
            end_month: '',
            academic_year: '',
            form_teacher_id: '',  
            local_teacher_id: '', 
            days: [], // Array untuk Checkbox Hari
            time_start: '',
            time_end: '',
            status: 'active'
        },

        updateUrl: '{{ route('admin.classes.update', ':id') }}',
        
        getUpdateUrl() {
            return this.editForm.id ? this.updateUrl.replace(':id', this.editForm.id) : '#';
        },

        openEditModal(data) {
            this.editForm.id = data.id;
            this.editForm.category = data.category;
            this.editForm.name = data.name;
            this.editForm.classroom = data.classroom || ''; 
            this.editForm.start_month = data.start_month; 
            this.editForm.end_month = data.end_month;     
            this.editForm.academic_year = data.academic_year;
            
            // Mapping Guru (Handle Null)
            this.editForm.form_teacher_id = data.form_teacher_id || ''; 
            this.editForm.local_teacher_id = data.local_teacher_id || ''; 

            // Mapping Jadwal (Ambil dari relasi schedules)
            this.editForm.days = data.schedules ? data.schedules.map(item => item.day_of_week) : [];

            this.editForm.time_start = data.start_time; 
            this.editForm.time_end = data.end_time; 
            this.editForm.status = data.is_active ? 'active' : 'inactive'; 
            
            this.showEditModal = true;
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Header & Title --}}
            <div class="mb-8">
                <div class="flex items-center gap-2 text-sm font-medium text-gray-500 mb-2">
                    <a href="{{ route('dashboard') }}" class="hover:text-gray-900">Dashboard</a>
                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                    <span class="text-gray-900 font-medium">Classes</span>
                </div>
                <h1 class="text-3xl font-bold bg-gradient-to-b from-blue-500 to-red-500 bg-clip-text text-transparent">
                    Classes
                </h1>
            </div>

            {{-- Action Bar --}}
            <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
                <div class="flex items-center gap-3 w-full md:w-auto">
                    <button @click="showAddModal = true" class="inline-flex items-center px-4 py-2 bg-blue-700 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-sm transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        Add New Class
                    </button>
                </div>

                <div class="relative w-full md:w-72">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg></div>
                    <form action="{{ route('admin.classes.index') }}" method="GET">
                        <input type="text" name="search" value="{{ request('search') }}" class="block w-full pl-10 pr-3 py-2 border border-gray-200 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Search">
                    </form>
                </div>
            </div>

            {{-- Table --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50 text-xs text-gray-400 font-medium uppercase border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4 w-16 font-normal text-center">No</th>
                                <th class="px-6 py-4 font-normal">Category</th>
                                <th class="px-6 py-4 font-normal">Class Name</th>
                                <th class="px-6 py-4 font-normal">Classroom</th>
                                <th class="px-6 py-4 font-normal">Schedule</th>
                                <th class="px-6 py-4 font-normal">Teacher</th>
                                <th class="px-6 py-4 font-normal">Status</th>
                                <th class="px-6 py-4 font-normal text-center w-32">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                            @forelse($classes as $index => $class)
                                <tr class="hover:bg-gray-50 transition-colors group">
                                    <td class="px-6 py-5 text-center text-gray-500">{{ $classes->firstItem() + $index }}</td>
                                    
                                    <td class="px-6 py-5 text-gray-600 capitalize">
                                        {{ str_replace('_', ' ', $class->category ?? '-') }}
                                    </td>
                                    
                                    <td class="px-6 py-5 font-medium text-gray-900">{{ $class->name }}</td>
                                    
                                    <td class="px-6 py-5 text-gray-600">{{ $class->classroom }}</td>
                                    
                                    {{-- Schedule (Hari & Jam) --}}
                                    <td class="px-6 py-5 text-gray-600">
                                        <div class="flex flex-col gap-1">
                                            <span class="font-medium text-gray-800">
                                                @if($class->schedules->isNotEmpty())
                                                    {{ $class->schedules->pluck('day_of_week')->implode(', ') }}
                                                @else
                                                    <span class="text-gray-400 italic">-</span>
                                                @endif
                                            </span>
                                            <span class="text-xs text-blue-600 bg-blue-50 px-2 py-1 rounded-md w-fit">
                                                {{ \Carbon\Carbon::parse($class->start_time)->format('H:i') }} - 
                                                {{ \Carbon\Carbon::parse($class->end_time)->format('H:i') }}
                                            </span>
                                        </div>
                                    </td>

                                    {{-- Teacher (Menampilkan Nama Guru dari Relasi) --}}
                                    <td class="px-6 py-5 text-gray-600 text-xs">
                                        <div><span class="font-semibold">Form:</span> {{ $class->formTeacher->name ?? '-' }}</div>
                                        <div><span class="font-semibold">Local:</span> {{ $class->localTeacher->name ?? '-' }}</div>
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-6 py-5">
                                        @if($class->is_active)
                                            <span class="px-3 py-1 bg-purple-100 text-purple-600 rounded-md text-xs font-medium capitalize inline-block">Active</span>
                                        @else
                                            <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-md text-xs font-medium capitalize inline-block">Inactive</span>
                                        @endif
                                    </td>

                                    {{-- Action Buttons --}}
                                    <td class="px-6 py-5">
                                        <div class="flex items-center justify-center gap-4 ">
                                             <a href="#" class="text-gray-400 hover:text-blue-600 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                            </a>
                                            <button class="text-gray-400 hover:text-red-600 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                            <button type="button" @click='openEditModal(@json($class))' class="text-gray-400 hover:text-green-600 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="px-6 py-10 text-center text-gray-500 bg-gray-50">No classes found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                {{-- PAGINATION MANUAL --}}
                <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between bg-white">
                    @if ($classes->onFirstPage())
                        <button class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-400 bg-white cursor-not-allowed" disabled>Previous</button>
                    @else
                        <a href="{{ $classes->previousPageUrl() }}" class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-600 bg-white hover:bg-gray-50 hover:text-gray-800 transition-colors">Previous</a>
                    @endif

                    <span class="text-sm text-gray-500">Page {{ $classes->currentPage() }} of {{ $classes->lastPage() }}</span>

                    @if ($classes->hasMorePages())
                        <a href="{{ $classes->nextPageUrl() }}" class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-600 bg-white hover:bg-gray-50 hover:text-gray-800 transition-colors">Next</a>
                    @else
                        <button class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-400 bg-white cursor-not-allowed" disabled>Next</button>
                    @endif
                </div>
            </div>

            {{-- MODAL ADD NEW CLASS --}}
            <div x-show="showAddModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="showAddModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="showAddModal = false"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                    <div x-show="showAddModal" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl w-full">
                        <div class="px-8 py-6 flex justify-between items-center border-b border-gray-100">
                            <h3 class="text-xl font-bold text-gray-900">Add new Class</h3>
                            <button @click="showAddModal = false" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                        </div>
                        <div class="px-8 py-6">
                            <form action="{{ route('admin.classes.store') }}" method="POST">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Category</label>
                                        <select name="category" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-gray-700 bg-white" required>
                                            <option value="">Select Category</option>
                                            <option value="pre_level">Pre-level</option>
                                            <option value="level">Level</option>
                                            <option value="step">Step</option>
                                            <option value="private">Private</option>
                                        </select>
                                    </div>
                                    <div><label class="block text-sm font-bold text-gray-700 mb-2">Class Name</label><input type="text" name="name" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-gray-700" required></div>
                                    <div><label class="block text-sm font-bold text-gray-700 mb-2">Classroom</label><input type="text" name="classroom" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-gray-700" required></div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Academic Year</label>
                                        <select name="academic_year" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-gray-700 bg-white">
                                            <option value="2025">2025</option>
                                            <option value="2026">2026</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Months</label>
                                        <div class="flex gap-2">
                                            <select name="start_month" class="w-1/2 border border-gray-200 rounded-lg px-2 py-2.5 text-gray-700 bg-white">@foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $m)<option value="{{$m}}">{{$m}}</option>@endforeach</select>
                                            <select name="end_month" class="w-1/2 border border-gray-200 rounded-lg px-2 py-2.5 text-gray-700 bg-white">@foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $m)<option value="{{$m}}">{{$m}}</option>@endforeach</select>
                                        </div>
                                    </div>

                                    {{-- DYNAMIC TEACHERS (OPTIONAL) --}}
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Form Teacher</label>
                                        <select name="form_teacher_id" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-gray-700 bg-white">
                                            <option value="">Select (Optional)</option>
                                            @foreach($teachers as $teacher)
                                                <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Local Teacher</label>
                                        <select name="local_teacher_id" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-gray-700 bg-white">
                                            <option value="">Select (Optional)</option>
                                            @foreach($teachers as $teacher)
                                                <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Days & Time --}}
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Days</label>
                                        <div class="flex flex-wrap gap-4 mt-1 p-3 bg-gray-50 rounded-lg border border-gray-100">
                                            @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                                                <label class="inline-flex items-center cursor-pointer"><input type="checkbox" name="days[]" value="{{ $day }}" class="w-5 h-5 text-blue-600 rounded"><span class="ml-2 text-gray-700 text-sm">{{ $day }}</span></label>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Time</label>
                                        <div class="flex gap-4">
                                            <input type="time" name="start_time" class="w-full border border-gray-200 rounded-lg px-3 py-2" required>
                                            <input type="time" name="end_time" class="w-full border border-gray-200 rounded-lg px-3 py-2" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex justify-end mt-8 pt-4 border-t border-gray-100">
                                    <button type="submit" class="px-8 py-2.5 bg-blue-700 hover:bg-blue-800 text-white font-bold rounded-lg shadow-md transition-colors text-sm">Save all</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- MODAL EDIT CLASS --}}
            <div x-show="showEditModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="showEditModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="showEditModal = false"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                    <div x-show="showEditModal" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl w-full">
                        <div class="px-8 py-6 flex justify-between items-center border-b border-gray-100">
                            <h3 class="text-xl font-bold text-gray-900">Edit Class</h3>
                            <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                        </div>
                        <div class="px-8 py-6">
                            <form :action="getUpdateUrl()" method="POST"> 
                                @csrf
                                @method('PUT')
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                    {{-- Inputs Lainnya... --}}
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Category</label>
                                        <select name="category" x-model="editForm.category" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-gray-700 bg-white">
                                            <option value="pre_level">Pre-level</option>
                                            <option value="level">Level</option>
                                            <option value="step">Step</option>
                                            <option value="private">Private</option>
                                        </select>
                                    </div>
                                    <div><label class="block text-sm font-bold text-gray-700 mb-2">Class Name</label><input type="text" name="name" x-model="editForm.name" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-gray-700"></div>
                                    <div><label class="block text-sm font-bold text-gray-700 mb-2">Classroom</label><input type="text" name="classroom" x-model="editForm.classroom" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-gray-700"></div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Academic Year</label>
                                        <select name="academic_year" x-model="editForm.academic_year" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-gray-700 bg-white">
                                            <option value="2025">2025</option>
                                            <option value="2026">2026</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Months</label>
                                        <div class="flex gap-2">
                                            <select name="start_month" x-model="editForm.start_month" class="w-1/2 border border-gray-200 rounded-lg px-2 py-2.5 text-gray-700 bg-white">@foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $m)<option value="{{$m}}">{{$m}}</option>@endforeach</select>
                                            <select name="end_month" x-model="editForm.end_month" class="w-1/2 border border-gray-200 rounded-lg px-2 py-2.5 text-gray-700 bg-white">@foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $m)<option value="{{$m}}">{{$m}}</option>@endforeach</select>
                                        </div>
                                    </div>

                                    {{-- DYNAMIC TEACHERS (EDIT) --}}
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Form Teacher</label>
                                        <select name="form_teacher_id" x-model="editForm.form_teacher_id" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-gray-700 bg-white">
                                            <option value="">Select (Optional)</option>
                                            @foreach($teachers as $teacher)
                                                <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Local Teacher</label>
                                        <select name="local_teacher_id" x-model="editForm.local_teacher_id" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-gray-700 bg-white">
                                            <option value="">Select (Optional)</option>
                                            @foreach($teachers as $teacher)
                                                <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Days & Time --}}
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Days</label>
                                        <div class="flex flex-wrap gap-4 mt-1 p-3 bg-gray-50 rounded-lg border border-gray-100">
                                            @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                                                <label class="inline-flex items-center cursor-pointer"><input type="checkbox" name="days[]" value="{{ $day }}" x-model="editForm.days" class="w-5 h-5 text-blue-600 rounded"><span class="ml-2 text-gray-700 text-sm">{{ $day }}</span></label>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Time</label>
                                        <div class="flex gap-4">
                                            <input type="time" name="start_time" x-model="editForm.time_start" class="w-full border border-gray-200 rounded-lg px-3 py-2">
                                            <input type="time" name="end_time" x-model="editForm.time_end" class="w-full border border-gray-200 rounded-lg px-3 py-2">
                                        </div>
                                    </div>
                                    
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Status</label>
                                        <div class="flex gap-6 mt-3">
                                            <label class="inline-flex items-center cursor-pointer"><input type="radio" name="status" value="active" x-model="editForm.status" class="text-blue-600"><span class="ml-2 text-gray-700 font-medium">Active</span></label>
                                            <label class="inline-flex items-center cursor-pointer"><input type="radio" name="status" value="inactive" x-model="editForm.status" class="text-blue-600"><span class="ml-2 text-gray-700 font-medium">Inactive</span></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex justify-end mt-8 pt-4 border-t border-gray-100">
                                    <button type="submit" class="px-8 py-2.5 bg-blue-700 hover:bg-blue-800 text-white font-bold rounded-lg shadow-md transition-colors text-sm">Save all</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>