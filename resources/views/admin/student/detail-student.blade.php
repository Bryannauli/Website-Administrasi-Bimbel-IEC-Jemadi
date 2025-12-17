<x-app-layout>
    <x-slot name="header"></x-slot>

    @php
        // Normalisasi variabel agar kode lebih bersih
        $isTrashed = isset($isTrashed) && $isTrashed;
    @endphp

    {{-- WRAPPER UTAMA DENGAN ALPINE JS --}}
    <div class="py-6" x-data="{ 
        // 1. STATE MODAL
        showEditModal: {{ !$isTrashed && ($errors->any() || request('action') == 'edit') ? 'true' : 'false' }},
        
        // 2. STATE FORM DATA
        editForm: {
            student_number: '{{ $student->student_number }}',
            name: '{{ addslashes($student->name) }}',
            gender: '{{ $student->gender }}',
            phone: '{{ $student->phone ?? '' }}',
            address: '{{ preg_replace( "/\r|\n/", " ", addslashes($student->address ?? '') ) }}',
            is_active: {{ $student->is_active ? 'true' : 'false' }},
            class_id: '{{ $student->class_id ?? '' }}'
        },

        // 3. URL ACTIONS
        deleteUrl: '{{ route('admin.student.delete', $student->id) }}',

        // 4. Helper Functions
        closeModal(modalVar) {
            if ({{ $errors->any() ? 'true' : 'false' }}) {
                window.location.href = window.location.href.split('?')[0]; 
            } else {
                this[modalVar] = false;
            }
        },

        // 5. FUNGSI DELETE (Soft Delete via Modal Edit)
        confirmDelete() {
            Swal.fire({
                title: 'Are you sure?',
                text: 'This student will be moved to trash (Soft Delete).',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
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

                    @if($isTrashed)
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                <a href="{{ route('admin.trash.index') }}" class="ml-1 text-sm font-medium text-gray-500 hover:text-blue-600 md:ml-2">Trash Bin</a>
                            </div>
                        </li>
                    @elseif(request('ref') == 'class' && $student->classModel)
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                <a href="{{ route('admin.classes.index') }}" class="ml-1 text-sm font-medium text-gray-500 hover:text-blue-600 md:ml-2">Classes</a>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                <a href="{{ route('admin.classes.detailclass', $student->classModel->id) }}" class="ml-1 text-sm font-medium text-gray-500 hover:text-blue-600 md:ml-2">{{ $student->classModel->name }}</a>
                            </div>
                        </li>
                    @else
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                <a href="{{ route('admin.student.index') }}" class="ml-1 text-sm font-medium text-gray-500 hover:text-blue-600 md:ml-2">Students</a>
                            </div>
                        </li>
                    @endif

                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            <span class="ml-1 text-sm font-medium text-gray-900 md:ml-2 truncate max-w-[150px] md:max-w-xs" title="{{ $student->name }}">
                                {{ $student->name }} {{ $isTrashed ? '(Deleted)' : '' }}
                            </span>
                        </div>
                    </li>
                </ol>
            </nav>

            {{-- HEADER & ACTIONS --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <h2 class="text-3xl font-bold bg-gradient-to-r {{ $isTrashed ? 'from-gray-500 to-gray-700' : 'from-blue-600 to-indigo-600' }} bg-clip-text text-transparent inline-block">
                    {{ $isTrashed ? 'Deleted Student Profile' : 'Student Profile' }}
                </h2>
                
                @if($isTrashed)
                    <div class="flex items-center gap-3">
                        {{-- RESTORE BUTTON --}}
                        <form action="{{ route('admin.trash.restore', ['type' => 'student', 'id' => $student->id]) }}" method="POST" onsubmit="return confirmAction(event, 'restore')">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium transition-colors shadow-sm flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                Restore
                            </button>
                        </form>

                        {{-- FORCE DELETE BUTTON --}}
                        <form action="{{ route('admin.trash.force_delete', ['type' => 'student', 'id' => $student->id]) }}" method="POST" onsubmit="return confirmAction(event, 'delete')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-medium transition-colors shadow-sm flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                Delete Permanently
                            </button>
                        </form>
                    </div>
                @else
                    <button @click="showEditModal = true" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium transition-colors shadow-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        Edit Student
                    </button>
                @endif
            </div>

            {{-- ALERT JIKA DELETED --}}
            @if($isTrashed)
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">
                                This student record was <strong>deleted</strong> on {{ $student->deleted_at->format('d M Y, H:i') }}. 
                                <br>Statistics and history are preserved in Read-Only mode. Restore to edit.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- 1. INFO SISWA --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8 relative overflow-hidden z-0">
                <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-bl-full -mr-8 -mt-8"></div>
                <div class="flex flex-col md:flex-row items-center justify-between relative gap-6">
                    <div class="flex items-center gap-6">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($student->name) }}&background=2563EB&color=fff&size=128&bold=true"
                            alt="{{ $student->name }}" class="w-20 h-20 md:w-24 md:h-24 rounded-full border-4 border-white shadow-md bg-white {{ $isTrashed ? 'grayscale' : '' }}">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ $student->name }}</h1>
                            <p class="text-gray-500 font-medium">ID: {{ $student->student_number }}</p>
                            <div class="flex items-center gap-2 mt-2">
                                @if($isTrashed)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 border border-red-200">DELETED</span>
                                @elseif($student->is_active)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 border border-green-200">Active</span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-500 border border-gray-200">Inactive</span>
                                @endif

                                @if($student->classModel)
                                    <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-bold uppercase tracking-wider border border-blue-100">{{ $student->classModel->name }}</span>
                                @else
                                    <span class="px-3 py-1 bg-red-50 text-red-600 rounded-full text-xs font-bold uppercase border border-red-100">No Class</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="text-right hidden md:block">
                        <p class="text-sm text-gray-500">Joined Date</p>
                        <p class="text-lg font-semibold text-gray-800">{{ $student->created_at->format('d M Y') }}</p>
                    </div>
                </div>
            </div>

            {{-- 2. GRID INFO & STATS --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                {{-- Kiri: Biodata --}}
                <div class="lg:col-span-1 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 h-fit">
                    <h3 class="text-lg font-bold text-gray-800 mb-6 border-b pb-2">Personal Info</h3>
                    <div class="space-y-4">
                        <div><p class="text-xs text-gray-400 uppercase tracking-wide font-semibold">Full Name</p><p class="text-sm font-medium text-gray-800">{{ $student->name }}</p></div>
                        <div><p class="text-xs text-gray-400 uppercase tracking-wide font-semibold">Gender</p><p class="text-sm font-medium text-gray-800 capitalize">{{ $student->gender }}</p></div>
                        <div><p class="text-xs text-gray-400 uppercase tracking-wide font-semibold">Phone</p><p class="text-sm font-medium text-gray-800">{{ $student->phone ?? '-' }}</p></div>
                        <div><p class="text-xs text-gray-400 uppercase tracking-wide font-semibold">Address</p><p class="text-sm font-medium text-gray-800">{{ $student->address ?? '-' }}</p></div>
                    </div>
                </div>

                {{-- Kanan: Statistik Absensi --}}
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-800">Attendance Overview</h3>
                        <div class="text-sm text-gray-500 bg-gray-50 px-3 py-1 rounded-lg">Total Sessions: <strong>{{ $totalDays }}</strong></div>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
                        <div class="p-4 rounded-xl bg-blue-50 border border-blue-100 text-center"><p class="text-xs text-blue-600 font-bold uppercase mb-1">Present</p><p class="text-2xl font-bold text-blue-700">{{ $summary['present'] }}</p></div>
                        <div class="p-4 rounded-xl bg-yellow-50 border border-yellow-100 text-center"><p class="text-xs text-yellow-600 font-bold uppercase mb-1">Late</p><p class="text-2xl font-bold text-yellow-700">{{ $summary['late'] }}</p></div>
                        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-100 text-center"><p class="text-xs text-emerald-600 font-bold uppercase mb-1">Permit</p><p class="text-2xl font-bold text-emerald-700">{{ $summary['permission'] }}</p></div>
                        <div class="p-4 rounded-xl bg-purple-50 border border-purple-100 text-center"><p class="text-xs text-purple-600 font-bold uppercase mb-1">Sick</p><p class="text-2xl font-bold text-purple-700">{{ $summary['sick'] }}</p></div>
                        <div class="p-4 rounded-xl bg-red-50 border border-red-100 text-center"><p class="text-xs text-red-600 font-bold uppercase mb-1">Absent</p><p class="text-2xl font-bold text-red-700">{{ $summary['absent'] }}</p></div>
                    </div>
                    <div class="flex-1 flex items-center justify-center py-4">
                        @php
                            $total = $totalDays > 0 ? $totalDays : 1;
                            $pPresent = ($summary['present'] / $total) * 100;
                            $pLate = ($summary['late'] / $total) * 100;
                            $pPermission = ($summary['permission'] / $total) * 100;
                            $pSick = ($summary['sick'] / $total) * 100;
                            $stop1 = $pPresent; $stop2 = $stop1 + $pLate; $stop3 = $stop2 + $pPermission; $stop4 = $stop3 + $pSick;
                            $attendanceRate = $totalDays > 0 ? round((($summary['present'] + $summary['late']) / $totalDays) * 100) : 0;
                            $chartAttribute = 'style="background: conic-gradient(#2563eb 0% ' . $stop1 . '%, #eab308 ' . $stop1 . '% ' . $stop2 . '%, #10b981 ' . $stop2 . '% ' . $stop3 . '%, #9333ea ' . $stop3 . '% ' . $stop4 . '%, #dc2626 ' . $stop4 . '% 100%);"';
                        @endphp
                        <div class="flex flex-col items-center">
                            <div class="relative w-48 h-48 rounded-full shadow-inner" {!! $chartAttribute !!}>
                                <div class="absolute inset-0 m-5 bg-white rounded-full flex flex-col items-center justify-center shadow-sm">
                                    <span class="text-gray-400 text-xs font-semibold uppercase">Rate</span>
                                    <span class="text-4xl font-extrabold text-gray-800">{{ $attendanceRate }}%</span>
                                </div>
                            </div>
                            <div class="flex flex-wrap justify-center gap-x-4 gap-y-2 mt-6 text-xs text-gray-500 uppercase font-bold">
                                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span> Present</span>
                                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-yellow-500"></span> Late</span>
                                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Permit</span>
                                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-purple-600"></span> Sick</span>
                                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-red-600"></span> Absent</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. TIMELINE ABSENSI --}}
            <div class="mb-10">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-800">Attendance History</h3>
                    <span class="text-xs text-gray-400">&larr; Scroll for history &rarr;</span>
                </div>
                <div id="attendance-timeline" class="flex overflow-x-auto gap-4 pb-4 custom-scrollbar scroll-smooth" style="scrollbar-width: thin;">
                    @forelse ($attendance as $record)
                        @php
                            $theme = match($record->status) {
                                'present'    => ['bg' => 'bg-blue-600', 'text' => 'text-white', 'border' => 'border-blue-200'],
                                'late'       => ['bg' => 'bg-yellow-500', 'text' => 'text-white', 'border' => 'border-yellow-200'],
                                'permission' => ['bg' => 'bg-emerald-600', 'text' => 'text-white', 'border' => 'border-emerald-200'],
                                'sick'       => ['bg' => 'bg-purple-600', 'text' => 'text-white', 'border' => 'border-purple-200'],
                                'absent'     => ['bg' => 'bg-red-600', 'text' => 'text-white', 'border' => 'border-red-200'],
                                default      => ['bg' => 'bg-gray-400', 'text' => 'text-white', 'border' => 'border-gray-200'],
                            };
                        @endphp
                        <div class="min-w-[140px] bg-white border {{ $theme['border'] }} rounded-xl p-4 flex flex-col items-center justify-center shadow-sm hover:shadow-md transition-shadow flex-shrink-0">
                            <span class="text-xs text-gray-400 font-semibold uppercase mb-1">{{ \Carbon\Carbon::parse($record->session_date)->format('D, d M') }}</span>
                            <span class="text-lg font-bold {{ str_replace('bg-', 'text-', str_replace('600', '700', $theme['bg'])) }} mb-2">{{ ucfirst($record->status) }}</span>
                            <div class="w-8 h-8 rounded-full {{ $theme['bg'] }} flex items-center justify-center {{ $theme['text'] }}"><span class="font-bold text-xs">{{ substr(strtoupper($record->status), 0, 1) }}</span></div>
                        </div>
                    @empty
                        <div class="w-full text-center py-8 text-gray-400 bg-gray-50 rounded-xl border border-dashed border-gray-200">No attendance history yet.</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- INCLUDE MODAL EDIT (Partial) --}}
        @if(!$isTrashed)
            @include('admin.student.partials.edit-modal', ['showClassAssignment' => true])

            {{-- 5. HIDDEN FORM DELETE (Soft Delete) --}}
            <form id="delete-student-form" action="{{ route('admin.student.delete', $student->id) }}" method="POST" style="display: none;">
                @csrf 
                @method('DELETE')
            </form>
        @endif

    </div>
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const container = document.getElementById('attendance-timeline');
            if(container) {
                container.scrollLeft = container.scrollWidth; 
            }
        });
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Fungsi Konfirmasi untuk Restore & Force Delete
        function confirmAction(e, type) {
            e.preventDefault();
            const form = e.target;
            
            const config = type === 'restore' 
                ? {
                    title: 'Restore Student?',
                    text: "This student will be moved back to the active list.",
                    icon: 'question',
                    confirmButtonColor: '#10B981', // Green
                    confirmButtonText: 'Yes, Restore!'
                  }
                : {
                    title: 'Delete Permanently?',
                    text: "WARNING: This action cannot be undone. All data related to this student will be lost forever.",
                    icon: 'warning',
                    confirmButtonColor: '#EF4444', // Red
                    confirmButtonText: 'Yes, Delete Permanently!'
                  };

            Swal.fire({
                title: config.title,
                text: config.text,
                icon: config.icon,
                showCancelButton: true,
                confirmButtonColor: config.confirmButtonColor,
                cancelButtonColor: '#6B7280',
                confirmButtonText: config.confirmButtonText
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
            return false;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const successMessage = "{{ session('success') }}";
            const errorMessage = "{{ session('error') }}";

            if (successMessage) {
                Swal.fire({icon: 'success', title: 'Success!', text: successMessage, timer: 3000, showConfirmButton: false});
            }
            if (errorMessage) {
                Swal.fire({icon: 'error', title: 'Error!', text: errorMessage});
            }
        });
    </script>
</x-app-layout>