{{-- resources/views/teacher/classes/index.blade.php --}}

<x-app-layout>
    
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- BREADCRUMB & TITLE (TETAP SAMA) --}}
            <nav class="flex mb-5" aria-label="Breadcrumb">
                {{-- ... (Kode Breadcrumb sama seperti sebelumnya) ... --}}
                 <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('teacher.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-blue-600 transition-colors">
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

            <div class="mb-8 flex flex-col sm:flex-row sm:justify-between sm:items-end gap-4">
                <div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent inline-block">
                        Active Classes
                    </h1>
                    <p class="text-gray-500 text-sm mt-1">View and manage attendance for all active classes.</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

                {{-- FILTERS (TETAP SAMA) --}}
                <div class="p-4 sm:p-6 border-b border-gray-200 flex flex-col gap-4">
                    <div class="w-full">
                        <form action="{{ route('teacher.classes.index') }}" method="GET" class="relative w-full">
                            @foreach(['category', 'class_id', 'day'] as $key)
                                @if(request($key)) <input type="hidden" name="{{ $key }}" value="{{ request($key) }}"> @endif
                            @endforeach
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Search class name or classroom..." 
                                   class="w-full h-11 pl-12 pr-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm shadow-sm transition-all placeholder-gray-400">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" /></svg>
                            </div>
                        </form>
                    </div>
                    
                    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                        <form action="{{ route('teacher.classes.index') }}" method="GET" class="flex flex-wrap items-center gap-2 w-full lg:w-auto">
                            @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                            
                            {{-- Day Filter --}}
                            <div class="relative flex-grow sm:flex-grow-0">
                                <select name="day" onchange="this.form.submit()" 
                                        class="h-10 w-full sm:w-auto px-3 py-2 pr-8 border border-gray-300 rounded-lg text-sm text-gray-700 bg-white focus:ring-2 focus:ring-blue-500 cursor-pointer shadow-sm">
                                    <option value="" {{ empty($currentDay) ? 'selected' : '' }}>All Days</option>
                                    @foreach($daysOfWeek as $day)
                                        <option value="{{ $day }}" {{ $currentDay == $day ? 'selected' : '' }}>{{ $day }}</option>
                                    @endforeach
                                </select>
                            </div>

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

                            {{-- Class Name Filter (class_id) --}}
                            <div class="relative flex-grow sm:flex-grow-0">
                                <select name="class_id" onchange="this.form.submit()" 
                                        class="h-10 w-full sm:w-auto px-3 py-2 pr-8 border border-gray-300 rounded-lg text-sm text-gray-700 bg-white focus:ring-2 focus:ring-blue-500 cursor-pointer shadow-sm">
                                    <option value="">All Classes</option>
                                    @foreach($classesForFilter as $filterClass)
                                        <option value="{{ $filterClass->id }}" {{ request('class_id') == $filterClass->id ? 'selected' : '' }}>
                                            {{-- MODIFIKASI: Hanya menampilkan nama kelas, tanpa classroom --}}
                                            {{ $filterClass->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            @php
                                $isFiltered = request('category') || request('search') || request('class_id') || !empty(request('day'));
                                $isDefaultDay = empty(request('day')) && $currentDay == \Carbon\Carbon::now()->format('l');
                            @endphp
                            
                            @if($isFiltered || !$isDefaultDay)
                                <a href="{{ route('teacher.classes.index') }}" class="h-10 w-10 flex items-center justify-center bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 rounded-lg transition-colors flex-shrink-0 shadow-sm" title="Reset Filters">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </a>
                            @endif
                        </form>
                    </div>
                </div>

                {{-- TABLE CONTENT --}}
                <div class="overflow-x-auto">
                    {{-- MODIFIKASI: 
                         1. Hapus 'table-fixed'.
                         2. Tambahkan 'min-w-max' agar tabel melebar sesuai konten (scrollable).
                    --}}
                    <table class="w-full text-left border-collapse min-w-max">
                        <thead class="bg-gray-50 text-xs text-gray-500 font-bold uppercase border-b border-gray-200 tracking-wider">
                            <tr>
                                {{-- Gunakan whitespace-nowrap agar header tidak turun baris --}}
                                <th class="px-6 py-4 w-16 text-center whitespace-nowrap">No</th>
                                <th class="px-6 py-4 whitespace-nowrap">Category</th>
                                <th class="px-6 py-4 whitespace-nowrap">Class Name</th>
                                <th class="px-6 py-4 whitespace-nowrap">Classroom</th>
                                <th class="px-6 py-4 whitespace-nowrap">Day</th>
                                <th class="px-6 py-4 whitespace-nowrap">Time</th>
                                <th class="px-6 py-4 whitespace-nowrap text-center">Status</th>
                                <th class="px-6 py-4 whitespace-nowrap text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm text-gray-700 bg-white">
                            @forelse($classes as $index => $class)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    {{-- No --}}
                                    <td class="px-6 py-5 text-center text-gray-500 font-medium">
                                        {{ $classes->firstItem() + $index }}
                                    </td>
                                    
                                    {{-- Category --}}
                                    <td class="px-6 py-5 text-gray-700 capitalize font-medium whitespace-nowrap">
                                        {{ str_replace('_', ' ', $class->category ?? '-') }}
                                    </td>
                                    
                                    {{-- Class Name --}}
                                    <td class="px-6 py-5 font-medium text-gray-900 text-base whitespace-nowrap">
                                        {{ $class->name }}
                                    </td>
                                    
                                    {{-- Classroom --}}
                                    <td class="px-6 py-5 text-gray-600 font-medium whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-door-open text-gray-300"></i> 
                                            {{ $class->classroom }}
                                        </div>
                                    </td>
                                    
                                    {{-- Day --}}
                                    <td class="px-6 py-5">
                                        {{-- Badge hari dibiarkan wrap jika sangat banyak, atau bisa di-nowrap juga jika mau scroll --}}
                                        <div class="flex flex-wrap gap-1 min-w-[200px]">
                                            @forelse($class->schedules as $schedule)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-bold border 
                                                    {{ $schedule->teacher_type == 'form' ? 'bg-blue-50 text-blue-700 border-blue-100' : 'bg-purple-50 text-purple-700 border-purple-100' }}">
                                                    <strong>{{ substr($schedule->day_of_week, 0, 3) }}</strong> 
                                                    <span class="mx-0.5 opacity-50 font-normal">|</span> 
                                                    {{ $schedule->teacher_type == 'form' ? 'F' : 'L' }}
                                                </span>
                                            @empty
                                                <span class="text-gray-400 italic text-xs">-</span>
                                            @endforelse
                                        </div>
                                    </td>

                                    {{-- Time --}}
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <span class="inline-block bg-gray-50 text-gray-600 border border-gray-200 px-2 py-0.5 rounded-md text-[10px] font-bold w-fit">
                                            {{ \Carbon\Carbon::parse($class->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($class->end_time)->format('H:i') }}
                                        </span>
                                    </td>
                                    
                                    {{-- Status --}}
                                    <td class="px-6 py-5 text-center whitespace-nowrap">
                                        @if($class->is_active)
                                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold border border-green-200">Active</span>
                                        @else
                                            <span class="px-3 py-1 bg-gray-100 text-gray-500 rounded-full text-xs font-bold border border-gray-200">Inactive</span>
                                        @endif
                                    </td>
                                    
                                    {{-- Action --}}
                                    <td class="px-6 py-5 text-center whitespace-nowrap">
                                        <a href="{{ route('teacher.classes.detail', $class->id) }}" 
                                           class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 border border-indigo-100 hover:bg-indigo-600 hover:text-white transition-all shadow-sm group" 
                                           title="View Details">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 group-hover:scale-110 transition-transform duration-200">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center text-gray-500">
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
                
                @if($classes->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                        {{ $classes->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>