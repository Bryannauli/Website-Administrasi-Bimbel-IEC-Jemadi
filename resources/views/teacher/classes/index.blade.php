@extends('layouts.teacher')

@section('title', 'Classes')

@section('breadcrumb')
<nav class="flex items-center space-x-2 text-sm">
    <a href="{{ route('teacher.dashboard') }}" class="text-gray-600 hover:text-gray-900">Home</a>
    <i class="fas fa-chevron-right text-xs text-gray-400"></i>
    <span class="text-gray-900 font-medium">Classes</span>
</nav>
@endsection

@section('content')
<div class="max-w-7xl mx-auto">
    
    {{-- 1. HEADER & TITLE --}}
    <div class="mb-8 flex justify-between items-end">
        <div>
            <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent inline-block">
                My Classes
            </h1>
            <p class="text-gray-500 text-sm mt-1">View and manage attendance for your assigned classes.</p>
        </div>
        
        {{-- Export Button (Opsional) --}}
        <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition shadow-sm flex items-center text-sm font-medium">
            <i class="fas fa-download mr-2 text-gray-400"></i> Export
        </button>
    </div>

    {{-- 2. TABLE SECTION --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

        {{-- Header Actions (Filters & Search) --}}
        <div class="p-4 sm:p-6 border-b border-gray-200 flex flex-col gap-4">
            
            {{-- SEARCH BAR --}}
            <div class="w-full">
                <form action="{{ route('teacher.classes.index') }}" method="GET" class="relative w-full">
                    @foreach(['category', 'status'] as $key)
                        @if(request($key)) <input type="hidden" name="{{ $key }}" value="{{ request($key) }}"> @endif
                    @endforeach
                    
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Search class name or classroom..." 
                           class="w-full h-11 pl-12 pr-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm shadow-sm transition-all">

                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </form>
            </div>
            
            {{-- FILTERS --}}
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">

                <form action="{{ route('teacher.classes.index') }}" method="GET" class="flex flex-wrap items-center gap-2 w-full lg:w-auto">
                    @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                    
                    {{-- Category Filter --}}
                    <div class="relative flex-grow sm:flex-grow-0">
                        <select name="category" onchange="this.form.submit()" 
                                class="h-10 w-full sm:w-auto px-3 py-2 pr-8 border border-gray-300 rounded-lg text-sm text-gray-700 bg-white focus:ring-2 focus:ring-blue-500 cursor-pointer">
                            <option value="">All Categories</option>
                            <option value="pre_level" {{ request('category') == 'pre_level' ? 'selected' : '' }}>Pre Level</option>
                            <option value="level" {{ request('category') == 'level' ? 'selected' : '' }}>Level</option>
                            <option value="step" {{ request('category') == 'step' ? 'selected' : '' }}>Step</option>
                            <option value="private" {{ request('category') == 'private' ? 'selected' : '' }}>Private</option>
                        </select>
                    </div>
                    
                    {{-- Status Filter --}}
                    <div class="relative flex-grow sm:flex-grow-0">
                        <select name="status" onchange="this.form.submit()" 
                                class="h-10 w-full sm:w-auto px-3 py-2 pr-8 border border-gray-300 rounded-lg text-sm text-gray-700 bg-white focus:ring-2 focus:ring-blue-500 cursor-pointer">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    {{-- Reset Filter Button --}}
                    @if(request('category') || request('search') || request('status'))
                        <a href="{{ route('teacher.classes.index') }}" class="h-10 w-10 flex items-center justify-center bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 rounded-lg transition-colors flex-shrink-0" title="Reset Filters">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </form>

            </div>
        </div>

        {{-- 3. TABLE CONTENT --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-max">
                <thead class="bg-gray-50 text-xs text-gray-500 font-bold uppercase border-b border-gray-100 tracking-wider">
                    <tr>
                        <th class="px-6 py-4 w-16 whitespace-nowrap text-center">No</th>
                        <th class="px-6 py-4 whitespace-nowrap">Category</th> {{-- Ganti Class ID jadi Category --}}
                        <th class="px-6 py-4 whitespace-nowrap">Class Name</th>
                        <th class="px-6 py-4 whitespace-nowrap">Classroom</th> {{-- Ganti Room jadi Classroom --}}
                        <th class="px-6 py-4 whitespace-nowrap">Schedule</th>
                        <th class="px-6 py-4 whitespace-nowrap">Teacher</th> {{-- Tambah Kolom Teacher --}}
                        <th class="px-6 py-4 whitespace-nowrap text-center">Status</th>
                        <th class="px-6 py-4 whitespace-nowrap text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-sm text-gray-700">
                    @forelse($classes as $index => $class)
                        <tr class="hover:bg-gray-50 transition-colors group">
                            {{-- No --}}
                            <td class="px-6 py-5 text-center text-gray-500 font-medium">
                                {{ $classes->firstItem() + $index }}
                            </td>
                            
                            {{-- Category (Baru) --}}
                            <td class="px-6 py-5 text-gray-600">
                                <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-xs font-semibold uppercase tracking-wide">
                                    {{ str_replace('_', ' ', $class->category ?? '-') }}
                                </span>
                            </td>
                            
                            {{-- Class Name (Hanya Nama) --}}
                            <td class="px-6 py-5 font-bold text-gray-900 text-base">
                                {{ $class->name }}
                            </td>
                            
                            {{-- Classroom (Renamed) --}}
                            <td class="px-6 py-5 text-gray-500 font-medium whitespace-nowrap">
                                <i class="fas fa-door-open text-gray-300 mr-1.5"></i> {{ $class->classroom }}
                            </td>
                            
                            {{-- Schedule --}}
                            <td class="px-6 py-5">
                                <div class="flex flex-col gap-1.5">
                                    <span class="font-medium text-gray-800 text-xs">
                                        @if($class->schedules->isNotEmpty())
                                            {{ $class->schedules->pluck('day_of_week')->implode(', ') }}
                                        @else
                                            <span class="text-gray-400 italic">No days set</span>
                                        @endif
                                    </span>
                                    <span class="inline-block bg-blue-50 text-blue-700 border border-blue-100 px-2 py-0.5 rounded-md text-[10px] font-bold w-fit">
                                        {{ \Carbon\Carbon::parse($class->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($class->end_time)->format('H:i') }}
                                    </span>
                                </div>
                            </td>

                            {{-- Teacher (Baru - Mirip Admin) --}}
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

                            {{-- Action --}}
                            <td class="px-6 py-5 text-center">
                                <a href="{{ route('teacher.classes.detail', $class->id) }}" 
                                   class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-white border border-gray-200 text-gray-400 hover:text-blue-600 hover:border-blue-200 hover:bg-blue-50 transition-all shadow-sm" 
                                   title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="bg-gray-50 rounded-full p-4 mb-3">
                                        <i class="fas fa-folder-open text-3xl text-gray-300"></i>
                                    </div>
                                    <p class="text-base font-medium text-gray-600">No classes found.</p>
                                    <p class="text-xs text-gray-400 mt-1">Try adjusting your filters or contact admin.</p>
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
</div>
@endsection