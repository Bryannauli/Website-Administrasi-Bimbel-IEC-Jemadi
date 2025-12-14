<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight hidden">
            {{ __('Students') }}
        </h2>
    </x-slot>

    <div class="py-6" x-data="{ 
        // === STATUS ERROR GLOBAL ===
        hasError: {{ $errors->any() ? 'true' : 'false' }},
        isEditFailed: {{ Session::get('edit_failed') ? 'true' : 'false' }}, 
        
        // LOGIKA AUTO-OPEN MODAL
        showAddModal: {{ $errors->any() && !Session::get('edit_failed') ? 'true' : 'false' }},
        showEditModal: {{ Session::get('edit_failed') ? 'true' : 'false' }}, 
        
        // URL ACTION
        updateUrl: '',
        deleteUrl: '',
        
        // Data Form Edit
        editForm: {
            id: null,
            student_number: '',
            name: '',
            gender: 'male',
            phone: '',
            address: '',
            is_active: true,
            class_id: ''
        },

        // 1. INIT FUNCTION (Jalankan saat load jika ada error validasi edit)
        init() {
            if (this.isEditFailed) {
                this.editForm = {
                    id: '{{ old('id') }}',
                    student_number: '{{ old('student_number') }}',
                    name: '{{ old('name') }}',
                    gender: '{{ old('gender') }}',
                    phone: '{{ old('phone') }}',
                    address: {{ json_encode(old('address')) }}, // Encode agar aman dari enter/newline
                    is_active: {{ old('is_active') == 1 ? 'true' : 'false' }},
                    class_id: '{{ old('class_id') }}'
                };
                // Reconstruct URL
                this.updateUrl = '{{ route('admin.student.update', ':id') }}'.replace(':id', this.editForm.id);
                this.deleteUrl = '{{ route('admin.student.delete', ':id') }}'.replace(':id', this.editForm.id);
            }
        },

        // 2. CLOSE MODAL
        closeModal(modalVar) {
            if (this.hasError) {
                // Jika sedang error, refresh halaman untuk bersihkan error bag
                window.location.href = window.location.href.split('?')[0]; 
            } else {
                this[modalVar] = false;
            }
        },

        // 3. OPEN EDIT MODAL
        openEditModal(student) {
            this.editForm = {
                id: student.id,
                student_number: student.student_number,
                name: student.name,
                gender: student.gender,
                phone: student.phone || '',
                address: student.address || '',
                is_active: student.is_active == 1,
                class_id: student.class_id || ''
            };

            // Set URL Update & Delete sesuai ID siswa
            this.updateUrl = '{{ route('admin.student.update', ':id') }}'.replace(':id', student.id);
            this.deleteUrl = '{{ route('admin.student.delete', ':id') }}'.replace(':id', student.id);
            
            this.showEditModal = true;
        },

        // 4. CONFIRM TOGGLE STATUS
        confirmToggleStatus(studentId, isActive) {
            const action = isActive ? 'DEACTIVATE' : 'ACTIVATE';
            const statusText = isActive ? 'inactive' : 'active';
            const iconColor = isActive ? '#EF4444' : '#10B981'; 

            Swal.fire({
                title: `${action} Student?`,
                text: `Are you sure you want to change this student's status to ${statusText}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: iconColor, 
                cancelButtonColor: '#6B7280',
                confirmButtonText: `Yes, ${action}`
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('toggleStatusForm');
                    const url = '{{ route('admin.student.toggleStatus', ':id') }}'.replace(':id', studentId);
                    form.action = url;
                    form.submit();
                }
            });
        },

        // 5. CONFIRM DELETE (Soft Delete)
        confirmDelete() {
            Swal.fire({
                title: 'Are you sure?',
                text: 'This student will be moved to trash (Soft Delete).',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444', 
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, Delete Student'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit form hidden yang ada di dalam edit-modal.blade.php
                    document.getElementById('delete-student-form').submit();
                }
            });
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- BREADCRUMB --}}
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
                            <span class="ml-1 text-sm font-medium text-gray-900 md:ml-2">Students</span>
                        </div>
                    </li>
                </ol>
            </nav>

            {{-- TITLE --}}
            <div class="mb-8">
                <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent inline-block">
                    Students Data
                </h1>
                <p class="text-gray-500 text-sm mt-1">Manage all active and inactive student records.</p>
            </div>

            {{-- STATS CARD --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 border-l-4 border-l-blue-600 p-4 mb-8 max-w-sm">
                <div class="flex items-center justify-between gap-4">
                    {{-- Kiri: Total Utama --}}
                    <div>
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-0.5">Total Students</h3>
                        <p class="text-3xl font-bold text-gray-900 leading-none">
                            {{ number_format($globalTotal ?? 0) }}
                        </p>
                    </div>

                    {{-- Kanan: Active & Inactive --}}
                    <div class="flex flex-col gap-1.5">
                        <div class="flex items-center justify-between gap-3 px-2.5 py-1 bg-blue-50 text-blue-700 rounded-md border border-blue-100 min-w-[110px]">
                            <div class="flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                                <span class="text-[10px] font-bold uppercase">Active</span>
                            </div>
                            <span class="text-sm font-bold">{{ number_format($totalActive ?? 0) }}</span>
                        </div>

                        <div class="flex items-center justify-between gap-3 px-2.5 py-1 bg-red-50 text-red-700 rounded-md border border-red-100 min-w-[110px]">
                            <div class="flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-600"></span>
                                <span class="text-[10px] font-bold uppercase">Inactive</span>
                            </div>
                            <span class="text-sm font-bold">{{ number_format($totalInactive ?? 0) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TABLE SECTION --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

                {{-- HEADER ACTIONS --}}
                <div class="p-4 sm:p-6 border-b border-gray-200 flex flex-col gap-4">
                    
                    {{-- Search --}}
                    <div class="w-full">
                        <form action="{{ route('admin.student.index') }}" method="GET" class="relative w-full">
                            @foreach(['academic_year', 'category', 'class_id', 'sort', 'status'] as $key)
                                @if(request($key)) <input type="hidden" name="{{ $key }}" value="{{ request($key) }}"> @endif
                            @endforeach
                            
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Search student name or ID..." 
                                   class="w-full h-11 pl-12 pr-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm shadow-sm transition-all">

                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" /></svg>
                            </div>
                        </form>
                    </div>

                    {{-- FILTERS & ADD BUTTON --}}
                    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">

                        <form action="{{ route('admin.student.index') }}" method="GET" class="flex flex-wrap items-center gap-2 w-full lg:w-auto">
                            @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif

                            {{-- Filter Tahun --}}
                            <div class="relative flex-grow sm:flex-grow-0">
                                <select name="academic_year" onchange="this.form.submit()" 
                                        class="h-10 w-full sm:w-auto px-3 py-2 pr-10 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 bg-gray-50 focus:ring-2 focus:ring-blue-500 cursor-pointer appearance-none">
                                    <option value="">All Years</option>
                                    @foreach($years as $year)
                                        <option value="{{ $year }}" {{ request('academic_year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Filter Category --}}
                            <div class="relative flex-grow sm:flex-grow-0">
                                <select name="category" onchange="this.form.submit()" 
                                        class="h-10 w-full sm:w-auto px-3 py-2 pr-10 border border-gray-300 rounded-lg text-sm text-gray-700 bg-white focus:ring-2 focus:ring-blue-500 cursor-pointer appearance-none">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ ucwords(str_replace('_', ' ', $cat)) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Filter Class --}}
                            <div class="relative flex-grow sm:flex-grow-0">
                                <select name="class_id" onchange="this.form.submit()" 
                                        class="h-10 w-full sm:w-auto px-3 py-2 pr-10 border border-gray-300 rounded-lg text-sm text-gray-700 bg-white focus:ring-2 focus:ring-blue-500 cursor-pointer appearance-none max-w-[200px] truncate">
                                    <option value="">All Classes</option>
                                    <option value="no_class" class="text-red-600 font-semibold" {{ request('class_id') == 'no_class' ? 'selected' : '' }}>⚠ No Class</option>
                                    @foreach($classes as $classItem)
                                        <option value="{{ $classItem->id }}" {{ request('class_id') == $classItem->id ? 'selected' : '' }}>{{ $classItem->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Filter Status --}}
                            <div class="relative flex-grow sm:flex-grow-0">
                                <select name="status" onchange="this.form.submit()" 
                                        class="h-10 w-full sm:w-auto px-3 py-2 pr-10 border border-gray-300 rounded-lg text-sm text-gray-700 bg-white focus:ring-2 focus:ring-blue-500 cursor-pointer appearance-none">
                                    <option value="" {{ request()->has('status') && request('status') == '' ? 'selected' : '' }}>All Status</option>
                                    <option value="active" {{ request('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>

                            {{-- Sort Filter --}}
                            <div class="relative flex-grow sm:flex-grow-0">
                                <select name="sort" onchange="this.form.submit()" 
                                        class="h-10 w-full sm:w-auto px-3 py-2 pr-10 border border-gray-300 rounded-lg text-sm text-gray-700 bg-white focus:ring-2 focus:ring-blue-500 cursor-pointer appearance-none">
                                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
                                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                                    <option value="number_asc" {{ request('sort') == 'number_asc' ? 'selected' : '' }}>ID (0-9)</option>
                                    <option value="number_desc" {{ request('sort') == 'number_desc' ? 'selected' : '' }}>ID (9-0)</option>
                                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                                    <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                                </select>
                            </div>

                            {{-- Reset Filters --}}
                            @if(request('class_id') || request('academic_year') || request('category') || request('sort') || request('search') || request('status'))
                                <a href="{{ route('admin.student.index') }}" class="h-10 w-10 flex items-center justify-center bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 rounded-lg transition-colors flex-shrink-0" title="Reset Filters">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </a>
                            @endif
                        </form>

                        {{-- BUTTON ADD NEW STUDENT --}}
                        <div class="w-full lg:w-auto">
                            <button @click="showAddModal = true" 
                            class="inline-flex w-full lg:w-auto items-center justify-center gap-2 px-5 h-10 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium text-sm shadow-sm whitespace-nowrap">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" /></svg>
                                Add New Student
                            </button>
                        </div>
                    </div>

                {{-- TABLE CONTENT --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-max">
                        <thead class="bg-gray-50 text-xs text-gray-500 font-bold uppercase border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 whitespace-nowrap">No</th>
                                <th class="px-6 py-4 whitespace-nowrap">Student ID</th>
                                <th class="px-6 py-4 whitespace-nowrap">Name</th>
                                <th class="px-6 py-4 whitespace-nowrap">Gender</th>
                                <th class="px-6 py-4 whitespace-nowrap">Phone</th>
                                <th class="px-6 py-4 whitespace-nowrap">Address</th>
                                <th class="px-6 py-4 whitespace-nowrap">Class</th>
                                <th class="px-6 py-4 whitespace-nowrap text-center">Active</th>
                                <th class="px-6 py-4 whitespace-nowrap text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                            @php $startNumber = ($students->currentPage() - 1) * $students->perPage() + 1; @endphp

                            @forelse ($students as $index => $student)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 text-gray-500 font-medium whitespace-nowrap">{{ $startNumber + $index }}</td>
                                    <td class="px-6 py-4 font-bold text-gray-900 whitespace-nowrap">{{ $student->student_number }}</td>
                                    <td class="px-6 py-4 font-semibold text-gray-800 whitespace-nowrap">{{ $student->name }}</td>
                                    <td class="px-6 py-4 capitalize whitespace-nowrap">{{ $student->gender }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $student->phone ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap max-w-[200px] truncate" title="{{ $student->address }}">{{ $student->address ?? '-' }}</td>
                                    
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        @if($student->classModel)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $student->classModel->name }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-600 border border-red-200">
                                                Unassigned
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        @if($student->is_active)
                                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">Active</span>
                                        @else
                                            <span class="px-3 py-1 bg-gray-100 text-gray-500 rounded-full text-xs font-bold">Inactive</span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-3">
                                            
                                            <a href="{{ route('admin.student.detail', $student->id) }}" 
                                               class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="View Details">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                            </a>

                                            <button type="button" @click='openEditModal(@json($student))' 
                                                    class="p-1.5 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors" title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                            </button>

                                            <button type="button" 
                                                @click="confirmToggleStatus({{ $student->id }}, {{ $student->is_active ? 'true' : 'false' }})"
                                                class="p-1.5 transition-colors 
                                                       {{ $student->is_active ? 'text-gray-400 hover:text-red-600 hover:bg-red-50' : 'text-gray-400 hover:text-green-600 hover:bg-green-50' }}"
                                                title="{{ $student->is_active ? 'Deactivate' : 'Activate' }}">
                                                @if($student->is_active)
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                                                @else
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                @endif
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-10 text-center text-gray-500">No students found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- PAGINATION --}}
                <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between bg-white">
                    @if ($students->onFirstPage())
                        <button class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-400 bg-gray-50 cursor-not-allowed" disabled>Previous</button>
                    @else
                        <a href="{{ $students->previousPageUrl() }}" class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-600 bg-white hover:bg-gray-50 hover:text-gray-800 transition-colors">Previous</a>
                    @endif
                    <span class="text-sm text-gray-500 font-medium">
                        Page <span class="font-semibold text-gray-900">{{ $students->currentPage() }}</span> of <span class="font-semibold text-gray-900">{{ $students->lastPage() }}</span>
                    </span>
                    @if ($students->hasMorePages())
                        <a href="{{ $students->nextPageUrl() }}" class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-600 bg-white hover:bg-gray-50 hover:text-gray-800 transition-colors">Next</a>
                    @else
                        <button class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-400 bg-gray-50 cursor-not-allowed" disabled>Next</button>
                    @endif
                </div>

            </div>
        </div>

        {{-- INCLUDE PARTIALS --}}
        @include('admin.student.partials.add-modal')
        @include('admin.student.partials.edit-modal', ['showClassAssignment' => false])

        {{-- FORM HIDDEN UNTUK TOGGLE STATUS --}}
        <form id="toggleStatusForm" method="POST" action="#" style="display: none;">
            @csrf 
            @method('PATCH')
        </form>
    </div>
    
    {{-- SWEETALERT HANDLER --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gunakan json_encode untuk keamanan syntax JS
            const successMessage = <?php json_encode(session('success')) ?>;
            const errorMessage   = <?php json_encode(session('error')) ?>;

            if (successMessage) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: successMessage,
                    timer: 3000,
                    showConfirmButton: false
                });
            }

            if (errorMessage) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: errorMessage
                });
            }
        });
    </script>
</x-app-layout>