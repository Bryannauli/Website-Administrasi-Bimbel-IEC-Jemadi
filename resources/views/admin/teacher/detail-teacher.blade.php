<x-app-layout>
    <x-slot name="header"></x-slot>

    @php
        // Normalisasi variabel
        $isTrashed = isset($isTrashed) && $isTrashed;
    @endphp

    {{-- WRAPPER UTAMA --}}
    <div class="py-6" x-data="{ 
        // 1. MODAL STATE
        showEditModal: {{ !$isTrashed && $errors->any() ? 'true' : 'false' }},
        
        // 2. FORM DATA (Hanya jika Active)
        @if(!$isTrashed)
        editForm: {
            name: '{{ addslashes($teacher->name) }}',
            username: '{{ $teacher->username }}',
            email: '{{ $teacher->email }}',
            phone: '{{ $teacher->phone ?? '' }}',
            status: {{ $teacher->is_active ? 'true' : 'false' }},
            address: '{{ preg_replace( "/\r|\n/", " ", addslashes($teacher->address ?? '') ) }}'
        },
        @endif

        // 3. URL HELPER (Untuk toggle role)
        toggleRoleUrl: '{{ route('admin.teacher.toggleRole', $teacher->id) }}', 

        // 4. FUNGSI CLOSE MODAL
        closeModal(modalVar) {
            if ({{ $errors->any() ? 'true' : 'false' }}) {
                window.location.href = window.location.href.split('?')[0]; 
            } else {
                this[modalVar] = false;
            }
        },

        // 5. FUNGSI DELETE (SweetAlert)
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
                    // Submit form hidden yang ada di bawah
                    document.getElementById('delete-teacher-form').submit();
                }
            });
        },

        // 6. FUNGSI TOGGLE ROLE
        confirmToggleRole() {
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
                confirmButtonColor: '#7C3AED', 
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
                    
                    @if($isTrashed)
                        {{-- CONTEXT: DARI TONG SAMPAH --}}
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                <a href="{{ route('admin.trash.index') }}" class="ml-1 text-sm font-medium text-gray-500 hover:text-blue-600 md:ml-2">Trash Bin</a>
                            </div>
                        </li>
                    @elseif(isset($ref) && $ref === 'class' && isset($refClass))
                        {{-- CONTEXT: DARI DETAIL KELAS --}}
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
                        {{-- CONTEXT: DARI LIST TEACHER --}}
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                <a href="{{ route('admin.teacher.index') }}" class="ml-1 text-sm font-medium text-gray-500 hover:text-blue-600 md:ml-2">Teachers</a>
                            </div>
                        </li>
                    @endif
                    
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            <span class="ml-1 text-sm font-medium text-gray-900 md:ml-2 truncate max-w-[150px] md:max-w-xs">
                                {{ $teacher->name }} {{ $isTrashed ? '(Deleted)' : '' }}
                            </span>
                        </div>
                    </li>
                </ol>
            </nav>

            {{-- HEADER TITLE & ACTION BUTTONS --}}
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                <h2 class="text-3xl font-bold bg-gradient-to-r {{ $isTrashed ? 'from-gray-500 to-gray-700' : 'from-blue-600 to-indigo-600' }} bg-clip-text text-transparent inline-block">
                    {{ $isTrashed ? 'Deleted Teacher Profile' : 'Teacher Profile' }}
                </h2>
                
                <div class="flex items-center gap-3">
                    @if($isTrashed)
                        {{-- TOMBOL RESTORE --}}
                        <form action="{{ route('admin.trash.restore', ['type' => 'teacher', 'id' => $teacher->id]) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium transition-colors shadow-sm flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                Restore Teacher
                            </button>
                        </form>
                    @else
                        {{-- TOMBOL NORMAL --}}
                        @if(Auth::id() !== $teacher->id)
                            <button type="button" @click="confirmToggleRole()"
                                class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 text-sm font-medium transition-colors shadow-sm flex items-center gap-2"
                                title="{{ $teacher->role === 'admin' ? 'Change to Teacher' : 'Make Admin' }}">
                                @if($teacher->role === 'admin')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                    Change to Teacher
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Make Admin
                                @endif
                            </button>

                            <button @click="showEditModal = true" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium transition-colors shadow-sm flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                Edit Teacher
                            </button>
                        @else
                            <div class="px-4 py-2 bg-gray-100 text-gray-500 rounded-lg text-sm font-bold border border-gray-200 flex items-center gap-2 cursor-not-allowed select-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                This is You
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            {{-- ALERT WARNING --}}
            @if($isTrashed)
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0"><svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg></div>
                        <div class="ml-3"><p class="text-sm text-red-700">This teacher account was <strong>deleted</strong> on {{ $teacher->deleted_at->format('d M Y, H:i') }}.<br>Restore to activate.</p></div>
                    </div>
                </div>
            @endif

            {{-- INFO CARD UTAMA --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8 relative overflow-hidden z-0">
                <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-bl-full -mr-8 -mt-8"></div>
                <div class="flex flex-col md:flex-row items-center justify-between relative gap-6">
                    <div class="flex items-center gap-6">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($teacher->name) }}&background=2563EB&color=fff&size=128&bold=true" alt="{{ $teacher->name }}" class="w-20 h-20 md:w-24 md:h-24 rounded-full border-4 border-white shadow-md bg-white {{ $isTrashed ? 'grayscale' : '' }}">
                        <div>
                            <div class="flex items-center gap-2">
                                <h1 class="text-2xl font-bold text-gray-900">{{ $teacher->name }}</h1>
                                @if($teacher->role === 'admin') <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-700 border border-purple-200 uppercase tracking-wide">Admin</span> @endif
                            </div>
                            <div class="flex items-center gap-2 mt-2">
                                @if($isTrashed) <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 border border-red-200">DELETED</span>
                                @elseif($teacher->is_active) <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 border border-green-200">Active</span>
                                @else <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-500 border border-gray-200">Inactive</span>
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
            
            {{-- INFORMASI KELAS (Hanya Tampil Jika Tidak Trashed - Opsional) --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Assigned Classes</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-xs font-bold text-blue-600 uppercase tracking-widest mb-3">As Form Teacher ({{ $teacher->formClasses->count() }})</h4>
                        <div class="flex flex-wrap gap-2 max-h-[200px] overflow-y-auto custom-scrollbar pr-2">
                            @forelse($teacher->formClasses as $class)
                                <a href="{{ route('admin.classes.detailclass', $class->id) }}" class="px-3 py-1.5 bg-blue-50 text-blue-700 border border-blue-100 rounded-lg text-sm font-bold hover:bg-blue-100 transition shadow-sm {{ $isTrashed ? 'opacity-70' : '' }}">{{ $class->name }}</a>
                            @empty <p class="text-sm text-gray-400 italic">No classes assigned.</p>
                            @endforelse
                        </div>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-purple-600 uppercase tracking-widest mb-3">As Local Teacher ({{ $teacher->localClasses->count() }})</h4>
                        <div class="flex flex-wrap gap-2 max-h-[200px] overflow-y-auto custom-scrollbar pr-2">
                            @forelse($teacher->localClasses as $class)
                                <a href="{{ route('admin.classes.detailclass', $class->id) }}" class="px-3 py-1.5 bg-purple-50 text-purple-700 border border-purple-100 rounded-lg text-sm font-bold hover:bg-purple-100 transition shadow-sm {{ $isTrashed ? 'opacity-70' : '' }}">{{ $class->name }}</a>
                            @empty <p class="text-sm text-gray-400 italic">No classes assigned.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- GRID INFO & STATS --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                <div class="lg:col-span-1 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 h-fit">
                    <h3 class="text-lg font-bold text-gray-800 mb-6 border-b pb-2">Contact Info</h3>
                    <div class="space-y-4">
                        <div><p class="text-xs text-gray-400 uppercase tracking-wide font-semibold">Email</p><p class="text-sm font-medium text-gray-800">{{ $teacher->email }}</p></div>
                        <div><p class="text-xs text-gray-400 uppercase tracking-wide font-semibold">Username</p><p class="text-sm font-medium text-gray-800">{{ $teacher->username }}</p></div>
                        <div><p class="text-xs text-gray-400 uppercase tracking-wide font-semibold">Phone</p><p class="text-sm font-medium text-gray-800">{{ $teacher->phone ?? '-' }}</p></div>
                        <div><p class="text-xs text-gray-400 uppercase tracking-wide font-semibold">Address</p><p class="text-sm font-medium text-gray-800">{{ $teacher->address ?? '-' }}</p></div>
                    </div>
                </div>

                <div class="lg:col-span-2 flex flex-col gap-6">
                    {{-- FILTER & STATS (Sama seperti sebelumnya) --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                        <form action="{{ $isTrashed ? route('admin.trash.teacher.detail', $teacher->id) : route('admin.teacher.detail', $teacher->id) }}" method="GET" class="flex flex-col sm:flex-row items-end gap-4">
                            @if(isset($ref)) <input type="hidden" name="ref" value="{{ $ref }}"> @endif
                            @if(isset($refClass)) <input type="hidden" name="class_id" value="{{ $refClass->id }}"> @endif
                            <div class="w-full sm:w-auto"><label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Start Date</label><input type="date" name="start_date" value="{{ $startDate }}" class="w-full sm:w-40 text-sm border-gray-300 rounded-lg"></div>
                            <div class="w-full sm:w-auto"><label class="text-xs font-bold text-gray-500 uppercase mb-1 block">End Date</label><input type="date" name="end_date" value="{{ $endDate }}" class="w-full sm:w-40 text-sm border-gray-300 rounded-lg"></div>
                            <button type="submit" class="w-full sm:w-auto px-6 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 h-[38px]">Apply Filter</button>
                        </form>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col flex-grow">
                        <div class="flex items-center justify-between mb-6"><h3 class="text-lg font-bold text-gray-800">Teaching Activity</h3><div class="text-xs font-medium text-blue-600 bg-blue-50 px-3 py-1 rounded-lg border border-blue-100">{{ \Carbon\Carbon::parse($startDate)->format('d M') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</div></div>
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="p-4 rounded-xl bg-blue-50 border border-blue-100 flex flex-col items-center"><span class="text-3xl font-bold text-blue-700">{{ $summary['total_sessions'] }}</span><span class="text-xs text-blue-600 font-bold uppercase mt-1">Sessions Taught</span></div>
                            <div class="p-4 rounded-xl bg-indigo-50 border border-indigo-100 flex flex-col items-center"><span class="text-3xl font-bold text-indigo-700">{{ $summary['unique_classes'] }}</span><span class="text-xs text-indigo-600 font-bold uppercase mt-1">Classes Handled</span></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TIMELINE HISTORY --}}
            <div class="mb-10">
                <div class="flex items-center justify-between mb-4"><h3 class="text-lg font-bold text-gray-800">Teaching History</h3><span class="text-xs text-gray-400">&larr; Scroll left for earliest</span></div>
                <div id="attendance-timeline" class="flex overflow-x-auto gap-4 pb-4 custom-scrollbar scroll-smooth" style="scrollbar-width: thin;">
                    @forelse ($history as $session)
                        @php $role = ($session->teacher_id == $session->form_teacher_id) ? 'Form Teacher' : (($session->teacher_id == $session->local_teacher_id) ? 'Local Teacher' : 'Co-Teacher'); $tagClass = ($role == 'Form Teacher') ? 'bg-blue-600' : (($role == 'Local Teacher') ? 'bg-purple-600' : 'bg-gray-400'); @endphp
                        <div class="min-w-[160px] bg-white border border-gray-200 rounded-xl p-4 flex flex-col justify-between shadow-sm hover:shadow-lg transition-shadow flex-shrink-0">
                            <div>
                                <span class="text-xs text-gray-400 font-semibold uppercase mb-1 block">{{ \Carbon\Carbon::parse($session->date)->format('D, d M') }}</span>
                                <span class="text-md font-bold text-gray-800 mb-2 leading-tight line-clamp-2" title="{{ $session->class_name }}">{{ $session->class_name }}</span>
                                <div class="text-[10px] text-gray-500 bg-gray-50 px-2 py-1 rounded inline-block">{{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}</div>
                            </div>
                            <div class="mt-3"><span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold leading-4 {{ $tagClass }} text-white shadow-md shadow-black/10">{{ $role }}</span></div>
                        </div>
                    @empty <div class="w-full text-center py-8 text-gray-400 bg-gray-50 rounded-xl border border-dashed border-gray-200">No teaching sessions found.</div>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- MODAL & FORM (Hanya Render Jika Aktif) --}}
        @if(!$isTrashed)
            @include('admin.teacher.partials.edit-teacher-modal')

            {{-- FORM HIDDEN UNTUK DELETE (Penting!) --}}
            <form id="delete-teacher-form" action="{{ route('admin.teacher.delete', $teacher->id) }}" method="POST" style="display: none;">
                @csrf 
                @method('DELETE')
            </form>

            {{-- FORM HIDDEN UNTUK TOGGLE ROLE --}}
            <form id="toggle-role-form" :action="toggleRoleUrl" method="POST" style="display: none;">
                @csrf 
                @method('PATCH')
            </form>
        @endif

    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const container = document.getElementById('attendance-timeline');
            if(container) container.scrollLeft = 0; 
        });
    </script>
    
    {{-- SWEETALERT --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const successMessage = "{{ session('success') }}";
            const errorMessage = "{{ session('error') }}";
            if (successMessage) Swal.fire({icon: 'success', title: 'Success!', text: successMessage, timer: 3000, showConfirmButton: false});
            if (errorMessage) Swal.fire({icon: 'error', title: 'Error!', text: errorMessage});
        });
    </script>
</x-app-layout>