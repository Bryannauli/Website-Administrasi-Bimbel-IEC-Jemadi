<x-app-layout>
    <x-slot name="header"></x-slot>

    {{-- KONTEN UTAMA (Background Biru Muda - Sama persis dengan Student) --}}
    <div x-data="{ 
        isAddModalOpen: false, 
        showEditModal: false,
        showDeleteModal: false,
        editForm: {
            id: '',
            name: '',
            username: '',
            email: '',
            phone: '',
            type: '',
            status: '',
            address: ''
        },
        updateUrl: '',
        deleteUrl: '',
        deleteName: '',
        
        // Fungsi untuk memuat data guru ke form edit
        loadEditModal(teacher) {
            // ... (Kode loadEditModal yang sudah ada) ...
            this.editForm.id = teacher.id;
            this.editForm.name = teacher.name;
            this.editForm.username = teacher.username;
            this.editForm.email = teacher.email;
            this.editForm.phone = teacher.phone ?? '';
            this.editForm.type = teacher.type;
            this.editForm.status = teacher.is_active ? '1' : '0';
            this.editForm.address = teacher.address ?? '';
            this.updateUrl = `/admin/teachers/${teacher.id}`;
            
            this.showEditModal = true;
        },

        // Fungsi BARU untuk memuat data guru ke modal delete
        loadDeleteModal(teacher) {
            this.deleteName = teacher.name;
            this.deleteUrl = `/admin/teachers/${teacher.id}`; // Gunakan route DELETE
            this.showDeleteModal = true;
        },
        
        // Fungsi closeModal yang diperlukan oleh partial
        closeModal(modalVar) {
            this[modalVar] = false;
            // Opsional: Reset URL jika ada error/query string
            if (window.location.search.includes('error')) {
                window.location.href = window.location.href.split('?')[0]; 
            }
        }
    }" class="py-6 bg-[#F3F4FF] min-h-screen font-sans">

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
                            <span class="ml-1 text-sm font-medium text-gray-900 md:ml-2">Teachers</span>
                        </div>
                    </li>
                </ol>
            </nav>

            {{-- 2. TITLE --}}
            <div class="mb-8">
                <h1 class="text-3xl font-bold bg-gradient-to-b from-blue-500 to-red-500 bg-clip-text text-transparent">
                    Teachers Data
                </h1>
            </div>

          {{-- 3. STATS CARD (Compact Version) --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 border-l-4 border-l-blue-600 p-4 mb-8 max-w-sm">
                <div class="flex items-center justify-between gap-4">
                    
                    {{-- Kiri: Total Utama --}}
                    <div>
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Total Teachers</h3>
                        <p class="text-3xl font-bold text-gray-900 leading-none">
                            {{ number_format($totalTeachers ?? 0) }}
                        </p>
                    </div>

                    {{-- Kanan: Active & Inactive (Atas Bawah lebih Rapat) --}}
                    <div class="flex flex-col gap-1.5">
                        {{-- Active --}}
                        <div class="flex items-center justify-between gap-3 px-2.5 py-1 bg-blue-50 text-blue-700 rounded-md border border-blue-100 min-w-[110px]">
                            <div class="flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                                <span class="text-[10px] font-bold uppercase">Active</span>
                            </div>
                            <span class="text-sm font-bold">{{ number_format($totalActive ?? 0) }}</span>
                        </div>

                        {{-- Inactive --}}
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
            {{-- 4. TABLE SECTION CONTAINER --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

                {{-- HEADER ACTIONS (SEARCH & FILTER) --}}
                <div class="p-4 sm:p-6 border-b border-gray-200 flex flex-col gap-4">
                    
                    {{-- BARIS 1: SEARCH BAR (Full Width) --}}
                    <div class="w-full">
                        <form action="{{ route('admin.teacher.index') }}" method="GET" class="relative w-full">
                            {{-- Hidden Inputs untuk menjaga filter lain --}}
                            @if(request('year')) <input type="hidden" name="year" value="{{ request('year') }}"> @endif
                            @if(request('type')) <input type="hidden" name="type" value="{{ request('type') }}"> @endif
                            @if(request('class_id')) <input type="hidden" name="class_id" value="{{ request('class_id') }}"> @endif
                            
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Search teacher name or ID..." 
                                   class="w-full h-11 pl-12 pr-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm shadow-sm transition-all">

                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </form>
                    </div>

                    {{-- BARIS 2: FILTERS & ADD BUTTON --}}
                    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">

                        {{-- FILTER DROPDOWNS --}}
                        <form action="{{ route('admin.teacher.index') }}" method="GET" class="flex flex-wrap items-center gap-2 w-full lg:w-auto">
                            @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif

                            {{-- Filter Tahun --}}
                            <div class="relative flex-grow sm:flex-grow-0">
                                <select name="year" onchange="this.form.submit()" 
                                        class="appearance-none h-10 w-full sm:w-auto px-3 py-2 pr-8 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 bg-gray-50 focus:ring-2 focus:ring-blue-500 cursor-pointer">
                                    <option value="">All Years</option>
                                    @foreach($years as $year)
                                        <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Filter Type --}}
                            <div class="relative flex-grow sm:flex-grow-0">
                                <select name="type" onchange="this.form.submit()" 
                                        class="appearance-none h-10 w-full sm:w-auto px-3 py-2 pr-8 border border-gray-300 rounded-lg text-sm text-gray-700 bg-white focus:ring-2 focus:ring-blue-500 cursor-pointer">
                                    <option value="">All Types</option>
                                    @foreach($types as $t)
                                        <option value="{{ $t }}" {{ request('type') == $t ? 'selected' : '' }}>{{ $t }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Filter Kelas --}}
                            <div class="relative flex-grow sm:flex-grow-0">
                                <select name="class_id" onchange="this.form.submit()" 
                                        class="appearance-none h-10 w-full sm:w-auto px-3 py-2 pr-8 border border-gray-300 rounded-lg text-sm text-gray-700 bg-white focus:ring-2 focus:ring-blue-500 cursor-pointer max-w-[200px] truncate">
                                    <option value="">All Classes</option>
                                    <option value="no_class" class="text-red-600 font-semibold" {{ request('class_id') == 'no_class' ? 'selected' : '' }}>⚠ No Class</option>
                                    @foreach($classes as $c)
                                        <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Reset Button --}}
                            @if(request('class_id') || request('year') || request('type') || request('search'))
                                <a href="{{ route('admin.teacher.index') }}" class="h-10 w-10 flex items-center justify-center bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 rounded-lg transition-colors flex-shrink-0" title="Reset Filters">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </a>
                            @endif
                        </form>

                        {{-- ADD BUTTON --}}
                        <div class="w-full lg:w-auto">
                            <button type="button" 
                                @click="isAddModalOpen = true" 
                                class="inline-flex w-full lg:w-auto items-center justify-center gap-2 px-5 h-10 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium text-sm shadow-sm whitespace-nowrap">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                                Add New Teacher
                            </button>
                        </div>
                    </div>
                </div>

                {{-- 5. TABLE --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-max">
                        <thead class="bg-gray-50 text-xs text-gray-500 font-bold uppercase border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 whitespace-nowrap w-16">No</th>
                                <th class="px-6 py-4 whitespace-nowrap">Teacher ID</th>
                                <th class="px-6 py-4 whitespace-nowrap">Name</th>
                                <th class="px-6 py-4 whitespace-nowrap">Type</th>
                                <th class="px-6 py-4 whitespace-nowrap text-center">Status</th>
                                <th class="px-6 py-4 whitespace-nowrap text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm text-gray-700">

                            @php
                                $startNumber = ($teachers->currentPage() - 1) * $teachers->perPage() + 1;
                            @endphp

                            @forelse ($teachers as $index => $teacher)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    {{-- No --}}
                                    <td class="px-6 py-4 text-gray-500 font-medium whitespace-nowrap">
                                        {{ $startNumber + $index }}
                                    </td>

                                    {{-- ID --}}
                                    <td class="px-6 py-4 font-bold text-gray-900 whitespace-nowrap">
                                        {{ $teacher->nip ?? $teacher->id }}
                                    </td>

                                    {{-- Name --}}
                                    <td class="px-6 py-4 font-semibold text-gray-800 whitespace-nowrap">
                                        {{ $teacher->name }}
                                    </td>

                                    {{-- Type --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $isForm = \App\Models\ClassModel::where('form_teacher_id', $teacher->id)->exists();
                                            $isLocal = \App\Models\ClassModel::where('local_teacher_id', $teacher->id)->exists();
                                        @endphp

                                        @if($isForm)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Form Teacher</span>
                                        @elseif($isLocal)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">Local Teacher</span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        @if($teacher->is_active ?? true)
                                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">Active</span>
                                        @else
                                            <span class="px-3 py-1 bg-gray-100 text-gray-500 rounded-full text-xs font-bold">Inactive</span>
                                        @endif
                                    </td>

                                    {{-- Action --}}
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-3">
                                            
                                            {{-- View --}}
                                            <a href="{{ route('admin.teacher.show', $teacher->id) }}" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="View Details">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                            </a>

                                            {{-- Edit --}}
                                            <button @click="loadEditModal({
                                                id: '{{ $teacher->id }}',
                                                name: '{{ addslashes($teacher->name) }}',
                                                username: '{{ $teacher->username }}',
                                                email: '{{ $teacher->email }}',
                                                phone: '{{ $teacher->phone }}',
                                                type: '{{ $teacher->type }}',
                                                is_active: {{ $teacher->is_active ?? 'true' }},
                                                address: '{{ preg_replace( "/\r|\n/", " ", addslashes($teacher->address ?? '') ) }}'
                                            })" class="p-1.5 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors" title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                            </button>

                                            {{-- Delete --}}
                                            <button @click="loadDeleteModal({
                                                id: '{{ $teacher->id }}',
                                                name: '{{ addslashes($teacher->name) }}'
                                            })" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Deactivate">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                            <p class="text-base font-medium">No teachers found.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>

                {{-- 6. PAGINATION (TOMBOL PREVIOUS & NEXT) --}}
                <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between bg-white">
                    
                    {{-- Tombol Previous --}}
                    @if ($teachers->onFirstPage())
                        <button class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-400 bg-gray-50 cursor-not-allowed" disabled>
                            Previous
                        </button>
                    @else
                        <a href="{{ $teachers->previousPageUrl() }}" class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-600 bg-white hover:bg-gray-50 hover:text-gray-800 transition-colors">
                            Previous
                        </a>
                    @endif
                    
                    {{-- Info Halaman (Tengah) --}}
                    <span class="text-sm text-gray-500 font-medium">
                        Page {{ $teachers->currentPage() }} of {{ $teachers->lastPage() }}
                    </span>
                    
                    {{-- Tombol Next --}}
                    @if ($teachers->hasMorePages())
                        <a href="{{ $teachers->nextPageUrl() }}" class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-600 bg-white hover:bg-gray-50 hover:text-gray-800 transition-colors">
                            Next
                        </a>
                    @else
                        <button class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-400 bg-gray-50 cursor-not-allowed" disabled>
                            Next
                        </button>
                    @endif

                </div>

            </div>
        </div>
        <div x-show="isAddModalOpen" 
            x-transition:enter="ease-out duration-300" 
            x-transition:enter-start="opacity-0" 
            x-transition:enter-end="opacity-100" 
            x-transition:leave="ease-in duration-200" 
            x-transition:leave-start="opacity-100" 
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50">
        
            <div class="flex items-center justify-center min-h-screen p-4" @click.outside="isAddModalOpen = false">
                
                <div x-show="isAddModalOpen"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="bg-white rounded-[20px] shadow-2xl w-full max-w-4xl p-8 transform transition-all mx-auto relative">
                    
                    {{-- Tombol Close Modal --}}
                    <button type="button" @click="isAddModalOpen = false" 
                            class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 focus:outline-none z-10">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <h2 class="text-2xl font-bold text-gray-900 mb-6 pb-4 border-b border-gray-100">Add New Teacher</h2>
                    
                    {{-- Form Start (Konten LENGKAP dari file add.blade.php) --}}
                    <form action="{{ route('admin.teacher.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <h3 class="text-xl font-bold text-gray-800 mb-6 pb-4 border-b border-gray-100">Teacher Information</h3>

                        {{-- Grid Layout untuk Field --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

                            {{-- 1. Name & Username --}}
                            <div class="space-y-2">
                                <label for="name" class="text-sm font-semibold text-gray-700">Full Name <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="name" required placeholder="e.g. Richard Lim"
                                    class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-200 transition duration-200 outline-none text-gray-700 placeholder-gray-400">
                            </div>

                            <div class="space-y-2">
                                <label for="username" class="text-sm font-semibold text-gray-700">Username <span class="text-red-500">*</span></label>
                                <input type="text" name="username" id="username" required placeholder="e.g. Richardlim7"
                                    class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-200 transition duration-200 outline-none text-gray-700 placeholder-gray-400">
                            </div>

                            {{-- 2. Email & Phone --}}
                            <div class="space-y-2">
                                <label for="email" class="text-sm font-semibold text-gray-700">Email Address <span class="text-red-500">*</span></label>
                                <input type="email" name="email" id="email" required placeholder="e.g. teacher@school.com"
                                    class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-200 transition duration-200 outline-none text-gray-700 placeholder-gray-400">
                            </div>

                            <div class="space-y-2">
                                <label for="phone" class="text-sm font-semibold text-gray-700">Phone Number <span class="text-red-500">*</span></label>
                                <input type="tel" name="phone" id="phone" required placeholder="e.g. 08123456789"
                                    class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-200 transition duration-200 outline-none text-gray-700 placeholder-gray-400">
                            </div>

                            {{-- 3. Password & Type --}}
                            <div class="space-y-2">
                                <label for="password" class="text-sm font-semibold text-gray-700">Password <span class="text-red-500">*</span></label>
                                
                                {{-- Wrapper Relative untuk posisi icon --}}
                                <div class="relative" x-data="{ show: false }">
                                    <input 
                                        :type="show ? 'text' : 'password'" 
                                        name="password" 
                                        id="password" 
                                        required 
                                        placeholder="••••••••"
                                        class="w-full px-4 py-3 pr-12 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-200 transition duration-200 outline-none text-gray-700 placeholder-gray-400"
                                    >
                                    
                                    {{-- Tombol Toggle Icon --}}
                                    <button type="button" @click="show = !show" 
                                        class="absolute inset-y-0 right-0 px-4 flex items-center text-gray-400 hover:text-blue-600 focus:outline-none transition-colors"
                                        title="Toggle Password Visibility">
                                        
                                        {{-- Icon Mata Terbuka (Show) --}}
                                        <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>

                                        {{-- Icon Mata Coret (Hide) --}}
                                        <svg x-show="show" style="display: none;" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.05 10.05 0 011.574-2.59M5.38 5.38a10.056 10.056 0 016.62-2.38c4.478 0 8.268 2.943 9.542 7a10.05 10.05 0 01-2.033 3.51M15 12a3 3 0 00-3-3m-1.5 7.5a3 3 0 01-3-3 3 3 0 013-3m6.75-4.5L5.25 19.5" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="space-y-2">
                                <label for="type" class="text-sm font-semibold text-gray-700">Teacher Type <span class="text-red-500">*</span></label>
                                <select name="type" id="type" required
                                    class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-200 transition duration-200 outline-none text-gray-700 cursor-pointer appearance-none">
                                    <option value="">Select Type</option>
                                    <option value="Form Teacher">Form Teacher</option>
                                    <option value="Local Teacher">Local Teacher</option>
                                </select>
                            </div>
                            
                            {{-- 4. Status Dropdown (dipindahkan dari kolom 4) --}}
                            <div class="space-y-2">
                                <label for="status" class="text-sm font-semibold text-gray-700">Status <span class="text-red-500">*</span></label>
                                <select name="status" id="status" required
                                    class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-200 transition duration-200 outline-none text-gray-700 cursor-pointer appearance-none">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>

                            {{-- Kolom Kosong untuk meratakan grid --}}
                            <div></div> 

                            {{-- 5. Address (Full Width) --}}
                            <div class="space-y-2 md:col-span-2">
                                <label for="address" class="text-sm font-semibold text-gray-700"> Address</label>
                                <textarea name="address" id="address" rows="3" placeholder="Enter full address here..."
                                    class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-200 transition duration-200 outline-none text-gray-700 placeholder-gray-400 resize-none"></textarea>
                            </div>

                            {{-- 6. Photo Upload (Drag & Drop Style) --}}
                            <div class="space-y-2 md:col-span-2">
                                <span class="text-sm font-semibold text-gray-700">Profile Photo</span>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl bg-gray-50 hover:bg-blue-50 hover:border-blue-300 transition group cursor-pointer relative">
                                    <div class="space-y-2 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400 group-hover:text-blue-500 transition" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600 justify-center">
                                            <label for="photo" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500 px-2">
                                                <span>Upload a file</span>
                                                <input id="photo" name="photo" type="file" class="sr-only" accept="image/*">
                                            </label>
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">
                                            PNG, JPG, GIF up to 2MB
                                        </p>
                                    </div>
                                </div>
                            </div>

                        </div>
                        {{-- End Grid Layout --}}

                        {{-- Action Buttons --}}
                        <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-100">
                            <button type="button" @click="isAddModalOpen = false"
                                class="px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition focus:outline-none focus:ring-2 focus:ring-gray-300">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-blue-800 transition shadow-lg shadow-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Save Teacher
                            </button>
                        </div>

                    </form>
                {{-- Form End --}}

            </div>
        </div>
    </div>
    @include('admin.teacher.partials.teacher-edit-modal')
    @include('admin.teacher.partials.teacher-delete-modal')
</x-app-layout>