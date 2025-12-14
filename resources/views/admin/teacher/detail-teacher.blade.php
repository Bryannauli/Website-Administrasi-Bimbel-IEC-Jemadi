<x-app-layout>
    <x-slot name="header"></x-slot>

    {{-- WRAPPER UTAMA --}}
    <div class="py-6" x-data="{ 
        // --- 1. DATA UNTUK EDIT MODAL ---
        showEditModal: {{ $errors->any() ? 'true' : 'false' }},
        
        editForm: {
            name: '{{ addslashes($teacher->name) }}',
            username: '{{ $teacher->username }}',
            email: '{{ $teacher->email }}',
            phone: '{{ $teacher->phone ?? '' }}',
            status: {{ $teacher->is_active ? 'true' : 'false' }},
            address: '{{ preg_replace( "/\r|\n/", " ", addslashes($teacher->address ?? '') ) }}'
        },

        updateUrl: '{{ route('admin.teacher.update', $teacher->id) }}',
        deleteUrl: '{{ route('admin.teacher.delete', $teacher->id) }}',
        toggleRoleUrl: '{{ route('admin.teacher.toggleRole', $teacher->id) }}', 

        // --- 2. FUNGSI-FUNGSI ---
        closeModal(modalVar) {
            if ({{ $errors->any() ? 'true' : 'false' }}) {
                window.location.href = window.location.href.split('?')[0]; 
            } else {
                this[modalVar] = false;
            }
        },

        confirmDelete() {
            Swal.fire({
                title: 'Are you sure?',
                text: 'This teacher will be moved to trash (Soft Delete).',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-teacher-form').submit();
                }
            });
        },

        confirmToggleRole() {
            // Tentukan teks berdasarkan role saat ini
            const isCurrentlyAdmin = '{{ $teacher->role }}' === 'admin';
            
            const actionText = isCurrentlyAdmin ? 'Change to Teacher' : 'Make Admin';
            
            const confirmText = isCurrentlyAdmin 
                ? 'Are you sure you want to change this user to a regular Teacher role?'
                : 'Are you sure you want to promote this user to an Admin role?';
                
            Swal.fire({
                title: actionText + '?',
                text: confirmText,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#7C3AED', // Warna Ungu
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, ' + actionText,
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('toggle-role-form').submit();
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
                    
                    {{-- LOGIKA DINAMIS BREADCRUMB --}}
                    @if(isset($ref) && $ref === 'class' && isset($refClass))
                        {{-- NAVIGASI DARI DETAIL KELAS: Dashboard > Classes > [Nama Kelas] > Teacher Detail --}}
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                <a href="{{ route('admin.classes.index') }}" class="ml-1 text-sm font-medium text-gray-500 hover:text-blue-600 md:ml-2">Classes</a>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                <a href="{{ route('admin.classes.detailclass', $refClass->id) }}" class="ml-1 text-sm font-medium text-gray-500 hover:text-blue-600 md:ml-2 truncate max-w-[100px]">{{ $refClass->name }}</a>
                            </div>
                        </li>
                    @else
                        {{-- NAVIGASI DARI LIST GURU (DEFAULT): Dashboard > Teachers > Teacher Detail --}}
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                <a href="{{ route('admin.teacher.index') }}" class="ml-1 text-sm font-medium text-gray-500 hover:text-blue-600 md:ml-2">Teachers</a>
                            </div>
                        </li>
                    @endif
                    
                    {{-- CURRENT PAGE (Selalu Ada) --}}
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            <span class="ml-1 text-sm font-medium text-gray-900 md:ml-2 truncate max-w-[150px] md:max-w-xs">{{ $teacher->name }}</span>
                        </div>
                    </li>
                </ol>
            </nav>

            {{-- HEADER TITLE & ACTION BUTTONS --}}
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                <h2 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent inline-block">
                    Teacher Profile
                </h2>
                
                {{-- GROUP BUTTONS --}}
                <div class="flex items-center gap-3">
                    
                    {{-- 1. TOMBOL TOGGLE ROLE (UNGU) --}}
                    @if(Auth::id() !== $teacher->id)
                        <button type="button" @click="confirmToggleRole()"
                            class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 text-sm font-medium transition-colors shadow-sm flex items-center gap-2"
                            title="{{ $teacher->role === 'admin' ? 'Change to Teacher' : 'Make Admin' }}">
                            
                            @if($teacher->role === 'admin')
                                {{-- Icon Arrow Down --}}
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                Change to Teacher
                            @else
                                {{-- Icon Badge Check --}}
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Make Admin
                            @endif
                        </button>
                    @endif

                    {{-- 2. TOMBOL EDIT (BIRU) --}}
                    @if(Auth::id() !== $teacher->id)
                        <button @click="showEditModal = true" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium transition-colors shadow-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            Edit Teacher
                        </button>
                    @else
                        {{-- SELF VIEW BADGE --}}
                        <div class="px-4 py-2 bg-gray-100 text-gray-500 rounded-lg text-sm font-bold border border-gray-200 flex items-center gap-2 cursor-not-allowed select-none" title="You cannot manage your own administrative status">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            This is You
                        </div>
                    @endif
                </div>
            </div>

            {{-- INFO CARD UTAMA --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8 relative overflow-hidden z-0">
                <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-bl-full -mr-8 -mt-8"></div>
                <div class="flex flex-col md:flex-row items-center justify-between relative gap-6">
                    <div class="flex items-center gap-6">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($teacher->name) }}&background=2563EB&color=fff&size=128&bold=true"
                            alt="{{ $teacher->name }}" class="w-20 h-20 md:w-24 md:h-24 rounded-full border-4 border-white shadow-md bg-white">
                        <div>
                            <div class="flex items-center gap-2">
                                <h1 class="text-2xl font-bold text-gray-900">{{ $teacher->name }}</h1>
                                {{-- Role Badge --}}
                                @if($teacher->role === 'admin')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-700 border border-purple-200 uppercase tracking-wide">Admin</span>
                                @endif
                            </div>
                            
                            <div class="flex items-center gap-2 mt-2">
                                @if($teacher->is_active)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 border border-green-200">Active</span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-500 border border-gray-200">Inactive</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="text-right hidden md:block">
                        <p class="text-sm text-gray-500">Joined Date</p>
                        <p class="text-lg font-semibold text-gray-800">{{ $teacher->created_at->format('d M Y') }}</p>
                    </div>
                </div>
            </div>
            
            {{-- INFORMASI KELAS YANG DIAJAR (BARU) --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Assigned Classes (Active)</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    {{-- Form Teacher Classes --}}
                    <div>
                        <h4 class="text-xs font-bold text-blue-600 uppercase tracking-widest mb-3">As Form Teacher ({{ $teacher->formClasses->count() }})</h4>
                        {{-- TAMBAHKAN MAX-HEIGHT DAN SCROLL DI SINI --}}
                        <div class="flex flex-wrap gap-2 max-h-[200px] overflow-y-auto custom-scrollbar pr-2">
                            @if($teacher->formClasses->count() > 0)
                                @foreach($teacher->formClasses as $class)
                                    <a href="{{ route('admin.classes.detailclass', $class->id) }}" 
                                       class="px-3 py-1.5 bg-blue-50 text-blue-700 border border-blue-100 rounded-lg text-sm font-bold hover:bg-blue-100 transition shadow-sm">
                                        {{ $class->name }}
                                    </a>
                                @endforeach
                            @else
                                <p class="text-sm text-gray-400 italic">No classes assigned.</p>
                            @endif
                        </div>
                    </div>

                    {{-- Local Teacher Classes --}}
                    <div>
                        <h4 class="text-xs font-bold text-purple-600 uppercase tracking-widest mb-3">As Local Teacher ({{ $teacher->localClasses->count() }})</h4>
                        {{-- TAMBAHKAN MAX-HEIGHT DAN SCROLL DI SINI --}}
                        <div class="flex flex-wrap gap-2 max-h-[200px] overflow-y-auto custom-scrollbar pr-2">
                            @if($teacher->localClasses->count() > 0)
                                @foreach($teacher->localClasses as $class)
                                    <a href="{{ route('admin.classes.detailclass', $class->id) }}" 
                                       class="px-3 py-1.5 bg-purple-50 text-purple-700 border border-purple-100 rounded-lg text-sm font-bold hover:bg-purple-100 transition shadow-sm">
                                        {{ $class->name }}
                                    </a>
                                @endforeach
                            @else
                                <p class="text-sm text-gray-400 italic">No classes assigned.</p>
                            @endif
                        </div>
                    </div>

                </div>
            </div>

            {{-- GRID INFO & STATS --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                
                {{-- Kiri: Biodata --}}
                <div class="lg:col-span-1 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 h-fit">
                    <h3 class="text-lg font-bold text-gray-800 mb-6 border-b pb-2">Contact Info</h3>
                    <div class="space-y-4">
                        <div><p class="text-xs text-gray-400 uppercase tracking-wide font-semibold">Email</p><p class="text-sm font-medium text-gray-800">{{ $teacher->email }}</p></div>
                        <div><p class="text-xs text-gray-400 uppercase tracking-wide font-semibold">Username</p><p class="text-sm font-medium text-gray-800">{{ $teacher->username }}</p></div>
                        <div><p class="text-xs text-gray-400 uppercase tracking-wide font-semibold">Phone</p><p class="text-sm font-medium text-gray-800">{{ $teacher->phone ?? '-' }}</p></div>
                        <div><p class="text-xs text-gray-400 uppercase tracking-wide font-semibold">Address</p><p class="text-sm font-medium text-gray-800">{{ $teacher->address ?? '-' }}</p></div>
                    </div>
                </div>

                {{-- Kanan: Statistik Teaching --}}
                <div class="lg:col-span-2 flex flex-col gap-6">
                    
                    {{-- FORM FILTER DATE RANGE --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                        <form action="{{ route('admin.teacher.detail', $teacher->id) }}" method="GET" class="flex flex-col sm:flex-row items-end gap-4">
                            
                            {{-- Tambahkan Hidden Input untuk mempertahankan konteks navigasi --}}
                            @if(isset($ref)) <input type="hidden" name="ref" value="{{ $ref }}"> @endif
                            @if(isset($refClass)) <input type="hidden" name="class_id" value="{{ $refClass->id }}"> @endif
                            
                            {{-- Start Date --}}
                            <div class="w-full sm:w-auto">
                                <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Start Date</label>
                                <input type="date" name="start_date" value="{{ $startDate }}" 
                                       class="w-full sm:w-40 text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            {{-- End Date --}}
                            <div class="w-full sm:w-auto">
                                <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">End Date</label>
                                <input type="date" name="end_date" value="{{ $endDate }}" 
                                       class="w-full sm:w-40 text-sm border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            {{-- Filter Button --}}
                            <button type="submit" class="w-full sm:w-auto px-6 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm h-[38px] flex items-center justify-center">
                                Apply Filter
                            </button>
                            
                        </form>
                    </div>

                    {{-- STATISTIK CARD --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col flex-grow">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-gray-800">Teaching Activity</h3>
                            <div class="text-xs font-medium text-blue-600 bg-blue-50 px-3 py-1 rounded-lg border border-blue-100">
                                {{ \Carbon\Carbon::parse($startDate)->format('d M') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                            </div>
                        </div>
                        
                        {{-- Grid Angka --}}
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            {{-- Kiri: Total Sesi --}}
                            <div class="p-4 rounded-xl bg-blue-50 border border-blue-100 flex flex-col items-center">
                                <span class="text-3xl font-bold text-blue-700">{{ $summary['total_sessions'] }}</span>
                                <span class="text-xs text-blue-600 font-bold uppercase mt-1">Sessions Taught</span>
                            </div>
                            
                            {{-- Kanan: Kelas Handled --}}
                            <div class="p-4 rounded-xl bg-indigo-50 border border-indigo-100 flex flex-col items-center">
                                <span class="text-3xl font-bold text-indigo-700">{{ $summary['unique_classes'] }}</span>
                                <span class="text-xs text-indigo-600 font-bold uppercase mt-1">Classes Handled</span>
                            </div>
                        </div>

                        <div class="mt-auto p-4 bg-gray-50 rounded-lg border border-gray-100 text-sm text-gray-500">
                            <p class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>
                                    <strong>Note:</strong> Statistics shown are based on the selected date range, including completed (inactive) classes.
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TIMELINE HISTORY --}}
            <div class="mb-10">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-800">Teaching History</h3>
                    <span class="text-xs text-gray-400">&larr; Scroll left for earliest</span>
                </div>
                
                <div id="attendance-timeline" class="flex overflow-x-auto gap-4 pb-4 custom-scrollbar scroll-smooth" style="scrollbar-width: thin;">
                    @forelse ($history as $session)
                        {{-- Logic untuk menentukan Role dan Class Style --}}
                        @php
                            $role = 'Co-Teacher'; // Default jika guru mengajar tapi bukan Form/Local
                            $tagClass = 'bg-gray-400';
                            $borderClass = 'border-gray-200';
                            
                            // Cek apakah guru yang dilihat adalah Form Teacher di kelas ini?
                            if ($session->teacher_id == $session->form_teacher_id) {
                                $role = 'Form Teacher';
                                $tagClass = 'bg-blue-600'; // Biru solid
                                $borderClass = 'border-blue-400/50';
                            
                            // Cek apakah guru yang dilihat adalah Local Teacher di kelas ini?
                            } elseif ($session->teacher_id == $session->local_teacher_id) {
                                $role = 'Local Teacher';
                                $tagClass = 'bg-purple-600'; // Ungu solid
                                $borderClass = 'border-purple-400/50';
                            }
                        @endphp

                        <div class="min-w-[160px] bg-white border {{ $borderClass }} rounded-xl p-4 flex flex-col justify-between shadow-sm hover:shadow-lg transition-shadow flex-shrink-0">
                            
                            <div>
                                {{-- TANGGAL --}}
                                <span class="text-xs text-gray-400 font-semibold uppercase mb-1 block">
                                    {{ \Carbon\Carbon::parse($session->date)->format('D, d M') }}
                                </span>
                                
                                {{-- NAMA KELAS --}}
                                <span class="text-md font-bold text-gray-800 mb-2 leading-tight line-clamp-2" title="{{ $session->class_name }}">
                                    {{ $session->class_name }}
                                </span>
                                
                                {{-- WAKTU --}}
                                <div class="text-[10px] text-gray-500 bg-gray-50 px-2 py-1 rounded inline-block">
                                    {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                                </div>
                            </div>

                            {{-- TAG ROLE GURU (Style Solid) --}}
                            <div class="mt-3">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold leading-4 {{ $tagClass }} text-white shadow-md shadow-black/10">
                                    {{ $role }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="w-full text-center py-8 text-gray-400 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                            No teaching sessions found in this period.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- MODAL EDIT (PARTIAL) --}}
        @include('admin.teacher.partials.edit-teacher-modal')

        {{-- FORM HIDDEN DELETE --}}
        <form id="delete-teacher-form" action="{{ route('admin.teacher.delete', $teacher->id) }}" method="POST" style="display: none;">
            @csrf 
            @method('DELETE')
        </form>

        {{-- FORM HIDDEN TOGGLE ROLE (BARU) --}}
        <form id="toggle-role-form" :action="toggleRoleUrl" method="POST" style="display: none;">
            @csrf 
            @method('PATCH')
        </form>

    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const container = document.getElementById('attendance-timeline');
            if(container) container.scrollLeft = 0; 
        });
    </script>

    {{-- SWEETALERT SCRIPTS --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const successMessage = "{{ session('success') }}";
            const errorMessage = "{{ session('error') }}";

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
                    text: errorMessage,
                });
            }
        });
    </script>
</x-app-layout>