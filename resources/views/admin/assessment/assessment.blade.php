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
                <p class="text-gray-500 text-sm mt-1">Monitor progress (Draft/Submitted/Final) and manage grades.</p>
            </div>

            {{-- TABLE SECTION (Card Utama) --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                
                {{-- HEADER FILTER --}}
                <div class="p-4 sm:p-6 border-b border-gray-200">
                    
                    {{-- SEARCH BAR --}}
                    <div class="w-full mb-4">
                        <form action="{{ route('admin.assessment.index') }}" method="GET" class="relative w-full">
                            @foreach(['academic_year', 'category', 'type', 'class_id', 'class_status', 'assessment_status'] as $key)
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
                            <div class="relative">
                                <select name="academic_year" onchange="this.form.submit()" class="h-10 w-full sm:w-32 px-3 pr-10 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 bg-gray-50 focus:ring-2 focus:ring-blue-500 shadow-sm cursor-pointer appearance-none">
                                    <option value="">All Years</option>
                                    @foreach($years as $year)
                                        <option value="{{ $year }}" {{ request('academic_year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="relative">
                                <select name="category" onchange="this.form.submit()" class="h-10 w-full sm:w-36 px-3 pr-10 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white focus:ring-2 focus:ring-blue-500 shadow-sm cursor-pointer appearance-none">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>{{ ucwords(str_replace('_', ' ', $category)) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="relative">
                                <select name="class_status" onchange="this.form.submit()" class="h-10 w-full sm:w-36 px-3 pr-10 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white focus:ring-2 focus:ring-blue-500 shadow-sm cursor-pointer appearance-none">
                                    <option value="" {{ request('class_status') == '' ? 'selected' : '' }}>All Status</option>
                                    <option value="active" {{ request('class_status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('class_status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            
                            <div class="relative">
                                <select name="type" onchange="this.form.submit()" class="h-10 w-full sm:w-32 px-3 pr-10 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white focus:ring-2 focus:ring-blue-500 shadow-sm cursor-pointer appearance-none">
                                    <option value="">All Types</option>
                                    @foreach($types as $type)
                                        <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="relative">
                                <select name="assessment_status" onchange="this.form.submit()" class="h-10 w-full sm:w-36 px-3 pr-10 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white focus:ring-2 focus:ring-blue-500 shadow-sm cursor-pointer appearance-none">
                                    <option value="">All Progress</option>
                                    @foreach($statuses as $statusOption)
                                        <option value="{{ $statusOption }}" {{ request('assessment_status') == $statusOption ? 'selected' : '' }}>
                                            {{ ucfirst($statusOption) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            @if(request('academic_year') || request('category') || request('type') || request('class_id') || request('search') || request('assessment_status') || (request()->has('class_status') && request('class_status') != 'active'))
                            <a href="{{ route('admin.assessment.index') }}" class="h-10 w-10 flex items-center justify-center bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 rounded-lg transition-colors shadow-sm" title="Reset Filters">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </a>
                            @endif
                        </div>
                    </form>
                </div>

                {{-- TABLE CONTENT --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-max">
                        <thead class="bg-gray-50 text-xs text-gray-500 font-bold uppercase border-b border-gray-200 tracking-wider">
                            <tr>
                                <th class="px-6 py-4 w-16 text-center">No</th>
                                <th class="px-6 py-4">Category</th>
                                <th class="px-6 py-4">Class Name</th>
                                <th class="px-6 py-4 text-center">Class Status</th>
                                <th class="px-6 py-4 text-center">Year</th>
                                <th class="px-6 py-4 text-center">Exam Date</th>
                                <th class="px-6 py-4 text-center">Type</th>
                                <th class="px-6 py-4 text-center">Progress</th>
                                <th class="px-6 py-4 text-center w-32">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                            @forelse($assessments as $index => $assessment)
                            <tr class="hover:bg-gray-50 transition-colors group">
                                <td class="px-6 py-5 text-center text-gray-500 font-medium">
                                    {{ $assessments->firstItem() + $index }}
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-100 text-blue-800 border border-blue-200">
                                        {{ str_replace('_', ' ', $assessment->classModel->category ?? '-') }}
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4 font-semibold text-gray-800 whitespace-nowrap">
                                    {{ $assessment->classModel->name ?? '-' }}
                                </td>

                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    @if($assessment->classModel && $assessment->classModel->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-700 border border-green-200">Active</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-500 border border-gray-200">Inactive</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-gray-600 font-bold text-xs whitespace-nowrap text-center">
                                    {{ $assessment->classModel->academic_year ?? '-' }}
                                </td>

                                {{-- [FIXED] Kolom written_date --}}
                                <td class="px-6 py-4 text-center text-gray-600 font-medium whitespace-nowrap">
                                    {{ $assessment->written_date ? \Carbon\Carbon::parse($assessment->written_date)->format('d M Y') : '-' }}
                                </td>
                                
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest
                                        {{ $assessment->type == 'final' ? 'bg-indigo-100 text-indigo-700 border border-indigo-200' : 'bg-emerald-100 text-emerald-700 border border-emerald-200' }}">
                                        {{ ucfirst($assessment->type) }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    @php
                                        $statusColor = match($assessment->status) {
                                            'submitted' => 'bg-blue-100 text-blue-700 border-blue-200', 
                                            'final'     => 'bg-purple-100 text-purple-700 border-purple-200', 
                                            default     => 'bg-gray-100 text-gray-600 border-gray-200', 
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide border {{ $statusColor }}">
                                        {{ ucfirst($assessment->status) }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center gap-3">
                                        <a href="{{ route('admin.classes.assessment.detail', ['classId' => $assessment->class_id, 'type' => $assessment->type, 'from' => 'assessment']) }}" 
                                            class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="View Grades">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                        </a>
                                        
                                        @if($assessment->status === 'submitted')
                                            <a href="{{ route('admin.classes.assessment.detail', ['classId' => $assessment->class_id, 'type' => $assessment->type, 'from' => 'assessment', 'mode' => 'edit']) }}" 
                                            class="p-1.5 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Review & Finalize">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="px-6 py-12 text-center text-gray-400 italic">No assessment sessions found for current filters.</td>
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