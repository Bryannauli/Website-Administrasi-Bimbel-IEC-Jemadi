<x-app-layout>
    <x-slot name="header"></x-slot>

    {{-- 
        x-data UTAMA:
        Mengontrol semua state (Add & Edit) dalam satu cakupan.
    --}}
    <div class="py-6" x-data="{ 
        showAddModal: false, 
        showEditModal: false,
        
        // Data form untuk Edit
        editForm: {
            id: null,
            name: '',
            classroom: '',
            period: '',
            academic_year: '',
            teacher_id: '',
            day: '',
            time: '',
            status: 'active'
        },

        // Template URL Update
        // Pastikan route ini ada di web.php: Route::put('/admin/classes/{id}', ...)
        updateUrl: '{{ route('admin.classes.update', ':id') }}',
        
        // Fungsi Helper URL
        getUpdateUrl() {
            return this.editForm.id ? this.updateUrl.replace(':id', this.editForm.id) : '#';
        },

        // Fungsi Buka Modal Edit
        openEditModal(data) {
            // Reset form dengan data baru
            this.editForm.id = data.id;
            this.editForm.name = data.name;
            this.editForm.classroom = 'E-101'; // Dummy default
            this.editForm.period = data.start_month + ' - ' + data.end_month;
            this.editForm.academic_year = data.academic_year;
            this.editForm.teacher_id = '1'; 
            this.editForm.day = 'Wednesday'; 
            this.editForm.time = '08.00 - 10.00'; 
            this.editForm.status = data.status;
            
            // Tampilkan Modal Edit
            this.showEditModal = true;
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Breadcrumb & Title --}}
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
                    
                    {{-- Filter Button --}}
                    <div x-data="{ open: false, selectedYear: '2025/2026' }" class="relative">
                        <button @click="open = !open" class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm w-40 justify-between">
                            <span x-text="selectedYear"></span>
                            <svg class="w-4 h-4 text-gray-500 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-transition class="absolute mt-2 w-40 bg-white border border-gray-200 rounded-lg shadow-md z-50">
                            <ul class="py-1 text-sm text-gray-700">
                                <template x-for="year in ['2023/2024','2024/2025','2025/2026','2026/2027']">
                                    <li><button @click="selectedYear = year; open = false" class="w-full text-left px-4 py-2 hover:bg-gray-100" x-text="year"></button></li>
                                </template>
                            </ul>
                        </div>
                    </div>

                    {{-- Add New Class Button --}}
                    <button @click="showAddModal = true" class="inline-flex items-center px-4 py-2 bg-blue-700 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-sm transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        Add New Class
                    </button>
                </div>

                {{-- Search Bar --}}
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
                                <th class="px-6 py-4 font-normal">Class</th>
                                <th class="px-6 py-4 font-normal">Start Month</th>
                                <th class="px-6 py-4 font-normal">End Month</th>
                                <th class="px-6 py-4 font-normal">Academic Year</th>
                                <th class="px-6 py-4 font-normal">Status</th>
                                <th class="px-6 py-4 font-normal text-center w-32">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                            @forelse($classes as $index => $class)
                                <tr class="hover:bg-gray-50 transition-colors group">
                                    <td class="px-6 py-5 text-center text-gray-500">{{ $classes->firstItem() + $index }}</td>
                                    <td class="px-6 py-5 font-medium text-gray-900">{{ $class->name }}</td>
                                    <td class="px-6 py-5 text-gray-600">{{ $class->start_month }}</td>
                                    <td class="px-6 py-5 text-gray-600">{{ $class->end_month }}</td>
                                    <td class="px-6 py-5 text-gray-600">{{ $class->academic_year }}</td>
                                    <td class="px-6 py-5">
                                        @if($class->status == 'active')
                                            <span class="px-3 py-1 bg-purple-100 text-purple-600 rounded-md text-xs font-medium capitalize inline-block">Active</span>
                                        @else
                                            <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-md text-xs font-medium capitalize inline-block">{{ $class->status }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex items-center justify-center gap-4 ">
                                            {{-- View --}}
                                            <button class="text-gray-400 hover:text-blue-600 transition-colors" title="View Details">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                            </button>
                                            {{-- Delete --}}
                                            <button class="text-gray-400 hover:text-red-600 transition-colors" title="Delete">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                            
                                            {{-- EDIT BUTTON (FIXED) --}}
                                            <button type="button" 
                                                    @click='openEditModal(@json($class))' 
                                                    class="text-gray-400 hover:text-green-600 transition-colors" 
                                                    title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="px-6 py-10 text-center text-gray-500 bg-gray-50">No classes found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{-- Pagination --}}
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

            {{-- ========================================== --}}
            {{-- MODAL ADD NEW CLASS (OVERLAY)              --}}
            {{-- ========================================== --}}
            <div x-show="showAddModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="showAddModal" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true" @click="showAddModal = false"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div x-show="showAddModal" x-transition.scale class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl w-full">
                        <div class="px-8 py-6 flex justify-between items-center border-b border-gray-100">
                            <div class="flex items-center gap-2"><h3 class="text-xl font-bold text-gray-900">Add new Class</h3></div>
                            <button @click="showAddModal = false" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                        </div>
                        <div class="px-8 py-6">
                            <form action="{{ route('admin.classes.store') }}" method="POST">
                                @csrf
                                {{-- FORM ADD --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                    <div><label class="block text-sm font-bold text-gray-700 mb-2">Class Name</label><input type="text" name="name" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none shadow-sm" required></div>
                                    <div>
    <label class="block text-sm font-bold text-gray-700 mb-2">Classroom</label>

    <select
        class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-gray-700
               focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none shadow-sm">

        <option value="">Select Classroom</option>
        <option value="E-101">E-101</option>
        <option value="E-102">E-102</option>
        <option value="E-103">E-103</option>
        <option value="F-201">F-201</option>
        <option value="F-202">F-202</option>
        <option value="F-203">F-203</option>
    </select>
</div>

                                   <div>
    <label class="block text-sm font-bold text-gray-700 mb-2">Start Month & End Month</label>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <!-- Start Date -->
        <input type="date"
               class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-gray-700
                      focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none shadow-sm">

        <!-- End Date -->
        <input type="date"
               class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-gray-700
                      focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none shadow-sm">
    </div>
</div>

                                    <div>
    <label class="block text-sm font-bold text-gray-700 mb-2">Academic Year</label>

    <select
        class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-gray-700
               focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none shadow-sm">

        <option value="">Select Academic Year</option>
   
        <option value="2025/2026">2025/2026</option>
        <option value="2026/2027">2026/2027</option>
        
    </select>
</div>

                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Teacher</label>
                                        <select name="teacher_id" class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-gray-500 bg-white"><option value="1">Mr. Geonwoo</option><option value="2">Ms. Sarah</option></select>
                                    </div>
                                    <div>
    <label class="block text-sm font-bold text-gray-700 mb-2">Schedule</label>
    <div class="flex gap-3">
        <div class="relative w-1/3">
            <select class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none bg-white cursor-pointer appearance-none shadow-sm">
                <option value="" disabled selected>Wednesday</option>
                <option>Monday</option>
                <option>Tuesday</option>
            </select>
        </div>

        <div class="flex flex-1 gap-2">
            <input type="time"
                   class="w-1/2 border border-gray-200 rounded-lg px-3 py-2 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none shadow-sm">
            <input type="time"
                   class="w-1/2 border border-gray-200 rounded-lg px-3 py-2 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none shadow-sm">
        </div>
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


            {{-- ========================================== --}}
            {{-- MODAL EDIT CLASS (OVERLAY)                 --}}
            {{-- ========================================== --}}
            <div x-show="showEditModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    {{-- Backdrop --}}
                    <div x-show="showEditModal" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true" @click="showEditModal = false"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    
                    {{-- Konten Modal Edit --}}
                    <div x-show="showEditModal" x-transition.scale class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl w-full">
                        
                        {{-- Header Modal Edit --}}
                        <div class="px-8 py-6 flex justify-between items-center border-b border-gray-100">
                            <div class="flex items-center gap-2">
                                <h3 class="text-xl font-bold text-gray-900">Edit Class</h3>
                            </div>
                            <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                        
                        {{-- Body Modal Edit --}}
                        <div class="px-8 py-6">
                            <form :action="getUpdateUrl()" method="POST"> 
                                @csrf
                                @method('PUT')
                                {{-- FORM EDIT (TERHUBUNG KE x-model editForm) --}}
                               <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

    {{-- Class Name --}}
    <div>
        <label class="block text-sm font-bold text-gray-700 mb-2">Class Name</label>
        <input type="text" name="name" x-model="editForm.name"
               class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-gray-700">
    </div>

    {{-- Classroom (DROPDOWN) --}}
    <div>
        <label class="block text-sm font-bold text-gray-700 mb-2">Classroom</label>
        <select name="classroom" x-model="editForm.classroom"
                class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-gray-500 bg-white cursor-pointer">
            <option value="" disabled>Select classroom</option>
            <option value="A101">A101</option>
            <option value="A102">A102</option>
            <option value="B201">B201</option>
        </select>
    </div>

    {{-- Start Month & End Month (KALENDER) --}}
    <div>
        <label class="block text-sm font-bold text-gray-700 mb-2">Start Month</label>
        <input type="month" name="start_month" x-model="editForm.start_month"
               class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-gray-700">
    </div>

    <div>
        <label class="block text-sm font-bold text-gray-700 mb-2">End Month</label>
        <input type="month" name="end_month" x-model="editForm.end_month"
               class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-gray-700">
    </div>

    {{-- Academic Year (DROPDOWN) --}}
    <div>
        <label class="block text-sm font-bold text-gray-700 mb-2">Academic Year</label>
        <select name="academic_year" x-model="editForm.academic_year"
                class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-gray-500 bg-white cursor-pointer">
            <option disabled value="">Select Year</option>
          
            <option>2025/2026</option>
        </select>
    </div>

    {{-- Teacher --}}
    <div>
        <label class="block text-sm font-bold text-gray-700 mb-2">Teacher</label>
        <select name="teacher_id" x-model="editForm.teacher_id"
                class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-gray-500 bg-white cursor-pointer">
            <option value="1">Mr. Geonwoo</option>
            <option value="2">Ms. Sarah</option>
        </select>
    </div>

    {{-- Schedule (Dropdown DAY + TIME RANGE) --}}
    <div class="md:col-span-2">
        <label class="block text-sm font-bold text-gray-700 mb-2">Schedule</label>
        <div class="flex gap-3">

            {{-- Day dropdown --}}
            <select name="day" x-model="editForm.day"
                    class="w-32 border border-gray-200 rounded-lg px-4 py-2.5 text-gray-500 bg-white cursor-pointer">
                <option>Monday</option>
                <option>Tuesday</option>
                <option>Wednesday</option>
                <option>Thursday</option>
                <option>Friday</option>
            </select>

            {{-- Time Start --}}
            <input type="time" name="time_start" x-model="editForm.time_start"
                   class="border border-gray-200 rounded-lg px-4 py-2.5 text-gray-700">

            {{-- Time End --}}
            <input type="time" name="time_end" x-model="editForm.time_end"
                   class="border border-gray-200 rounded-lg px-4 py-2.5 text-gray-700">

        </div>
    </div>

</div>

                                {{-- Status --}}
                                <div class="mb-6">
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Status</label>
                                    <div class="flex gap-6">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="radio" name="status" value="active" x-model="editForm.status" class="text-blue-600 border-gray-300 focus:ring-blue-500">
                                            <span class="ml-2 text-gray-700 font-medium">Active</span>
                                        </label>
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="radio" name="status" value="inactive" x-model="editForm.status" class="text-blue-600 border-gray-300 focus:ring-blue-500">
                                            <span class="ml-2 text-gray-700 font-medium">Inactive</span>
                                        </label>
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
            {{-- END MODAL EDIT --}}

        </div>
    </div>
</x-app-layout>