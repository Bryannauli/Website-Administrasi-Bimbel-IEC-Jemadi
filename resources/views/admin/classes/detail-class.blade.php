<x-app-layout>
    <x-slot name="header"></x-slot>

    {{-- WRAPPER UTAMA DENGAN ALPINE JS --}}
    <div class="bg-[#EEF2FF] min-h-screen font-sans" x-data="{ 
        // 1. STATE MODAL
        showEditModal: {{ $errors->any() || session('edit_failed') ? 'true' : 'false' }},
        showAddStudentModal: {{ request('search_student') ? 'true' : 'false' }},
        showHistoryModal: false,
        showStudentStatsModal: false,
        showAssignTeacherModal: false,
        assignTeacherRole: '',

        // 2. STATE FORM DATA
        editForm: {
            name: '{{ addslashes($class->name) }}',
            category: '{{ $class->category }}',
            academic_year: '{{ $class->academic_year }}',
            classroom: '{{ addslashes($class->classroom) }}',
            start_month: '{{ $class->start_month }}',
            end_month: '{{ $class->end_month }}',
            form_teacher_id: '{{ $class->form_teacher_id ?? '' }}',
            local_teacher_id: '{{ $class->local_teacher_id ?? '' }}',
            time_start: '{{ \Carbon\Carbon::parse($class->start_time)->format('H:i') }}',
            time_end: '{{ \Carbon\Carbon::parse($class->end_time)->format('H:i') }}',
            days: [
                @foreach($class->schedules as $schedule)
                    '{{ $schedule->day_of_week }}',
                @endforeach
            ],
            teacher_types: {
                @foreach($class->schedules as $schedule)
                    '{{ $schedule->day_of_week }}': '{{ $schedule->teacher_type }}',
                @endforeach
            },
            status: '{{ $class->is_active ? 'active' : 'inactive' }}',
        },

        // 3. URL & ID DEFINITION
        classId: '{{ $class->id }}', 
        deleteUrl: '{{ route('admin.classes.delete', $class->id) }}',
        updateBaseUrl: '{{ route('admin.classes.update', ['id' => 'PLACEHOLDER']) }}'.replace('/PLACEHOLDER', ''),
        
        getUpdateUrl() {
            const oldId = '{{ old('id') ?? '' }}';
            const finalId = oldId || this.classId;
            return `${this.updateBaseUrl}/${finalId}`;
        },

        closeModal(modalVar) {
            if ({{ $errors->any() || session('edit_failed') ? 'true' : 'false' }}) {
                window.location.href = window.location.href; 
            } else {
                this[modalVar] = false;
            }
        },

        openAssignTeacherModal(role) {
            this.assignTeacherRole = role;
            this.showAssignTeacherModal = true;
        },
        
        // 4. FUNCTION CONFIRM DELETE CLASS
        confirmDelete() {
            Swal.fire({
                title: 'Delete Class?',
                text: 'This class will be moved to trash (Soft Delete).',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, Delete'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = this.deleteUrl;
                }
            });
        },
        
        // 5. FUNCTION CONFIRM REMOVE STUDENT (UNASSIGN)
        confirmRemove(studentName, formId) {
            Swal.fire({
                title: 'Remove Student?',
                text: `Remove ${studentName} from this class? They will become Unassigned (No Class).`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, Remove'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        },

        // 6. FUNCTION CONFIRM TOGGLE STATUS
        confirmToggleStatus(studentId, isActive) {
            const action = isActive ? 'DEACTIVATE' : 'ACTIVATE';
            const statusText = isActive ? 'inactive' : 'active';
            const iconColor = isActive ? '#EF4444' : '#10B981';

            Swal.fire({
                title: `${action} Student?`,
                text: `Change status to ${statusText}? Inactive students remain in history.`,
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

        confirmUnassignTeacher(teacherName, type) {
            Swal.fire({
                title: 'Unassign Teacher?',
                text: `Are you sure you want to remove ${teacherName} as the ${type === 'form' ? 'Form' : 'Local'} Teacher?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, Remove'
            }).then((result) => {
                if (result.isConfirmed) {
                    const url = `{{ route('admin.classes.unassignTeacher', ['class' => ':classId', 'type' => ':type']) }}`
                                .replace(':classId', this.classId)
                                .replace(':type', type);
                    
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;
                    
                    const csrfToken = document.querySelector('meta[name=csrf-token]').content;
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken;
                    
                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'PATCH';
                    
                    form.appendChild(csrfInput);
                    form.appendChild(methodInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    }">

        {{-- CONTAINER KONTEN --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

            {{-- BREADCRUMB --}}
            <nav class="flex mb-5" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-blue-600">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            <a href="{{ route('admin.classes.index') }}" class="ml-1 text-sm font-medium text-gray-500 hover:text-blue-600 md:ml-2">Classes</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            <span class="ml-1 text-sm font-medium text-gray-900 md:ml-2 truncate max-w-xs">{{ $class->name }}</span>
                        </div>
                    </li>
                </ol>
            </nav>

            {{-- HEADER TITLE & BUTTON --}}
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent inline-block">
                    Class: {{ $class->name ?? 'Detail' }}
                </h2>
                
                {{-- TRIGGER EDIT MODAL --}}
                <button @click="showEditModal = true" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium transition-colors shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                    Edit Class
                </button>
            </div>
        
            <div class="space-y-8">

                {{-- 1. INFO KELAS (TOP CARD) --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 relative overflow-hidden z-0">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-bl-full -mr-8 -mt-8"></div>
                    <div class="flex flex-col md:flex-row items-center justify-between relative gap-6">
                        <div class="flex items-center gap-5">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($class->name) }}&background=2563EB&color=fff&size=128&bold=true&length=2"
                                alt="{{ $class->name }}" 
                                class="w-16 h-16 md:w-20 md:h-20 rounded-2xl shadow-md border-4 border-white bg-blue-600">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">{{ $class->name }}</h1>
                                <div class="flex flex-col gap-1 mt-1 mb-2">
                                    <div class="flex items-center gap-2 text-sm text-gray-600">
                                        <span class="font-semibold text-gray-800">
                                            {{ \Carbon\Carbon::parse($class->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($class->end_time)->format('H:i') }}
                                        </span>
                                        <span class="text-gray-300">|</span>
                                        <div class="flex flex-wrap gap-1">
                                            @forelse($class->schedules as $schedule)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold border 
                                                    {{ $schedule->teacher_type == 'form' ? 'bg-blue-100 text-blue-700 border-blue-200' : 'bg-purple-100 text-purple-700 border-purple-200' }}">
                                                    {{ substr($schedule->day_of_week, 0, 3) }} 
                                                    <span class="mx-0.5 opacity-50">|</span> 
                                                    {{ $schedule->teacher_type == 'form' ? 'F' : 'L' }}
                                                </span>
                                            @empty
                                                <span class="text-gray-400 italic text-sm">No Schedule</span>
                                            @endforelse
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 text-sm text-gray-500">
                                        <span>{{ $class->classroom ?? 'No Classroom' }}</span>
                                    </div>
                                </div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="px-2.5 py-0.5 {{ $class->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600' }} rounded text-xs font-bold uppercase tracking-wider">
                                        {{ $class->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                    <span class="px-2.5 py-0.5 bg-blue-50 text-blue-700 rounded text-xs font-bold uppercase tracking-wider border border-blue-100">
                                        {{ ucwords(str_replace('_', ' ', $class->category)) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="text-center md:text-right hidden md:block">
                            <p class="text-xs text-gray-400 uppercase font-bold tracking-wide mb-1">Academic Year</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $class->academic_year }}</p>
                            <p class="text-sm text-gray-500 font-medium bg-gray-50 px-2 py-1 rounded inline-block mt-1">
                                {{ $class->start_month }} - {{ $class->end_month }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- 2. ROW 1: TEACHER & TEACHER ATTENDANCE --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    {{-- A. LIST TEACHER --}}
                    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                                Teachers Assigned
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Form Teacher --}}
                            @if($class->formTeacher)
                                <div class="p-3 rounded-xl border border-blue-100 bg-blue-50/20 flex items-center justify-between group">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-sm shadow-sm">
                                            {{ substr($class->formTeacher->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-gray-800 text-sm">{{ $class->formTeacher->name }}</h4>
                                            <p class="text-[10px] text-blue-600 font-bold uppercase tracking-wider">Form Teacher</p>
                                        </div>
                                    </div>
                                    {{-- QUICK ACTIONS --}}
                                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <a href="{{ route('admin.teacher.detail', ['id' => $class->localTeacher->id, 'ref' => 'class', 'class_id' => $class->id]) }}" 
                                        class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-white rounded shadow-sm" title="View Profile">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </a>
                                        <button @click="confirmUnassignTeacher('{{ addslashes($class->formTeacher->name) }}', 'form')" 
                                                class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-white rounded shadow-sm" title="Unassign">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            @else
                                <button @click="openAssignTeacherModal('form')" class="w-full p-3 rounded-xl border border-dashed border-gray-300 bg-gray-50 flex items-center justify-center gap-2 text-gray-400 hover:bg-gray-100 hover:border-gray-400 transition">
                                    <span class="text-xs font-medium italic">+ Assign Form Teacher</span>
                                </button>
                            @endif

                            {{-- Local Teacher --}}
                            @if($class->localTeacher)
                                <div class="p-3 rounded-xl border border-purple-100 bg-purple-50/20 flex items-center justify-between group">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 font-bold text-sm shadow-sm">
                                            {{ substr($class->localTeacher->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-gray-800 text-sm">{{ $class->localTeacher->name }}</h4>
                                            <p class="text-[10px] text-purple-600 font-bold uppercase tracking-wider">Local Teacher</p>
                                        </div>
                                    </div>
                                    {{-- QUICK ACTIONS --}}
                                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <a href="{{ route('admin.teacher.detail', $class->localTeacher->id) }}" 
                                        class="p-1.5 text-gray-400 hover:text-purple-600 hover:bg-white rounded shadow-sm" title="View Profile">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </a>
                                        <button @click="confirmUnassignTeacher('{{ addslashes($class->localTeacher->name) }}', 'local')" 
                                                class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-white rounded shadow-sm" title="Unassign">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            @else
                                <button @click="openAssignTeacherModal('local')" class="w-full p-3 rounded-xl border border-dashed border-gray-300 bg-gray-50 flex items-center justify-center gap-2 text-gray-400 hover:bg-gray-100 hover:border-gray-400 transition">
                                    <span class="text-xs font-medium italic">+ Assign Local Teacher</span>
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- B. TEACHER ATTENDANCE --}}
                    <div class="lg:col-span-1 bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl shadow-lg shadow-blue-200 p-6 text-white flex flex-col justify-between relative overflow-hidden">
                        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white opacity-10 rounded-full blur-xl"></div>
                        <div>
                            <div class="flex justify-between items-start">
                                <h4 class="text-blue-100 text-xs font-bold uppercase tracking-widest mb-2">Latest Activity</h4>
                                @if($lastSession)
                                    <span class="bg-blue-800 bg-opacity-50 text-xs px-2 py-1 rounded border border-blue-400 border-opacity-30">
                                        {{ \Carbon\Carbon::parse($lastSession->date)->format('d M') }}
                                    </span>
                                @endif
                            </div>

                            @php $lastTeacher = $lastSession ? $lastSession->teacher : null; @endphp

                            @if($lastTeacher)
                                <h3 class="text-lg font-bold truncate" title="{{ $lastTeacher->name }}">{{ $lastTeacher->name }}</h3>
                                <div class="mt-3 bg-blue-800 bg-opacity-40 p-3 rounded-lg border border-blue-500 border-opacity-30">
                                    <p class="text-blue-50 text-xs italic line-clamp-3">"{{ $lastSession->comment ?? 'No teaching notes provided.' }}"</p>
                                </div>
                            @else
                                <h3 class="text-xl font-bold">No Data Yet</h3>
                                <p class="text-blue-100 text-xs mt-2 opacity-80 leading-relaxed">Teaching logs will appear here once attendance is submitted.</p>
                            @endif
                        </div>

                        <button @click="showHistoryModal = true" 
                                class="mt-4 w-full py-2.5 bg-white text-blue-700 rounded-lg text-sm font-bold hover:bg-blue-50 transition shadow-sm flex items-center justify-center gap-2">
                            View Teaching Logs <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
                    </div>
                </div>

                {{-- 3. ROW 2: STUDENTS & STUDENT ATTENDANCE --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    {{-- C. LIST STUDENTS TABLE (FIXED: Added max-height & scroll) --}}
                    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm p-6 border border-gray-100 flex flex-col h-full">
                        <div class="flex justify-between items-center mb-5 shrink-0">
                            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                                Enrolled Students
                                <span class="bg-gray-100 text-gray-600 text-xs font-bold px-2 py-0.5 rounded-full ml-2">
                                    {{ $class->students_count ?? 0 }}
                                </span>
                            </h3>
                            
                            <button @click="showAddStudentModal = true" class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold py-2 px-3 rounded-lg transition shadow-sm shadow-green-200">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Add Student
                            </button>
                        </div>

                        {{-- CONTAINER SCROLLABLE BARU --}}
                        <div class="overflow-x-auto overflow-y-auto max-h-[500px] flex-1 custom-scrollbar border border-gray-50 rounded-lg">
                            <table class="w-full text-left border-collapse relative">
                                <thead class="bg-gray-50 text-gray-400 text-xs font-medium border-b border-gray-100 sticky top-0 z-10 shadow-sm">
                                    <tr>
                                        <th class="px-4 py-3 font-normal w-12 bg-gray-50">No</th>
                                        <th class="px-4 py-3 font-normal bg-gray-50">Student ID</th>
                                        <th class="px-4 py-3 font-normal bg-gray-50">Name</th>
                                        <th class="px-4 py-3 font-normal bg-gray-50">Status</th>
                                        <th class="px-4 py-3 font-normal text-center w-28 bg-gray-50">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-sm text-gray-800 bg-white">
                                    @forelse($class->students ?? [] as $index => $student)
                                    <tr class="transition group {{ $student->is_active ? 'hover:bg-gray-50' : 'bg-red-50 hover:bg-red-100' }}">
                                        
                                        <td class="px-4 py-3 text-gray-400 text-xs">{{ $index + 1 }}</td>
                                        <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $student->student_number }}</td>
                                        
                                        <td class="px-4 py-3 font-medium transition-colors {{ $student->is_active ? 'text-gray-900 group-hover:text-blue-600' : 'text-red-800 line-through decoration-red-500' }}">
                                            {{ $student->name }}
                                        </td>
                                        
                                        <td class="px-4 py-3">
                                            @if($student->is_active)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-700 border border-green-200">Active</span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700 border border-red-200">Inactive</span>
                                            @endif
                                        </td>

                                        <td class="px-4 py-3 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                
                                                <a href="{{ route('admin.student.detail', ['id' => $student->id, 'ref' => 'class', 'class_id' => $class->id]) }}" 
                                                class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded" title="View Profile">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                </a>

                                                <button type="button" 
                                                    @click="confirmToggleStatus({{ $student->id }}, {{ $student->is_active ? 'true' : 'false' }})"
                                                    class="p-1.5 rounded transition-colors {{ $student->is_active ? 'text-gray-400 hover:text-red-600 hover:bg-red-50' : 'text-gray-400 hover:text-green-600 hover:bg-green-50' }}"
                                                    title="{{ $student->is_active ? 'Deactivate (Quit)' : 'Activate' }}">

                                                    @if($student->is_active)
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                                                    @else
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                    @endif
                                                </button>

                                                <form id="remove-student-{{ $student->id }}" action="{{ route('admin.classes.unassignStudent', $student->id) }}" method="POST" style="display: none;">
                                                    @csrf @method('PATCH')
                                                </form>
                                                <button @click="confirmRemove('{{ addslashes($student->name) }}', 'remove-student-{{ $student->id }}')" 
                                                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded" title="Unassign (Wrong Class)">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-12 text-center text-gray-400 italic bg-gray-50 rounded-lg border border-dashed border-gray-200">
                                            No students enrolled in this class yet.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- D. STUDENT ATTENDANCE (FIXED: Sticky footer button) --}}
                    <div class="lg:col-span-1 bg-white rounded-2xl shadow-sm p-6 border border-gray-100 flex flex-col h-full">
                        <div class="flex justify-between items-start mb-4 shrink-0">
                            <div>
                                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Attendance</h4>
                                <h3 class="text-xl font-bold text-gray-800">Last Session</h3>
                                
                                @if($lastSession)
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        {{ \Carbon\Carbon::parse($lastSession->date)->format('d M Y') }}
                                    </p>
                                @else
                                    <p class="text-xs text-gray-400 mt-0.5">-</p>
                                @endif
                            </div>

                            @if($lastSession)
                                <div class="text-right">
                                    @php
                                        $pCount = $lastSession->records->whereIn('status', ['present', 'late'])->count();
                                        $tCount = $lastSession->records->count();
                                        $sPerc = $tCount > 0 ? round(($pCount / $tCount) * 100) : 0;
                                    @endphp
                                    <span class="text-2xl font-bold {{ $sPerc >= 80 ? 'text-green-600' : ($sPerc >= 50 ? 'text-yellow-600' : 'text-red-600') }}">{{ $sPerc }}%</span>
                                    <p class="text-[10px] text-gray-400">Present</p>
                                </div>
                            @endif
                        </div>

                        {{-- Bagian Tengah (List Absentees) --}}
                        <div class="flex-1 overflow-y-auto custom-scrollbar pr-1 mb-4 min-h-[150px]">
                            @if($lastSession && $lastSession->records->count() > 0)
                                @php $absentees = $lastSession->records->whereIn('status', ['absent', 'sick', 'permission']); @endphp
                                @if($absentees->count() > 0)
                                    <p class="text-xs font-bold text-red-500 mb-2 uppercase tracking-wide">Absentees List:</p>
                                    <ul class="space-y-2">
                                    @foreach($absentees as $record)
                                        <li class="flex items-center justify-between p-2 bg-gray-50 rounded-lg border border-gray-100">
                                            <span class="text-sm font-medium text-gray-700 truncate w-32" title="{{ $record->student->name ?? '-' }}">{{ $record->student->name ?? '-' }}</span>
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded uppercase {{ $record->status == 'absent' ? 'bg-red-100 text-red-700 border border-red-200' : '' }} {{ $record->status == 'sick' ? 'bg-purple-100 text-purple-700 border border-purple-200' : '' }} {{ $record->status == 'permission' ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : '' }}">{{ $record->status }}</span>
                                        </li>
                                    @endforeach
                                    </ul>
                                @else
                                    <div class="h-full flex flex-col items-center justify-center text-center py-6 bg-green-50 rounded-xl border border-dashed border-green-200"><div class="w-10 h-10 rounded-full bg-green-100 text-green-600 flex items-center justify-center mb-2"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div><p class="text-sm font-bold text-green-700">Perfect Attendance!</p><p class="text-xs text-green-600">All students present.</p></div>
                                @endif
                            @else
                                <div class="h-full flex flex-col items-center justify-center text-center p-4 bg-gray-50 rounded-xl border border-dashed border-gray-200"><p class="text-sm text-gray-500">No attendance data yet.</p></div>
                            @endif
                        </div>

                        {{-- Bagian Bawah (Tombol) - Sticky Footer --}}
                        <div class="mt-auto pt-4 border-t border-gray-50">
                            <button @click="showStudentStatsModal = true" class="w-full py-2.5 bg-green-600 text-white rounded-lg text-sm font-bold hover:bg-green-700 transition shadow-sm shadow-green-200 flex items-center justify-center gap-2">
                                View Full Report <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- 4. ROW 3: ACADEMIC ASSESSMENTS --}}
                <div class="mt-8">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        Academic Assessments
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        {{-- MID TERM CARD --}}
                        @php
                            $midSession = $class->assessmentSessions->where('type', 'mid')->first();
                            $midStatus = $midSession->status ?? 'draft'; 
                            
                            $midColor = match($midStatus) {
                                'submitted' => 'bg-blue-100 text-blue-700 border-blue-200',
                                'final'     => 'bg-purple-100 text-purple-700 border-purple-200',
                                default     => 'bg-gray-100 text-gray-600 border-gray-200',
                            };
                        @endphp
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 relative overflow-hidden group hover:border-blue-300 hover:shadow-md transition-all">
                            
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h4 class="text-lg font-bold text-gray-800">Mid Term</h4>
                                    <p class="text-xs text-gray-400 mt-1">
                                        {{ $midSession && $midSession->date ? \Carbon\Carbon::parse($midSession->date)->format('d M Y') : 'Date not set' }}
                                    </p>
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide border {{ $midColor }}">
                                    {{ ucfirst($midStatus) }}
                                </span>
                            </div>

                            <div class="flex items-center gap-3">
                                <a href="{{ route('admin.classes.assessment.detail', ['classId' => $class->id, 'type' => 'mid']) }}" 
                                class="flex-1 inline-flex justify-center items-center px-4 py-2.5 bg-blue-600 text-white text-sm font-bold rounded-lg hover:bg-blue-700 transition shadow-blue-200 gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    {{ $midStatus == 'draft' ? 'Input Grades' : 'View Grades' }}
                                </a>
                                
                                <button disabled class="p-2.5 text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed" title="Print Report Card">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                </button>
                            </div>
                        </div>

                        {{-- FINAL TERM CARD --}}
                        @php
                            $finalSession = $class->assessmentSessions->where('type', 'final')->first();
                            $finalStatus = $finalSession->status ?? 'draft'; 

                            $finalColor = match($finalStatus) {
                                'submitted' => 'bg-indigo-100 text-indigo-700 border-indigo-200',
                                'final'     => 'bg-purple-100 text-purple-700 border-purple-200',
                                default     => 'bg-gray-100 text-gray-600 border-gray-200',
                            };
                        @endphp
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 relative overflow-hidden group hover:border-indigo-300 hover:shadow-md transition-all">
                            
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h4 class="text-lg font-bold text-gray-800">Final Term</h4>
                                    <p class="text-xs text-gray-400 mt-1">
                                        {{ $finalSession && $finalSession->date ? \Carbon\Carbon::parse($finalSession->date)->format('d M Y') : 'Date not set' }}
                                    </p>
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide border {{ $finalColor }}">
                                    {{ ucfirst($finalStatus) }}
                                </span>
                            </div>

                            <div class="flex items-center gap-3">
                                <a href="{{ route('admin.classes.assessment.detail', ['classId' => $class->id, 'type' => 'final']) }}" 
                                class="flex-1 inline-flex justify-center items-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-lg hover:bg-indigo-700 transition shadow-indigo-200 gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    {{ $finalStatus == 'draft' ? 'Input Grades' : 'View Grades' }}
                                </a>
                                <button disabled class="p-2.5 text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed" title="Print Certificate">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> 

        {{-- INCLUDE MODALS (PARTIALS) --}}
        @include('admin.classes.partials.assign-teacher-modal')
        @include('admin.classes.partials.assign-student-modal', ['class' => $class, 'availableStudents' => $availableStudents])
        @include('admin.classes.partials.activity-history-modal', ['teachingLogs' => $teachingLogs])
        @include('admin.classes.partials.attendance-modal', ['studentStats' => $studentStats, 'teachingLogs' => $teachingLogs, 'attendanceMatrix' => $attendanceMatrix])
        @include('admin.classes.partials.edit-class-modal', ['teachers' => $teachers, 'categories' => $categories, 'years' => $years])

        {{-- FORM HIDDEN UNTUK TOGGLE STATUS --}}
        <form id="toggleStatusForm" method="POST" action="#" style="display: none;">
            @csrf @method('PATCH')
        </form>

    </div>

    {{-- SWEETALERT --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const successMessage = <?php echo json_encode(session('success')); ?>;
            const errorMessage = <?php echo json_encode(session('error')); ?>;

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