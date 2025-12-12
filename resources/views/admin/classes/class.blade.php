<x-app-layout>
    <x-slot name="header"></x-slot>

    {{-- 
        MAIN WRAPPER: 
        Menggabungkan logika Add, Edit, Toggle Status, dan Delete dalam satu scope Alpine.js
    --}}
    <div class="py-6" x-data="{ 
        // =====================================================================
        // 1. ADD & EDIT LOGIC
        // =====================================================================
        // Logic: Buka Modal Add jika ada error validasi
        showAddModal: {{ $errors->any() && !Session::get('edit_failed') ? 'true' : 'false' }}, 
        
        // Logic: Buka Modal Edit jika ada error validasi dari proses edit
        showEditModal: {{ Session::get('edit_failed') ? 'true' : 'false' }},
        
        // Data formulir edit. Diisi dari data lama (old()) jika validasi gagal.
        editForm: {
            id: '{{ old('id') }}', // Harus ada ID lama agar form update bisa jalan
            category: '{{ old('category') }}',
            name: '{{ old('name') }}',
            classroom: '{{ old('classroom') }}',
            start_month: '{{ old('start_month') }}',
            end_month: '{{ old('end_month') }}',
            academic_year: '{{ old('academic_year') }}',
            form_teacher_id: '{{ old('form_teacher_id') }}',  
            local_teacher_id: '{{ old('local_teacher_id') }}', 
            // Untuk hari, kita pakai data lama atau data yang diload dari tabel
            days: {{ $errors->any() ? json_encode(old('days', [])) : '[]' }}, 
            time_start: '{{ old('start_time') }}', // Perhatikan: field di DB start_time, di modal old('start_time')
            time_end: '{{ old('end_time') }}',     // Perhatikan: field di DB end_time, di modal old('end_time')
            status: '{{ old('status', 'active') }}'
        },

        // URL Templates
        updateUrlTemplate: '{{ route('admin.classes.update', ':id') }}',
        deleteUrlTemplate: '{{ route('admin.classes.delete', 'PLACEHOLDER') }}',
        
        getUpdateUrl() {
            return this.editForm.id ? this.updateUrlTemplate.replace(':id', this.editForm.id) : '#';
        },

        closeModal(modalVar) {
            if ({{ $errors->any() ? 'true' : 'false' }}) {
                // Jika ada error, refresh halaman untuk membersihkan sesi error
                window.location.href = window.location.href.split('?')[0]; // Ambil URL bersih
            } else {
                // Jika tidak ada error, tutup modal via Alpine JS
                this[modalVar] = false;
            }
        },

        openEditModal(data) {
            this.editForm.id = data.id;
            this.editForm.category = data.category;
            this.editForm.name = data.name;
            this.editForm.classroom = data.classroom || ''; 
            this.editForm.start_month = data.start_month; 
            this.editForm.end_month = data.end_month;     
            this.editForm.academic_year = data.academic_year;
            this.editForm.form_teacher_id = data.form_teacher_id || ''; 
            this.editForm.local_teacher_id = data.local_teacher_id || ''; 
            this.editForm.days = data.schedules ? data.schedules.map(item => item.day_of_week) : [];
            this.editForm.time_start = data.start_time; 
            this.editForm.time_end = data.end_time; 
            this.editForm.status = data.is_active ? 'active' : 'inactive'; 
            
            this.showEditModal = true;
        },

        // =====================================================================
        // 2. DELETE LOGIC (DANGER ZONE)
        // =====================================================================
        confirmDelete() {
            if (!this.editForm.id) return;

            Swal.fire({
                title: 'Are you sure?',
                text: 'This class will be moved to trash (Soft Delete). You can restore it later if needed.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444', 
                cancelButtonColor: '#6B7280', 
                confirmButtonText: 'Yes, Delete Class'
            }).then((result) => {
                if (result.isConfirmed) {
                    // 1. Ambil elemen form delete dari x-ref
                    const form = this.$refs.deleteForm;
                    // 2. Ganti placeholder ID dengan ID kelas yang sedang diedit
                    form.action = this.deleteUrlTemplate.replace('PLACEHOLDER', this.editForm.id);
                    // 3. Submit
                    form.submit();
                }
            });
        },

        // =====================================================================
        // 3. CONFIRMATION LOGIC (TOGGLE STATUS) - MENGGUNAKAN SWEETALERT
        // =====================================================================
        confirmToggleStatus(classId, isActive) {
            const action = isActive ? 'DEACTIVATE' : 'ACTIVATE';
            const statusText = isActive ? 'inactive' : 'active';
            const iconColor = isActive ? '#EF4444' : '#10B981'; 

            Swal.fire({
                title: `${action} Class?`,
                text: `Are you sure you want to change this class's status to ${statusText}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: iconColor, 
                cancelButtonColor: '#6B7280',
                confirmButtonText: `Yes, ${action}`
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit form PATCH secara dinamis
                    const form = document.getElementById('toggleStatusForm');
                    // Mengambil URL template dari atribut data-url form tersembunyi
                    const urlTemplate = form.getAttribute('data-url');
                    const url = urlTemplate.replace('PLACEHOLDER', classId); 
                    
                    form.action = url;
                    form.submit();
                }
            });
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- 1. BREADCRUMB --}}
            <nav class="flex mb-5" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-blue-600">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                            Dashboard
                        </a>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            <span class="ml-1 text-sm font-medium text-gray-900 md:ml-2">Classes</span>
                        </div>
                    </li>
                </ol>
            </nav>
            
            {{-- Title --}}
            <div class="mb-8">
                <h1 class="text-3xl font-bold bg-gradient-to-b from-blue-500 to-red-500 bg-clip-text text-transparent">
                    Classes Data
                </h1>
            </div>

            {{-- 2. TABLE SECTION --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

                {{-- Header Actions (Filters) --}}
                <div class="p-4 sm:p-6 border-b border-gray-200 flex flex-col gap-4">
                    
                    {{-- SEARCH BAR --}}
                    <div class="w-full">
                        <form action="{{ route('admin.classes.index') }}" method="GET" class="relative w-full">
                            @foreach(['academic_year', 'category', 'sort', 'status'] as $key)
                                @if(request($key)) <input type="hidden" name="{{ $key }}" value="{{ request($key) }}"> @endif
                            @endforeach
                            
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Search class name or classroom..." 
                                   class="w-full h-11 pl-12 pr-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm shadow-sm transition-all">

                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" /></svg>
                            </div>
                        </form>
                    </div>
                    
                    {{-- FILTERS --}}
                    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">

                        <form action="{{ route('admin.classes.index') }}" method="GET" class="flex flex-wrap items-center gap-2 w-full lg:w-auto">
                            @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                            
                            {{-- Academic Year --}}
                            <div class="relative flex-grow sm:flex-grow-0">
                                <select name="academic_year" onchange="this.form.submit()" 
                                        class="h-10 w-full sm:w-auto px-3 py-2 pr-8 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 bg-gray-50 focus:ring-2 focus:ring-blue-500 cursor-pointer">
                                    <option value="">All Years</option>
                                    @foreach($years as $year)
                                        <option value="{{ $year }}" {{ request('academic_year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Category --}}
                            <div class="relative flex-grow sm:flex-grow-0">
                                <select name="category" onchange="this.form.submit()" 
                                        class="h-10 w-full sm:w-auto px-3 py-2 pr-8 border border-gray-300 rounded-lg text-sm text-gray-700 bg-white focus:ring-2 focus:ring-blue-500 cursor-pointer">
                                    <option value="">Category</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ ucwords(str_replace('_', ' ', $cat)) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            {{-- Status --}}
                            <div class="relative flex-grow sm:flex-grow-0">
                                <select name="status" onchange="this.form.submit()" 
                                        class="h-10 w-full sm:w-auto px-3 py-2 pr-8 border border-gray-300 rounded-lg text-sm text-gray-700 bg-white focus:ring-2 focus:ring-blue-500 cursor-pointer">
                                    <option value="" {{ request('status') === null ? '' : (request('status') == '' ? 'selected' : '') }}>All Status</option>
                                    <option value="active" {{ request('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>

                            {{-- Sort --}}
                            <div class="relative flex-grow sm:flex-grow-0">
                                <select name="sort" onchange="this.form.submit()" 
                                        class="h-10 w-full sm:w-auto px-3 py-2 pr-8 border border-gray-300 rounded-lg text-sm text-gray-700 bg-white focus:ring-2 focus:ring-blue-500 cursor-pointer">
                                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
                                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>A-Z</option>
                                    <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Z-A</option>
                                </select>
                            </div>

                            {{-- Reset --}}
                            @if(request('academic_year') || request('category') || request('sort') || request('search') || request('status'))
                                <a href="{{ route('admin.classes.index') }}" class="h-10 w-10 flex items-center justify-center bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 rounded-lg transition-colors flex-shrink-0" title="Reset Filters">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </a>
                            @endif
                        </form>

                        {{-- ADD BUTTON --}}
                        <div class="w-full lg:w-auto">
                            <button @click="showAddModal = true"
                                class="inline-flex w-full lg:w-auto items-center justify-center gap-2 px-5 h-10 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium text-sm shadow-sm whitespace-nowrap">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                Add New Class
                            </button>
                        </div>
                    </div>
                </div>

                {{-- 3. TABLE CONTENT --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-max">
                        <thead class="bg-gray-50 text-xs text-gray-500 font-bold uppercase border-b border-gray-100 tracking-wider">
                            <tr>
                                <th class="px-6 py-4 w-16 whitespace-nowrap text-center">No</th>
                                <th class="px-6 py-4 whitespace-nowrap">Category</th>
                                <th class="px-6 py-4 whitespace-nowrap">Class Name</th>
                                <th class="px-6 py-4 whitespace-nowrap">Classroom</th>
                                <th class="px-6 py-4 whitespace-nowrap">Schedule</th>
                                <th class="px-6 py-4 whitespace-nowrap">Teacher</th>
                                <th class="px-6 py-4 whitespace-nowrap text-center">Status</th>
                                <th class="px-6 py-4 whitespace-nowrap text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-sm text-gray-700">
                            @forelse($classes as $index => $class)
                                <tr class="hover:bg-gray-50 transition-colors group">
                                    {{-- No --}}
                                    <td class="px-6 py-5 text-center text-gray-500 font-medium">{{ $classes->firstItem() + $index }}</td>
                                    
                                    {{-- Category --}}
                                    <td class="px-6 py-5 text-gray-600 capitalize">
                                        {{ str_replace('_', ' ', $class->category ?? '-') }}
                                    </td>
                                    
                                    {{-- Class Name (Bold Hitam) --}}
                                    <td class="px-6 py-5 font-medium text-gray-900 text-base">
                                        {{ $class->name }}
                                    </td>
                                    
                                    {{-- Classroom --}}
                                    <td class="px-6 py-5 text-gray-500 font-medium whitespace-nowrap">{{ $class->classroom }}</td>
                                    
                                    {{-- Schedule --}}
                                    <td class="px-6 py-5">
                                        <div class="flex flex-col gap-1.5">
                                            <span class="font-medium text-gray-800 text-xs">
                                                @if($class->schedules->isNotEmpty())
                                                    {{ $class->schedules->pluck('day_of_week')->implode(', ') }}
                                                @else
                                                    <span class="text-gray-400 italic">-</span>
                                                @endif
                                            </span>
                                            <span class="inline-block bg-blue-50 text-blue-700 border border-blue-100 px-2 py-0.5 rounded-md text-[10px] font-bold w-fit">
                                                {{ \Carbon\Carbon::parse($class->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($class->end_time)->format('H:i') }}
                                            </span>
                                        </div>
                                    </td>

                                    {{-- Teacher --}}
                                    <td class="px-6 py-5 text-xs whitespace-nowrap">
                                        <div class="flex flex-col gap-1">
                                            <div class="flex items-center gap-1.5">
                                                <span class="text-gray-400 font-bold uppercase text-[10px] w-10">FORM:</span>
                                                <span class="text-gray-900 font-medium">{{ $class->formTeacher->name ?? '-' }}</span>
                                            </div>
                                            <div class="flex items-center gap-1.5">
                                                <span class="text-gray-400 font-bold uppercase text-[10px] w-10">LOCAL:</span>
                                                <span class="text-gray-700">{{ $class->localTeacher->name ?? '-' }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-6 py-5 text-center">
                                        @if($class->is_active)
                                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold border border-green-200">Active</span>
                                        @else
                                            <span class="px-3 py-1 bg-gray-100 text-gray-500 rounded-full text-xs font-bold border border-gray-200">Inactive</span>
                                        @endif
                                    </td>

                                    {{-- Action Buttons --}}
                                    <td class="px-6 py-5 text-center">
                                        <div class="flex items-center justify-center gap-3">
                                            {{-- Detail --}}
                                            <a href="{{ route('admin.classes.detailclass', $class->id) }}" 
                                                class="text-gray-400 hover:text-blue-600 transition-colors" title="View Details">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                            </a>
                                            
                                            {{-- Edit --}}
                                            <button type="button" @click='openEditModal(@json($class))' 
                                                    class="text-gray-400 hover:text-green-600 transition-colors" title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                            </button>
                                            
                                            {{-- Toggle Status BARU (Memanggil SweetAlert) --}}
                                            <button type="button" 
                                                @click="confirmToggleStatus({{ $class->id }}, {{ $class->is_active ? 'true' : 'false' }})"
                                                class="p-1.5 transition-colors 
                                                       {{ $class->is_active ? 'text-gray-400 hover:text-red-600 hover:bg-red-50' : 'text-gray-400 hover:text-green-600 hover:bg-green-50' }}"
                                                title="{{ $class->is_active ? 'Deactivate' : 'Activate' }}">
                                                
                                                @if($class->is_active)
                                                    {{-- Icon Power Off --}}
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                    </svg>
                                                @else
                                                    {{-- Icon Refresh --}}
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                    </svg>
                                                @endif
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-10 text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                            <p class="text-base font-medium">No classes found.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                {{-- Pagination --}}
                <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between bg-white">
                    <span class="text-sm text-gray-500 font-medium">Page {{ $classes->currentPage() }} of {{ $classes->lastPage() }}</span>
                    @if ($classes->lastPage() > 1)
                        {{ $classes->links() }}
                    @endif
                </div>
            </div>

            {{-- 3. MODALS AREA --}}
            
            {{-- Include Modal Add --}}
            @include('admin.classes.partials.add-class-modal')

            {{-- Include Modal Edit --}}
            @include('admin.classes.partials.edit-class-modal')

            {{-- CATATAN: Modal Konfirmasi Kustom Alpine JS dihapus, diganti SweetAlert --}}
            
            {{-- Hidden Form Toggle Status (DIPERTahankan untuk SweetAlert) --}}
            <form id="toggleStatusForm" method="POST" 
                  action="" 
                  data-url="{{ route('admin.classes.toggleStatus', ['id' => 'PLACEHOLDER']) }}" 
                  style="display: none;">
                @csrf
                @method('PATCH')
            </form>

            {{-- Hidden Form Delete (Masih menggunakan x-ref di file utama) --}}
            <form method="POST" action="#" x-ref="deleteForm" style="display: none;">
                @csrf
                @method('DELETE')
            </form>

        </div> {{-- END MAIN WRAPPER --}}
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</x-app-layout>