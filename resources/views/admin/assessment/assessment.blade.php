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

            {{-- TABLE SECTION --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                
                {{-- HEADER FILTER (Sama seperti sebelumnya) --}}
                <div class="p-4 sm:p-6 border-b border-gray-200">
                    {{-- ... (Search & Filter Logic) ... --}}
                    <form action="{{ route('admin.assessment.index') }}" method="GET" class="w-full">
                        {{-- Search Input Hidden agar tidak hilang saat filter berubah --}}
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        
                        <div class="flex flex-wrap items-end gap-3 ">
                            <select name="academic_year" onchange="this.form.submit()" class="h-10 px-3 border pr-10  border-gray-300 rounded-lg text-sm bg-gray-50 focus:ring-2 focus:ring-blue-500">
                                <option value="">All Years</option>
                                @foreach($years as $year)
                                    <option value="{{ $year }}" {{ request('academic_year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endforeach
                            </select>

                            <select name="category" onchange="this.form.submit()" class="h-10 px-3 border pr-10 border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-blue-500">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>{{ ucwords(str_replace('_', ' ', $category)) }}</option>
                                @endforeach
                            </select>

                            <select name="type" onchange="this.form.submit()" class="h-10 px-3 border border-gray-300 pr-10 rounded-lg text-sm bg-white focus:ring-2 focus:ring-blue-500">
                                <option value="">All Types</option>
                                @foreach($types as $type)
                                    <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                                @endforeach
                            </select>

                            <select name="assessment_status" onchange="this.form.submit()" class="h-10 px-3 pr-10 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-blue-500">
                                <option value="">All Progress</option>
                                @foreach($statuses as $statusOption)
                                    <option value="{{ $statusOption }}" {{ request('assessment_status') == $statusOption ? 'selected' : '' }}>{{ ucfirst($statusOption) }}</option>
                                @endforeach
                            </select>

                            @if(request()->anyFilled(['academic_year', 'category', 'type', 'assessment_status', 'search']))
                            <a href="{{ route('admin.assessment.index') }}" class="h-10 w-10 flex items-center justify-center bg-red-50 text-red-600 border border-red-200 rounded-lg" title="Reset Filters">
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
                                <th class="px-6 py-4">Class Name</th>
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
                                <td class="px-6 py-5 text-center text-gray-500">
                                    {{ $assessments->firstItem() + $index }}
                                </td>
                                
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-800">{{ $assessment->classModel->name ?? '-' }}</div>
                                    <div class="text-[10px] uppercase text-blue-600 font-bold">{{ str_replace('_', ' ', $assessment->classModel->category ?? '-') }}</div>
                                </td>

                                <td class="px-6 py-4 text-center font-bold text-xs text-gray-600">
                                    {{ $assessment->classModel->academic_year ?? '-' }}
                                </td>

                                <td class="px-6 py-4 text-center text-gray-600">
                                    {{ $assessment->written_date ? \Carbon\Carbon::parse($assessment->written_date)->format('d M Y') : '-' }}
                                </td>
                                
                                <td class="px-6 py-4 text-center">
                                    <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest {{ $assessment->type == 'final' ? 'bg-indigo-100 text-indigo-700' : 'bg-emerald-100 text-emerald-700' }}">
                                        {{ ucfirst($assessment->type) }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-center">
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
                                        
                                        {{-- 1. VIEW ICON (Selalu Bisa) --}}
                                        <a href="{{ route('admin.classes.assessment.detail', ['classId' => $assessment->class_id, 'type' => $assessment->type, 'from' => 'assessment']) }}" 
                                            class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="View Grades">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                        </a>
                                        
                                        {{-- 2. EDIT ICON (With Alert Logic) --}}
                                        <button type="button"
                                            onclick="handleEdit('{{ $assessment->status }}', '{{ $assessment->class_id }}', '{{ $assessment->type }}')"
                                            class="p-1.5 transition-colors rounded-lg {{ $assessment->status === 'submitted' ? 'text-gray-400 hover:text-emerald-600 hover:bg-emerald-50' : 'text-gray-300' }}" 
                                            title="Review & Finalize">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                        </button>

                                        {{-- 3. PRINT ICON (With Alert Logic) --}}
                                        <a href="{{ route('admin.classes.assessment.print', ['classId' => $assessment->class_id, 'sessionId' => $assessment->id]) }}" 
                                        target="_blank"
                                        class="p-1.5 transition-colors rounded-lg flex items-center justify-center {{ $assessment->status === 'final' ? 'text-gray-400 hover:text-purple-600 hover:bg-purple-50' : 'text-gray-300 pointer-events-none cursor-not-allowed' }}" 
                                        title="Print Report Card">
                                            
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 00-2 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-400 italic">No assessment sessions found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- PAGINATION --}}
                @if($assessments->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between bg-white text-xs font-medium text-gray-500">
                    <span>Showing {{ $assessments->firstItem() }} to {{ $assessments->lastItem() }} of {{ $assessments->total() }} results</span>
                    {{ $assessments->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- SWEETALERT SCRIPT --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // LOGIC EDIT: Harus berstatus 'submitted'
        function handleEdit(status, classId, type) {
            if (status === 'draft') {
                Swal.fire({
                    icon: 'info',
                    title: 'Teacher is Working',
                    text: 'This assessment is still in DRAFT mode. Please wait for the teacher to submit the grades before you can review/finalize.',
                    confirmButtonColor: '#3B82F6'
                });
            } else if (status === 'final') {
                Swal.fire({
                    icon: 'success',
                    title: 'Already Finalized',
                    text: 'This assessment is already FINAL. You can only view the results now.',
                    confirmButtonColor: '#8B5CF6'
                });
            } else {
                // Status is 'submitted' - Go to edit mode
                window.location.href = `{{ url('/admin/classes') }}/${classId}/assessment/${type}?from=assessment&mode=edit`;
            }
        }

        // LOGIC PRINT: Harus berstatus 'final'
        function handlePrint(status, classId, type) {
            if (status !== 'final') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Not Finalized Yet',
                    text: 'You can only print report cards once the assessment status is set to FINAL.',
                    confirmButtonColor: '#F59E0B'
                });
            } else {
                Swal.fire({
                    icon: 'question',
                    title: 'Print Options',
                    text: 'Prepare report cards for ' + type + ' term?',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Generate PDF',
                    confirmButtonColor: '#8B5CF6'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // window.location.href = `/admin/assessment/print/${classId}/${type}`;
                    }
                });
            }
        }
    </script>
</x-app-layout>