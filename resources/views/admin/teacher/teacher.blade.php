<x-app-layout>
    <x-slot name="header"></x-slot>

    {{-- KONTEN UTAMA --}}
    <div x-data="{ 
        // LOGIKA MODAL (FIXED)
        // Buka Add Modal HANYA jika ada error DAN BUKAN error edit
        showAddModal: {{ $errors->any() && !session('edit_error') ? 'true' : 'false' }},
        
        // Buka Edit Modal jika ada session 'edit_error'
        showEditModal: {{ session('edit_error') ? 'true' : 'false' }},
        
        // SETUP DATA FORM (Agar tidak hilang saat error)
        editForm: {
            name: '{{ old('name') }}',
            username: '{{ old('username') }}',
            email: '{{ old('email') }}',
            phone: '{{ old('phone') }}',
            address: {{ json_encode(old('address')) }}, // Encode agar aman dari enter/newline
            status: {{ old('status') == '1' ? 'true' : 'false' }} // Konversi string '1'/'0' ke boolean
        },

        // Reconstruct URL Update jika sedang dalam kondisi Error Edit
        updateUrl: '{{ session('edit_error') ? route('admin.teacher.update', session('edit_error')) : '' }}',

        // Functions
        closeModal(modalVar) {
            if ({{ $errors->any() ? 'true' : 'false' }}) {
                // Jika sedang error, bersihkan URL parameter agar modal tidak stuck terbuka
                window.location.href = window.location.href.split('?')[0]; 
            } else {
                this[modalVar] = false;
            }
        },

        openEditModal(teacher) {
            // Populate data saat tombol edit diklik (Normal Flow)
            this.editForm = {
                name: teacher.name,
                username: teacher.username,
                email: teacher.email || '',
                phone: teacher.phone || '',
                address: teacher.address || '',
                status: teacher.is_active == 1 
            };
            this.updateUrl = '{{ route('admin.teacher.update', ':id') }}'.replace(':id', teacher.id);
            this.showEditModal = true;
        }
    }" class="py-6 bg-[#F3F4FF] min-h-screen font-sans">
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- BREADCRUMB --}}
            <nav class="flex mb-5" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-blue-600 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                            Dashboard
                        </a>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            <span class="ml-1 text-sm font-medium text-gray-900 md:ml-2">Teachers</span>
                        </div>
                    </li>
                </ol>
            </nav>

            {{-- TITLE --}}
            <div class="mb-8">
                <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent inline-block">
                    Teachers Data
                </h1>
            </div>

            {{-- STATS CARD --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 border-l-4 border-l-blue-600 p-4 mb-8 max-w-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Total Teachers</h3>
                        <p class="text-3xl font-bold text-gray-900 leading-none">{{ number_format($totalTeachers ?? 0) }}</p>
                    </div>
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
                        <form action="{{ route('admin.teacher.index') }}" method="GET" class="relative w-full">
                            @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                            @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
                            
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Search teacher name, username or email..." 
                                   class="w-full h-11 pl-12 pr-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm shadow-sm transition-all">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" /></svg>
                            </div>
                        </form>
                    </div>

                    {{-- Filters & Add --}}
                    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                        <form action="{{ route('admin.teacher.index') }}" method="GET" class="flex flex-wrap items-center gap-2 w-full lg:w-auto">
                            @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif

                            {{-- Filter Status --}}
                            <div class="relative flex-grow sm:flex-grow-0">
                                <select name="status" onchange="this.form.submit()" class="appearance-none h-10 w-full sm:w-auto px-3 py-2 pr-8 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 bg-gray-50 focus:ring-2 focus:ring-blue-500 cursor-pointer">
                                    <option value="">All Status</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>

                            {{-- Filter Sort --}}
                            <div class="relative flex-grow sm:flex-grow-0">
                                <select name="sort" onchange="this.form.submit()" class="appearance-none h-10 w-full sm:w-auto px-3 py-2 pr-8 border border-gray-300 rounded-lg text-sm text-gray-700 bg-white focus:ring-2 focus:ring-blue-500 cursor-pointer">
                                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                                    <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
                                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                                </select>
                            </div>

                            {{-- Reset --}}
                            @if(request('status') || request('sort') || request('search'))
                                <a href="{{ route('admin.teacher.index') }}" class="h-10 w-10 flex items-center justify-center bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 rounded-lg transition-colors flex-shrink-0" title="Reset Filters">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </a>
                            @endif
                        </form>

                        {{-- Add Button --}}
                        <div class="w-full lg:w-auto">
                            <button type="button" @click="showAddModal = true" 
                                class="inline-flex w-full lg:w-auto items-center justify-center gap-2 px-5 h-10 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium text-sm shadow-sm whitespace-nowrap">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" /></svg>
                                Add New Teacher
                            </button>
                        </div>
                    </div>
                </div>

                {{-- TABLE CONTENT --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-max">
                        <thead class="bg-gray-50 text-xs text-gray-500 font-bold uppercase border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 whitespace-nowrap w-16">No</th>
                                <th class="px-6 py-4 whitespace-nowrap">Name</th>
                                <th class="px-6 py-4 whitespace-nowrap">Phone</th>
                                <th class="px-6 py-4 whitespace-nowrap">Email</th>
                                <th class="px-6 py-4 whitespace-nowrap text-center">Status</th>
                                <th class="px-6 py-4 whitespace-nowrap text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                            @php $startNumber = ($teachers->currentPage() - 1) * $teachers->perPage() + 1; @endphp

                            @forelse ($teachers as $index => $teacher)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 text-gray-500 font-medium whitespace-nowrap">{{ $startNumber + $index }}</td>
                                    <td class="px-6 py-4 font-semibold text-gray-800 whitespace-nowrap">{{ $teacher->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $teacher->phone ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $teacher->email ?? '-' }}</td>
                                    
                                    {{-- Status & Badge --}}
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <div class="flex flex-col items-center justify-center gap-1">
                                            @if($teacher->is_active)
                                                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">Active</span>
                                            @else
                                                <span class="px-3 py-1 bg-gray-100 text-gray-500 rounded-full text-xs font-bold">Inactive</span>
                                            @endif
                                            
                                            @php
                                                $isForm = \App\Models\ClassModel::where('form_teacher_id', $teacher->id)->exists();
                                                $isLocal = \App\Models\ClassModel::where('local_teacher_id', $teacher->id)->exists();
                                            @endphp
                                            @if ($isForm && $isLocal) <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-700 border border-purple-200">Form & Local</span>
                                            @elseif ($isForm) <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-700 border border-blue-200">Form Teacher</span>
                                            @elseif ($isLocal) <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-100 text-indigo-700 border border-indigo-200">Local Teacher</span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Actions --}}
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-3">
                                            <a href="{{ route('admin.teacher.show', $teacher->id) }}" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="View">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                            </a>
                                            <button @click='openEditModal(@json($teacher))' class="p-1.5 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors" title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                            </button>
                                            <button class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-6 py-10 text-center text-gray-500">No teachers found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between bg-white">
                    @if ($teachers->onFirstPage())
                        <button class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-400 bg-gray-50 cursor-not-allowed" disabled>Previous</button>
                    @else
                        <a href="{{ $teachers->previousPageUrl() }}" class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-600 bg-white hover:bg-gray-50 hover:text-gray-800 transition-colors">Previous</a>
                    @endif
                    <span class="text-sm text-gray-500 font-medium">Page {{ $teachers->currentPage() }} of {{ $teachers->lastPage() }}</span>
                    @if ($teachers->hasMorePages())
                        <a href="{{ $teachers->nextPageUrl() }}" class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-600 bg-white hover:bg-gray-50 hover:text-gray-800 transition-colors">Next</a>
                    @else
                        <button class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-400 bg-gray-50 cursor-not-allowed" disabled>Next</button>
                    @endif
                </div>

            </div>
        </div>

        {{-- MODALS --}}
        @include('admin.teacher.partials.add-teacher-modal')
        @include('admin.teacher.partials.edit-teacher-modal')

    </div>

    {{-- SweetAlert --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const successMessage = <?php echo json_encode(session('success')); ?>;
            const errorMessage = <?php echo json_encode(session('error')); ?>;
            if (successMessage) Swal.fire({ icon: 'success', title: 'Success!', text: successMessage, timer: 3000, showConfirmButton: false });
            if (errorMessage) Swal.fire({ icon: 'error', title: 'Error!', text: errorMessage });
        });
    </script>
</x-app-layout>