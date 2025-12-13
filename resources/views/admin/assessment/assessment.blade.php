<x-app-layout>
    <x-slot name="header"></x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- BREADCRUMB --}}
            <nav class="flex mb-5" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-blue-600 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                            Dashboard
                        </a>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            <span class="ml-1 text-sm font-medium text-gray-900 md:ml-2">Assessment Recap</span>
                        </div>
                    </li>
                </ol>
            </nav>

            {{-- TITLE SECTION --}}
            <div class="mb-8">
                <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent inline-block">
                    Assessment Sessions
                </h1>
                <p class="text-gray-500 text-sm mt-1">Monitor and manage mid/final term grades across all classes.</p>
            </div>

            {{-- TABLE SECTION (Card Utama) --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                
                {{-- HEADER FILTER --}}
                <div class="p-4 sm:p-6 border-b border-gray-200">
                    
                    {{-- SEARCH BAR --}}
                    <div class="w-full mb-4">
                        <form action="{{ route('admin.assessment.index') }}" method="GET" class="relative w-full">
                            @foreach(['academic_year', 'category', 'type', 'class_id', 'class_status'] as $key)
                                @if(request()->has($key)) 
                                    <input type="hidden" name="{{ $key }}" value="{{ request($key) }}"> 
                                @elseif($key == 'class_status' && !request()->has('class_status'))
                                    <input type="hidden" name="class_status" value="active">
                                @endif
                            @endforeach
                            
                            <input type="text" name="search" value="{{ request('search') }}" 
                                placeholder="Search class name..." 
                                class="w-full h-10 pl-12 pr-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm shadow-sm transition-all">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" /></svg>
                            </div>
                        </form>
                    </div>

                    {{-- FILTER DROPDOWNS --}}
                    <form action="{{ route('admin.assessment.index') }}" method="GET" class="w-full">
                        @if(request('search')) 
                            <input type="hidden" name="search" value="{{ request('search') }}"> 
                        @endif
                        
                        <div class="flex flex-wrap items-end gap-3">
                            
                            {{-- Year Filter --}}
                            <div class="relative">
                                <select name="academic_year" onchange="this.form.submit()" 
                                        class="h-10 w-full sm:w-32 px-3 pr-10 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 bg-gray-50 focus:ring-2 focus:ring-blue-500 shadow-sm cursor-pointer appearance-none">
                                    <option value="">All Years</option>
                                    @foreach($years as $year)
                                        <option value="{{ $year }}" {{ request('academic_year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Category Filter --}}
                            <div class="relative">
                                <select name="category" onchange="this.form.submit()" 
                                        class="h-10 w-full sm:w-36 px-3 pr-10 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white focus:ring-2 focus:ring-blue-500 shadow-sm cursor-pointer appearance-none">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>{{ ucwords(str_replace('_', ' ', $category)) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Class Filter --}}
                            <div class="relative">
                                <select name="class_id" onchange="this.form.submit()" 
                                        class="h-10 w-full sm:w-36 px-3 pr-10 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white focus:ring-2 focus:ring-blue-500 shadow-sm cursor-pointer appearance-none">
                                    <option value="">All Classes</option>
                                    @foreach($classes as $classItem)
                                        <option value="{{ $classItem->id }}" {{ request('class_id') == $classItem->id ? 'selected' : '' }}>{{ $classItem->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Class Status Filter --}}
                            <div class="relative">
                                <select name="class_status" onchange="this.form.submit()" 
                                        class="h-10 w-full sm:w-36 px-3 pr-10 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white focus:ring-2 focus:ring-blue-500 shadow-sm cursor-pointer appearance-none">
                                    <option value="" {{ request('class_status') == '' ? 'selected' : '' }}>All Status</option>
                                    <option value="active" {{ request('class_status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('class_status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>

                            {{-- Exam Type Filter --}}
                            <div class="relative">
                                <select name="type" onchange="this.form.submit()" 
                                        class="h-10 w-full sm:w-32 px-3 pr-10 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white focus:ring-2 focus:ring-blue-500 shadow-sm cursor-pointer appearance-none">
                                    <option value="">All Types</option>
                                    @foreach($types as $type)
                                        <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Reset Filter Button --}}
                            @if(request('academic_year') || request('category') || request('type') || request('class_id') || request('search') || request()->has('class_status') && request('class_status') != 'active')
                            <div class="flex flex-col gap-1">
                                <div class="h-3 hidden sm:block">&nbsp;</div> 
                                <a href="{{ route('admin.assessment.index') }}" 
                                   class="h-10 w-10 flex items-center justify-center bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 rounded-lg transition-colors shadow-sm" 
                                   title="Reset Filters">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </a>
                            </div>
                            @endif
                        </div>
                    </form>
                </div>

                {{-- TABLE CONTENT --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-max">
                        <thead class="bg-gray-50 text-xs text-gray-500 font-bold uppercase border-b border-gray-200 tracking-wider">
                            <tr>
                                <th class="px-6 py-4 w-16 whitespace-nowrap text-center font-bold">No</th>
                                <th class="px-6 py-4 whitespace-nowrap font-bold">Category</th>
                                <th class="px-6 py-4 whitespace-nowrap font-bold">Class Name</th>
                                <th class="px-6 py-4 whitespace-nowrap text-center font-bold">Exam Date</th>
                                <th class="px-6 py-4 whitespace-nowrap text-center font-bold">Type</th>
                                <th class="px-6 py-4 whitespace-nowrap text-center font-bold">Year</th> 
                                <th class="px-6 py-4 whitespace-nowrap text-center font-bold w-32">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                            @forelse($assessments as $index => $assessment)
                            <tr class="hover:bg-gray-50 transition-colors group">
                                <td class="px-6 py-5 text-center text-gray-500 font-medium">
                                    {{ $assessments->firstItem() + $index }}
                                </td>
                                
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                                        {{ $assessment->classModel->category == 'level' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-600 border border-gray-200' }}">
                                        {{ str_replace('_', ' ', $assessment->classModel->category ?? '-') }}
                                    </span>
                                </td>

                                <td class="px-6 py-5 font-bold text-gray-900 text-base whitespace-nowrap">
                                    {{ $assessment->classModel->name ?? '-' }}
                                    @if($assessment->classModel && !$assessment->classModel->is_active)
                                    <span class="text-[9px] text-red-600 ml-1">(Inactive)</span>
                                    @endif
                                </td>

                                {{-- UPDATED: Cek apakah tanggal ada. Jika NULL tampilkan '-' --}}
                                <td class="px-6 py-5 text-center text-gray-600 font-medium whitespace-nowrap">
                                    {{ $assessment->date ? \Carbon\Carbon::parse($assessment->date)->format('d M Y') : '-' }}
                                </td>
                                
                                <td class="px-6 py-5 text-center whitespace-nowrap">
                                    <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest
                                        {{ $assessment->type == 'final' ? 'bg-indigo-100 text-indigo-700 border border-indigo-200' : 'bg-emerald-100 text-emerald-700 border border-emerald-200' }}">
                                        {{ ucfirst($assessment->type) }}
                                    </span>
                                </td>

                                <td class="px-6 py-5 text-center font-bold text-gray-700 text-xs">
                                    {{ $assessment->classModel->academic_year ?? '-' }}
                                </td>

                                <td class="px-6 py-5 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center gap-3">
                                        <a href="{{ route('admin.classes.assessment.detail', ['classId' => $assessment->class_id, 'type' => $assessment->type, 'from' => 'assessment']) }}" 
                                           class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="View Grades">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.classes.assessment.detail', ['classId' => $assessment->class_id, 'type' => $assessment->type, 'from' => 'assessment', 'mode' => 'edit']) }}" 
                                           class="p-1.5 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Edit Session">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                        </a>
                                        <button class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-400 italic">No assessment sessions found for current filters.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($assessments->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between bg-white text-xs font-medium text-gray-500">
                    <span>Showing {{ $assessments->firstItem() }} to {{ $assessments->lastItem() }} of {{ $assessments->total() }} results</span>
                    {{ $assessments->links() }}
                </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>