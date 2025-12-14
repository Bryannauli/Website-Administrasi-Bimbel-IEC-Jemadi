{{-- resources/views/teacher/classes/index.blade.php --}}

<x-app-layout>
    
    {{-- Kita HAPUS x-slot header agar tidak muncul di dalam navbar putih --}}
    
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- ================================================= --}}
            {{-- 1. BREADCRUMB (POSISI DI BAWAH NAVBAR SEKARANG) --}}
            {{-- ================================================= --}}
            <nav class="flex mb-5" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li class="inline-flex items-center">
                        <a href="{{ route('teacher.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-blue-600 transition-colors">
                            <svg class="w-3 h-3 mr-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                            </svg>
                            Home
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                            </svg>
                            <span class="ml-1 text-sm font-medium text-gray-700 md:ml-2">Classes</span>
                        </div>
                    </li>
                </ol>
            </nav>

            {{-- ================================================= --}}
            {{-- 2. JUDUL HALAMAN (PAGE TITLE)                     --}}
            {{-- ================================================= --}}
            <div class="mb-8 flex flex-col sm:flex-row sm:justify-between sm:items-end gap-4">
                <div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent inline-block">
                        My Classes
                    </h1>
                    <p class="text-gray-500 text-sm mt-1">View and manage attendance for your assigned classes.</p>
                </div>
                
                {{-- Export Button --}}
                <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition shadow-sm flex items-center text-sm font-medium">
                    <i class="fas fa-download mr-2 text-gray-400"></i> Export
                </button>
            </div>

            {{-- ================================================= --}}
            {{-- 3. TABEL DATA (CONTENT CARD)                      --}}
            {{-- ================================================= --}}
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
                                   class="w-full h-11 pl-12 pr-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm shadow-sm transition-all placeholder-gray-400">

                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                        </form>
                    </div>
                    
                    {{-- FILTERS ROW --}}
                    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">

                        <form action="{{ route('teacher.classes.index') }}" method="GET" class="flex flex-wrap items-center gap-2 w-full lg:w-auto">
                            @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                            
                            {{-- Category Filter --}}
                            <div class="relative flex-grow sm:flex-grow-0">
                                <select name="category" onchange="this.form.submit()" 
                                        class="h-10 w-full sm:w-auto px-3 py-2 pr-8 border border-gray-300 rounded-lg text-sm text-gray-700 bg-white focus:ring-2 focus:ring-blue-500 cursor-pointer shadow-sm">
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
                                        class="h-10 w-full sm:w-auto px-3 py-2 pr-8 border border-gray-300 rounded-lg text-sm text-gray-700 bg-white focus:ring-2 focus:ring-blue-500 cursor-pointer shadow-sm">
                                    <option value="">All Status</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>

                            {{-- Reset Filter Button --}}
                            @if(request('category') || request('search') || request('status'))
                                <a href="{{ route('teacher.classes.index') }}" class="h-10 w-10 flex items-center justify-center bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 rounded-lg transition-colors flex-shrink-0 shadow-sm" title="Reset Filters">
                                    <i class="fas fa-times"></i>
                                </a>
                            @endif
                        </form>

                    </div>
                </div>

                {{-- TABLE CONTENT --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-max">
                        <thead class="bg-gray-50 text-xs text-gray-500 font-bold uppercase border-b border-gray-200 tracking-wider">
                            <tr>
                                <th class="px-6 py-4 w-16 whitespace-nowrap text-center">No</th>
                                <th class="px-6 py-4 whitespace-nowrap">Category</th>
                                <th class="px-6 py-4 whitespace-nowrap">Class Name</th>
                                <th class="px-6 py-4 whitespace-nowrap">Classroom</th>
                                <th class="px-6 py-4 whitespace-nowrap">Schedule</th>
                                <th class="px-6 py-4 whitespace-nowrap text-center">Status</th>
                                <th class="px-6 py-4 whitespace-nowrap text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm text-gray-700 bg-white">
                            @forelse($classes as $index => $class)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-5 text-center text-gray-500 font-medium">{{ $classes->firstItem() + $index }}</td>
                                    <td class="px-6 py-5">
                                        <span class="px-2.5 py-1 bg-gray-100 text-gray-600 border border-gray-200 rounded text-xs font-bold uppercase tracking-wide">
                                            {{ str_replace('_', ' ', $class->category ?? '-') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5">
                                        <span class="font-bold text-gray-900 text-base block">{{ $class->name }}</span>
                                        <span class="text-xs text-gray-400 mt-0.5 block">ID: #{{ $class->id }}</span>
                                    </td>
                                    <td class="px-6 py-5 text-gray-600 font-medium whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-door-open text-gray-300"></i> {{ $class->classroom }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex flex-col gap-1.5">
                                            <div class="flex items-center gap-2 text-gray-700 font-medium text-xs">
                                                <i class="fas fa-calendar-alt text-blue-400 w-3"></i>
                                                @if($class->schedules->isNotEmpty())
                                                    {{ $class->schedules->pluck('day_of_week')->implode(', ') }}
                                                @else
                                                    <span class="text-gray-400 italic">No days set</span>
                                                @endif
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <i class="fas fa-clock text-blue-400 w-3 text-xs"></i>
                                                <span class="bg-blue-50 text-blue-700 border border-blue-100 px-2 py-0.5 rounded text-[10px] font-bold">
                                                    {{ \Carbon\Carbon::parse($class->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($class->end_time)->format('H:i') }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        @if($class->is_active)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span> Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                                <span class="w-1.5 h-1.5 bg-gray-500 rounded-full mr-1.5"></span> Inactive
                                            </span>
                                        @endif
                                    </td>
                               <td class="px-6 py-5 text-center">
    <a href="{{ route('teacher.classes.detail', $class->id) }}" 
       class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 border border-indigo-100 hover:bg-indigo-600 hover:text-white transition-all shadow-sm group" 
       title="View Details">
        
        {{-- Ikon Mata (SVG) --}}
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 group-hover:scale-110 transition-transform duration-200">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>

    </a>
</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="bg-gray-50 rounded-full p-4 mb-3 border border-gray-100">
                                                <i class="fas fa-folder-open text-3xl text-gray-300"></i>
                                            </div>
                                            <p class="text-base font-medium text-gray-600">No classes found.</p>
                                            <p class="text-xs text-gray-400 mt-1">Try adjusting your filters or search query.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                {{-- Pagination --}}
                @if($classes->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                        {{ $classes->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

</x-app-layout>