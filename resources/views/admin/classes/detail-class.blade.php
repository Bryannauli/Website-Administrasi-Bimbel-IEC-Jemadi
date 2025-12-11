<x-app-layout>
    <x-slot name="header"></x-slot>

    {{-- x-data UTAMA UNTUK MODAL EDIT --}}
    <div class="py-6" x-data="{ 
        showAddModal: false, 
        showEditModal: false,
        
        editForm: {
            id: null,
            category: '',
            name: '',
            classroom: '',
            start_month: '',
            end_month: '',
            academic_year: '',
            form_teacher_id: '',  
            local_teacher_id: '', 
            days: [],
            time_start: '',
            time_end: '',
            status: 'active'
        },

        updateUrl: '{{ route('admin.classes.update', ':id') }}',
        
        getUpdateUrl() {
            return this.editForm.id ? this.updateUrl.replace(':id', this.editForm.id) : '#';
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
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- 1. BREADCRUMB (CONSISTENT STYLE) --}}
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
                    Classes Management
                </h1>
            </div>

            {{-- 2. TABLE SECTION --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

                {{-- Header Actions: Search & Filter --}}
                <div class="p-4 sm:p-6 border-b border-gray-200 flex flex-col gap-4">
                    
                    {{-- SEARCH BAR --}}
                    <div class="w-full">
                        <form action="{{ route('admin.classes.index') }}" method="GET" class="relative w-full">
                            @if(request('academic_year')) <input type="hidden" name="academic_year" value="{{ request('academic_year') }}"> @endif
                            @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
                            @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
                            @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                            
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Search class name or classroom..." 
                                   class="w-full h-11 pl-12 pr-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm shadow-sm transition-all">

                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </form>
                    </div>
                    
                    {{-- FILTERS & BUTTONS --}}
                    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">

                        <form action="{{ route('admin.classes.index') }}" method="GET" class="flex flex-wrap items-center gap-2 w-full lg:w-auto">
                            @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                            
                            {{-- Academic Year --}}
                            <div class="relative flex-grow sm:flex-grow-0">
                                <select name="academic_year" onchange="this.form.submit()" 
                                        class="appearance-none h-10 w-full sm:w-auto px-3 py-2 pr-8 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 bg-gray-50 focus:ring-2 focus:ring-blue-500 cursor-pointer">
                                    <option value="">All Years</option>
                                    @foreach($years as $year)
                                        <option value="{{ $year }}" {{ request('academic_year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Category --}}
                            <div class="relative flex-grow sm:flex-grow-0">
                                <select name="category" onchange="this.form.submit()" 
                                        class="appearance-none h-10 w-full sm:w-auto px-3 py-2 pr-8 border border-gray-300 rounded-lg text-sm text-gray-700 bg-white focus:ring-2 focus:ring-blue-500 cursor-pointer">
                                    <option value="">Category</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ ucwords(str_replace('_', ' ', $cat)) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            {{-- Status --}}
                            <div class="relative flex-grow sm:flex-grow-0">
                                <select name="status" onchange="this.form.submit()" 
                                        class="appearance-none h-10 w-full sm:w-auto px-3 py-2 pr-8 border border-gray-300 rounded-lg text-sm text-gray-700 bg-white focus:ring-2 focus:ring-blue-500 cursor-pointer">
                                    <option value="">Status</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>

                            {{-- Sort --}}
                            <div class="relative flex-grow sm:flex-grow-0">
                                <select name="sort" onchange="this.form.submit()" 
                                        class="appearance-none h-10 w-full sm:w-auto px-3 py-2 pr-8 border border-gray-300 rounded-lg text-sm text-gray-700 bg-white focus:ring-2 focus:ring-blue-500 cursor-pointer">
                                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
                                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>A-Z</option>
                                    <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Z-A</option>
                                </select>
                            </div>

                            {{-- Reset Button --}}
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
                        <thead class="bg-gray-50 text-xs text-gray-500 font-bold uppercase border-b border-gray-200">
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
                        <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                            @forelse($classes as $index => $class)
                                <tr class="hover:bg-gray-50 transition-colors group">
                                    <td class="px-6 py-4 text-center text-gray-500 font-medium">{{ $classes->firstItem() + $index }}</td>
                                    
                                    <td class="px-6 py-4 capitalize text-gray-600 font-semibold">
                                        <span class="bg-blue-50 text-blue-700 px-2 py-1 rounded text-xs border border-blue-100">{{ str_replace('_', ' ', $class->category ?? '-') }}</span>
                                    </td>
                                    
                                    <td class="px-6 py-4 font-bold text-gray-900">{{ $class->name }}</td>
                                    
                                    <td class="px-6 py-4 text-gray-600">{{ $class->classroom }}</td>
                                    
                                    {{-- Schedule --}}
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col gap-1">
                                            <span class="font-medium text-gray-800 text-xs">
                                                @if($class->schedules->isNotEmpty())
                                                    {{ $class->schedules->pluck('day_of_week')->implode(', ') }}
                                                @else
                                                    <span class="text-gray-400 italic">-</span>
                                                @endif
                                            </span>
                                            <span class="text-[10px] text-gray-500 font-mono">
                                                {{ \Carbon\Carbon::parse($class->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($class->end_time)->format('H:i') }}
                                            </span>
                                        </div>
                                    </td>

                                    {{-- Teacher --}}
                                    <td class="px-6 py-4 text-xs">
                                        @if($class->formTeacher)
                                            <div class="text-gray-800 font-semibold mb-0.5">{{ $class->formTeacher->name }} <span class="text-gray-400 font-normal">(Form)</span></div>
                                        @endif
                                        @if($class->localTeacher)
                                            <div class="text-gray-600">{{ $class->localTeacher->name }} <span class="text-gray-400">(Local)</span></div>
                                        @endif
                                        @if(!$class->formTeacher && !$class->localTeacher)
                                            <span class="text-gray-400 italic">Unassigned</span>
                                        @endif
                                    </td>

                                    {{-- Status (Clean Badge) --}}
                                    <td class="px-6 py-4 text-center">
                                        @if($class->is_active)
                                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-lg text-xs font-bold">Active</span>
                                        @else
                                            <span class="px-3 py-1 bg-gray-100 text-gray-500 rounded-lg text-xs font-bold">Inactive</span>
                                        @endif
                                    </td>

                                    {{-- Action Buttons --}}
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center gap-3">
                                            <a href="{{ route('admin.classes.detailclass', $class->id) }}" 
                                                class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="View Details">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                            </a>
                                            
                                            <button type="button" @click='openEditModal(@json($class))' 
                                                    class="p-1.5 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors" title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                            </button>
                                            
                                            {{-- Delete Button (Placeholder Logic) --}}
                                            <button class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
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
                    @if ($classes->onFirstPage())
                        <button class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-400 bg-white cursor-not-allowed" disabled>Previous</button>
                    @else
                        <a href="{{ $classes->previousPageUrl() }}" class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-600 bg-white hover:bg-gray-50 hover:text-gray-800 transition-colors">Previous</a>
                    @endif
                    <span class="text-sm text-gray-500 font-medium">Page {{ $classes->currentPage() }} of {{ $classes->lastPage() }}</span>
                    @if ($classes->hasMorePages())
                        <a href="{{ $classes->nextPageUrl() }}" class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-600 bg-white hover:bg-gray-50 hover:text-gray-800 transition-colors">Next</a>
                    @else
                        <button class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-400 bg-white cursor-not-allowed" disabled>Next</button>
                    @endif
                </div>
            </div>

            {{-- 3. MODALS (Add & Edit - Disimpan di file yang sama agar ringkas) --}}
            {{-- MODAL ADD --}}
            <div x-show="showAddModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="showAddModal = false"></div>
                    <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl w-full border border-gray-100">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                            <h3 class="text-lg font-bold text-gray-900">Add New Class</h3>
                            <button @click="showAddModal = false" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                        </div>
                        <div class="p-6">
                            <form action="{{ route('admin.classes.store') }}" method="POST">
                                @csrf
                                {{-- Gunakan Grid Layout yang Rapi --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
                                    {{-- Kolom Kiri --}}
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-1">Class Name <span class="text-red-500">*</span></label>
                                            <input type="text" name="name" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required placeholder="e.g. Level 1A">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-1">Category <span class="text-red-500">*</span></label>
                                            <select name="category" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                                <option value="">Select Category</option>
                                                @foreach($categories as $cat) <option value="{{ $cat }}">{{ ucwords(str_replace('_', ' ', $cat)) }}</option> @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-1">Academic Year</label>
                                            <select name="academic_year" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                <option value="2025">2025</option>
                                                <option value="2026">2026</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    {{-- Kolom Kanan --}}
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-1">Classroom <span class="text-red-500">*</span></label>
                                            <input type="text" name="classroom" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required placeholder="e.g. Room 101">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-1">Period (Start - End)</label>
                                            <div class="flex gap-2">
                                                <select name="start_month" class="w-1/2 border-gray-300 rounded-lg shadow-sm text-sm">@foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $m)<option value="{{$m}}">{{$m}}</option>@endforeach</select>
                                                <select name="end_month" class="w-1/2 border-gray-300 rounded-lg shadow-sm text-sm">@foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $m)<option value="{{$m}}">{{$m}}</option>@endforeach</select>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Full Width: Teachers --}}
                                    <div class="md:col-span-2 grid grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg border border-gray-100">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Form Teacher</label>
                                            <select name="form_teacher_id" class="w-full border-gray-300 rounded-lg shadow-sm text-sm">
                                                <option value="">Select Teacher (Optional)</option>
                                                @foreach($teachers as $teacher) <option value="{{ $teacher->id }}">{{ $teacher->name }}</option> @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Local Teacher</label>
                                            <select name="local_teacher_id" class="w-full border-gray-300 rounded-lg shadow-sm text-sm">
                                                <option value="">Select Teacher (Optional)</option>
                                                @foreach($teachers as $teacher) <option value="{{ $teacher->id }}">{{ $teacher->name }}</option> @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Full Width: Schedule --}}
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Schedule Days</label>
                                        <div class="flex flex-wrap gap-3">
                                            @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                                                <label class="inline-flex items-center cursor-pointer bg-white border border-gray-200 px-3 py-1.5 rounded-lg hover:bg-blue-50 hover:border-blue-200 transition">
                                                    <input type="checkbox" name="days[]" value="{{ $day }}" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                                    <span class="ml-2 text-gray-700 text-sm font-medium">{{ $day }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                    
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-bold text-gray-700 mb-1">Class Time</label>
                                        <div class="flex items-center gap-2">
                                            <input type="time" name="start_time" class="border-gray-300 rounded-lg shadow-sm" required>
                                            <span class="text-gray-400 font-bold">-</span>
                                            <input type="time" name="end_time" class="border-gray-300 rounded-lg shadow-sm" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                                    <button type="button" @click="showAddModal = false" class="px-5 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition">Cancel</button>
                                    <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 shadow-sm transition">Create Class</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- MODAL EDIT (Layout Sama dengan Add) --}}
            <div x-show="showEditModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="showEditModal = false"></div>
                    <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl w-full border border-gray-100">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                            <h3 class="text-lg font-bold text-gray-900">Edit Class</h3>
                            <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                        </div>
                        <div class="p-6">
                            <form :action="getUpdateUrl()" method="POST"> 
                                @csrf
                                @method('PUT')
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
                                    {{-- Sama dengan ADD, hanya ada x-model --}}
                                    <div class="space-y-4">
                                        <div><label class="block text-sm font-bold text-gray-700 mb-1">Class Name</label><input type="text" name="name" x-model="editForm.name" class="w-full border-gray-300 rounded-lg shadow-sm"></div>
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-1">Category</label>
                                            <select name="category" x-model="editForm.category" class="w-full border-gray-300 rounded-lg shadow-sm">
                                                @foreach($categories as $cat) <option value="{{ $cat }}">{{ ucwords(str_replace('_', ' ', $cat)) }}</option> @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-1">Academic Year</label>
                                            <select name="academic_year" x-model="editForm.academic_year" class="w-full border-gray-300 rounded-lg shadow-sm">
                                                <option value="2025">2025</option><option value="2026">2026</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="space-y-4">
                                        <div><label class="block text-sm font-bold text-gray-700 mb-1">Classroom</label><input type="text" name="classroom" x-model="editForm.classroom" class="w-full border-gray-300 rounded-lg shadow-sm"></div>
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-1">Period</label>
                                            <div class="flex gap-2">
                                                <select name="start_month" x-model="editForm.start_month" class="w-1/2 border-gray-300 rounded-lg text-sm">@foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $m)<option value="{{$m}}">{{$m}}</option>@endforeach</select>
                                                <select name="end_month" x-model="editForm.end_month" class="w-1/2 border-gray-300 rounded-lg text-sm">@foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $m)<option value="{{$m}}">{{$m}}</option>@endforeach</select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="md:col-span-2 grid grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg border border-gray-100">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Form Teacher</label>
                                            <select name="form_teacher_id" x-model="editForm.form_teacher_id" class="w-full border-gray-300 rounded-lg shadow-sm text-sm">
                                                <option value="">Select (Optional)</option>
                                                @foreach($teachers as $teacher) <option value="{{ $teacher->id }}">{{ $teacher->name }}</option> @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Local Teacher</label>
                                            <select name="local_teacher_id" x-model="editForm.local_teacher_id" class="w-full border-gray-300 rounded-lg shadow-sm text-sm">
                                                <option value="">Select (Optional)</option>
                                                @foreach($teachers as $teacher) <option value="{{ $teacher->id }}">{{ $teacher->name }}</option> @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Schedule Days</label>
                                        <div class="flex flex-wrap gap-3">
                                            @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                                                <label class="inline-flex items-center cursor-pointer bg-white border border-gray-200 px-3 py-1.5 rounded-lg hover:bg-blue-50 hover:border-blue-200 transition">
                                                    <input type="checkbox" name="days[]" value="{{ $day }}" x-model="editForm.days" class="w-4 h-4 text-blue-600 rounded border-gray-300">
                                                    <span class="ml-2 text-gray-700 text-sm font-medium">{{ $day }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="md:col-span-2 flex justify-between items-end">
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-1">Class Time</label>
                                            <div class="flex items-center gap-2">
                                                <input type="time" name="start_time" x-model="editForm.time_start" class="border-gray-300 rounded-lg shadow-sm">
                                                <span class="text-gray-400 font-bold">-</span>
                                                <input type="time" name="end_time" x-model="editForm.time_end" class="border-gray-300 rounded-lg shadow-sm">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Status</label>
                                            <div class="flex gap-4">
                                                <label class="inline-flex items-center"><input type="radio" name="status" value="active" x-model="editForm.status" class="text-green-600"><span class="ml-2 text-sm">Active</span></label>
                                                <label class="inline-flex items-center"><input type="radio" name="status" value="inactive" x-model="editForm.status" class="text-gray-600"><span class="ml-2 text-sm">Inactive</span></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                                    <button type="button" @click="showEditModal = false" class="px-5 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition">Cancel</button>
                                    <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 shadow-sm transition">Update Class</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>