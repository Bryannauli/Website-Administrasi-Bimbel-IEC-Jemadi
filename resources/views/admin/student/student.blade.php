<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight hidden">
            {{ __('Students') }}
        </h2>
    </x-slot>

    {{-- KONTEN UTAMA --}}
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Breadcrumb & Title --}}
            <div class="mb-6">
                <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
                    <a href="{{ route('dashboard') }}" class="hover:text-gray-900">Dashboard</a>
                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-gray-900 font-medium">Students</span>
                </div>

                <h1 class="text-3xl font-bold bg-gradient-to-b from-blue-500 to-red-500 bg-clip-text text-transparent">
                    Students Data
                </h1>
            </div>

            {{-- Stats Card --}}
            <div class="bg-white rounded-xl shadow-sm p-4 mb-8 max-w-xl border border-gray-100">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-6 w-full sm:w-auto">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-500 mb-1">Total Students</h3>
                            <p class="text-3xl font-bold text-gray-900">{{ number_format($total_students ?? 0) }}</p>
                        </div>
                        <div class="flex gap-3">
                            <div class="flex items-center gap-2 px-3 py-1 bg-blue-50 text-blue-700 rounded-lg border border-blue-100">
                                <span class="text-xs font-medium">Active</span>
                                <span class="text-sm font-bold">{{ number_format($total_active ?? 0) }}</span>
                            </div>
                            <div class="flex items-center gap-2 px-3 py-1 bg-red-50 text-red-700 rounded-lg border border-red-100">
                                <span class="text-xs font-medium">Inactive</span>
                                <span class="text-sm font-bold">{{ number_format($total_inactive ?? 0) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Table Section --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

                {{-- Table Header Actions (LAYOUT BARU: Search Atas, Filter Bawah) --}}
                <div class="p-4 sm:p-6 border-b border-gray-200 flex flex-col gap-4">
                    
                    {{-- BARIS 1: SEARCH BAR (Full Width) --}}
                    <div class="w-full">
                        <form action="{{ route('admin.student.index') }}" method="GET" class="relative w-full">
                            {{-- Pertahankan filter saat searching --}}
                            @if(request('academic_year')) <input type="hidden" name="academic_year" value="{{ request('academic_year') }}"> @endif
                            @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
                            @if(request('class_id')) <input type="hidden" name="class_id" value="{{ request('class_id') }}"> @endif
                            @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
                            
                            {{-- INPUT FIELD --}}
                            {{-- NOTE: Saya tambahkan style="padding-left: 3rem" untuk memaksa teks geser ke kanan --}}
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Search student name or ID..." 
                                   style="padding-left: 2.5rem;"
                                   class="w-full h-11 pr-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm shadow-sm transition-all pl-12">

                            {{-- IKON SEARCH --}}
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </form>
                    </div>

                    {{-- BARIS 2: FILTERS & ADD BUTTON --}}
                    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">

                        {{-- BAGIAN KIRI: FILTER FORM --}}
                        <form action="{{ route('admin.student.index') }}" method="GET" class="flex flex-wrap items-center gap-2 w-full lg:w-auto">
                            
                            {{-- HIDDEN INPUT SEARCH (Supaya filter tidak mereset search) --}}
                            @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif

                            {{-- 1. FILTER TAHUN --}}
                            <div class="relative flex-grow sm:flex-grow-0">
                                <select name="academic_year" onchange="this.form.submit()" 
                                        class="appearance-none h-10 w-full sm:w-auto px-3 py-2 pr-8 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 bg-gray-50 focus:ring-2 focus:ring-blue-500 cursor-pointer">
                                    <option value="">All Years</option>
                                    @foreach($years as $year)
                                        <option value="{{ $year }}" {{ request('academic_year') == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- 2. FILTER CATEGORY --}}
                            <div class="relative flex-grow sm:flex-grow-0">
                                <select name="category" onchange="this.form.submit()" 
                                        class="appearance-none h-10 w-full sm:w-auto px-3 py-2 pr-8 border border-gray-300 rounded-lg text-sm text-gray-700 bg-white focus:ring-2 focus:ring-blue-500 cursor-pointer">
                                    <option value="">Category</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                                            {{ ucwords(str_replace('_', ' ', $cat)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- 3. FILTER KELAS --}}
                            <div class="relative flex-grow sm:flex-grow-0">
                                <select name="class_id" onchange="this.form.submit()" 
                                        class="appearance-none h-10 w-full sm:w-auto px-3 py-2 pr-8 border border-gray-300 rounded-lg text-sm text-gray-700 bg-white focus:ring-2 focus:ring-blue-500 cursor-pointer max-w-[200px] truncate">
                                    <option value="">All Classes</option>
                                    <option value="no_class" class="text-red-600 font-semibold" {{ request('class_id') == 'no_class' ? 'selected' : '' }}>
                                        ⚠ No Class
                                    </option>
                                    @forelse($classes as $classItem)
                                        <option value="{{ $classItem->id }}" {{ request('class_id') == $classItem->id ? 'selected' : '' }}>
                                            {{ $classItem->name }}
                                        </option>
                                    @empty
                                        @if(request('class_id') != 'no_class')
                                            <option value="" disabled>No classes</option>
                                        @endif
                                    @endforelse
                                </select>
                            </div>

                            {{-- 4. SORT BY --}}
                            <div class="relative flex-grow sm:flex-grow-0">
                                <select name="sort" onchange="this.form.submit()" 
                                        class="appearance-none h-10 w-full sm:w-auto px-3 py-2 pr-8 border border-gray-300 rounded-lg text-sm text-gray-700 bg-white focus:ring-2 focus:ring-blue-500 cursor-pointer">
                                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
                                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>A-Z</option>
                                    <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Z-A</option>
                                </select>
                            </div>

                            {{-- 5. RESET BUTTON --}}
                            @if(request('class_id') || request('academic_year') || request('category') || request('sort') || request('search'))
                                <a href="{{ route('admin.student.index') }}" 
                                   class="h-10 w-10 flex items-center justify-center bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 rounded-lg transition-colors flex-shrink-0" 
                                   title="Reset Filters">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </a>
                            @endif
                        </form>

                        {{-- BAGIAN KANAN: ADD BUTTON --}}
                        <div class="w-full lg:w-auto">
                            <a href="{{ route('admin.student.add') }}" 
                               class="inline-flex w-full lg:w-auto items-center justify-center gap-2 px-5 h-10 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium text-sm shadow-sm whitespace-nowrap">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                                Add New Student
                            </a>
                        </div>

                    </div>
                </div>

                {{-- Table --}}
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

                            @php
                                $startNumber = ($students->currentPage() - 1) * $students->perPage() + 1;
                            @endphp

                            @forelse ($students as $index => $student)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    
                                    {{-- 1. No (Auto Increment) --}}
                                    <td class="px-6 py-4 text-gray-500 font-medium whitespace-nowrap">
                                        {{ $startNumber + $index }}
                                    </td>

                                    {{-- 2. Student Number --}}
                                    <td class="px-6 py-4 font-bold text-gray-900 whitespace-nowrap">
                                        {{ $student->student_number ?? $student->student_id }}
                                    </td>

                                    {{-- 3. Name --}}
                                    <td class="px-6 py-4 font-semibold text-gray-800 whitespace-nowrap">
                                        {{ $student->name }}
                                    </td>

                                    {{-- 4. Gender --}}
                                    <td class="px-6 py-4 capitalize whitespace-nowrap">
                                        {{ $student->gender }}
                                    </td>

                                    {{-- 5. Phone --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $student->phone ?? '-' }}
                                    </td>

                                    {{-- 6. Address --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $student->address ?? '-' }}
                                    </td>

                                    {{-- 7. Class Name (Relasi) --}}
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        @if($student->classModel)
                                            {{-- Jika Punya Kelas (Biru) --}}
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $student->classModel->name }}
                                            </span>
                                        @else
                                            {{-- Jika Tidak Punya Kelas / NULL (Merah) --}}
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-600 border border-red-200">
                                                Unassigned
                                            </span>
                                        @endif
                                    </td>

                                    {{-- 8. Is Active (Status) --}}
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        @if($student->is_active == 1 || $student->status == 'active')
                                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">
                                                Active
                                            </span>
                                        @else
                                            <span class="px-3 py-1 bg-gray-100 text-gray-500 rounded-full text-xs font-bold">
                                                Inactive
                                            </span>
                                        @endif
                                    </td>

                                    {{-- 9. Actions --}}
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-3">
                                            
                                            {{-- View --}}
                                            <a href="{{ route('admin.student.detail', $student->id) }}" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="View Details">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                            </a>

                                            {{-- Edit --}}
                                            <a href="{{ route('admin.student.edit', $student->id) }}" class="p-1.5 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors" title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                            </a>

                                            {{-- C. Toggle Status (GANTIKAN DELETE) --}}
                                            <form action="{{ route('admin.student.toggleStatus', $student->id) }}" method="POST" class="inline-block">
                                                @csrf
                                                @method('PATCH')
                                                
                                                @if($student->is_active)
                                                    {{-- Tombol Matikan (Merah) --}}
                                                    <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Deactivate Student">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                                    </button>
                                                @else
                                                    {{-- Tombol Hidupkan (Hijau) --}}
                                                    <button type="submit" class="p-1.5 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors" title="Activate Student">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    </button>
                                                @endif
                                            </form>

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-10 text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                            <p class="text-base font-medium">No students found.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>

                {{-- Pagination Manual --}}
                <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between bg-white">
                    @if ($students->onFirstPage())
                        <button class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-400 bg-white cursor-not-allowed" disabled>Previous</button>
                    @else
                        <a href="{{ $students->previousPageUrl() }}" class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-600 bg-white hover:bg-gray-50 hover:text-gray-800 transition-colors">Previous</a>
                    @endif
                    
                    <span class="text-sm text-gray-500 font-medium">Page {{ $students->currentPage() }} of {{ $students->lastPage() }}</span>
                    
                    @if ($students->hasMorePages())
                        <a href="{{ $students->nextPageUrl() }}" class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-600 bg-white hover:bg-gray-50 hover:text-gray-800 transition-colors">Next</a>
                    @else
                        <button class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-400 bg-white cursor-not-allowed" disabled>Next</button>
                    @endif
                </div>

            </div>
        </div>
    </div>

    {{-- Script SweetAlert untuk Delete --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', function(e){
                e.preventDefault();
                let form = this.closest('form');
                Swal.fire({
                    title: "Delete Student?",
                    text: "This action cannot be undone.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#EF4444",
                    cancelButtonColor: "#6B7280",
                    confirmButtonText: "Yes, delete",
                    cancelButtonText: "Cancel"
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                })
            });
        });
    </script>
</x-app-layout>