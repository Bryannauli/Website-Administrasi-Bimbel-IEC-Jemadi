<x-app-layout>
    
    <x-slot name="header"></x-slot>

    <div class="py-8" x-data="{ openModal: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            {{-- 1. BREADCRUMB --}}
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                    <li class="inline-flex items-center">
                        <a href="{{ route('teacher.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-blue-600 transition-colors">
                            {{-- Icon Home SVG --}}
                            <svg class="w-3 h-3 mr-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                            </svg>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                            </svg>
                            <a href="{{ route('teacher.classes.index') }}" class="ml-1 text-sm font-medium text-gray-500 hover:text-blue-600 md:ml-2 transition-colors">Classes</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                            </svg>
                            <span class="ml-1 text-sm font-bold text-gray-800 md:ml-2">Detail</span>
                        </div>
                    </li>
                </ol>
            </nav>

            {{-- 2. HEADER INFO KELAS --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="border-l-4 border-blue-500 p-6 sm:p-8">
                    <div class="flex flex-col md:flex-row md:items-start justify-between gap-6">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h2 class="text-3xl font-bold text-gray-900">{{ $class->name }}</h2>
                                <span class="bg-blue-50 text-blue-700 border border-blue-100 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide">
                                    {{ str_replace('_', ' ', $class->category) }}
                                </span>
                            </div>
                            
                            <p class="text-gray-500 text-sm mb-6 max-w-2xl">
                                Class detail information and session management.
                            </p>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                {{-- Schedule Days --}}
                                <div class="flex items-center p-3 bg-gray-50 rounded-lg border border-gray-100">
                                    <div class="p-2 bg-white rounded-md shadow-sm text-blue-500 mr-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400 font-medium uppercase">Days</p>
                                        <p class="text-sm font-bold text-gray-700">
                                            {{ $class->schedules->count() > 0 ? $class->schedules->pluck('day_of_week')->implode(', ') : '-' }}
                                        </p>
                                    </div>
                                </div>

                                {{-- Time --}}
                                <div class="flex items-center p-3 bg-gray-50 rounded-lg border border-gray-100">
                                    <div class="p-2 bg-white rounded-md shadow-sm text-purple-500 mr-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400 font-medium uppercase">Time</p>
                                        <p class="text-sm font-bold text-gray-700">
                                            {{ \Carbon\Carbon::parse($class->start_time)->format('H:i') }} - 
                                            {{ \Carbon\Carbon::parse($class->end_time)->format('H:i') }}
                                        </p>
                                    </div>
                                </div>

                                {{-- Room --}}
                                <div class="flex items-center p-3 bg-gray-50 rounded-lg border border-gray-100">
                                    <div class="p-2 bg-white rounded-md shadow-sm text-green-500 mr-3">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-400 font-medium uppercase">Room</p>
                                        <p class="text-sm font-bold text-gray-700">{{ $class->classroom }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. STUDENT LIST --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        Student List
                    </h3>
                    
                    <form method="GET" action="{{ url()->current() }}" class="flex items-center text-sm">
                        <span class="text-gray-500 mr-2">Show</span>
                        <select name="per_page" onchange="this.form.submit()" class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm text-sm py-1 pl-2 pr-8">
                            <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5</option>
                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        </select>
                        <span class="text-gray-500 ml-2">entries</span>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 text-xs text-gray-500 font-bold uppercase border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 w-16 text-center">No</th>
                                <th class="px-6 py-4">Student Name</th>
                                <th class="px-6 py-4">ID Number</th>
                                <th class="px-6 py-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                            @forelse($students as $student)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 text-center text-gray-500">{{ $loop->iteration + $students->firstItem() - 1 }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="h-8 w-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs mr-3 border border-blue-200">
                                                {{ substr($student->name, 0, 2) }}
                                            </div>
                                            <span class="font-medium text-gray-900">{{ $student->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-mono text-gray-500">{{ $student->student_number }}</td>
                                    <td class="px-6 py-4 text-center">
                                        @if($student->is_active)
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 border border-green-200">Active</span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 border border-red-200">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">No students found in this class.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($students->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                        {{ $students->links() }}
                    </div>
                @endif
            </div>

            {{-- 4. ATTENDANCE HISTORY --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Attendance History
                    </h3>
                    <button @click="openModal = true" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center shadow-sm text-sm font-medium">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Add Session
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 text-xs text-gray-500 font-bold uppercase border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 w-16 text-center">No</th>
                                <th class="px-6 py-4">Date</th>
                                <th class="px-6 py-4">Topics</th>
                                <th class="px-6 py-4">Teacher</th>
                                <th class="px-6 py-4 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                            @forelse($classSessions as $session)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 text-center text-gray-500">{{ $loop->iteration + $classSessions->firstItem() - 1 }}</td>
                                    <td class="px-6 py-4 font-medium text-gray-900">
                                        {{ \Carbon\Carbon::parse($session->date)->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-600 truncate max-w-xs">{{ $session->comment ?? '-' }}</td>
                                    <td class="px-6 py-4 text-gray-600">{{ $session->teacher->name ?? 'Unknown' }}</td>
                                    
                                    {{-- ACTION BUTTON (MATA) - Menggunakan SVG agar PASTI MUNCUL --}}
                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ route('teacher.classes.session.detail', [$class->id, $session->id]) }}" 
                                           class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white text-gray-500 border border-gray-200 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 transition-all shadow-sm"
                                           title="View Details">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">No attendance sessions yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($classSessions->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                        {{ $classSessions->links() }}
                    </div>
                @endif
            </div>

            {{-- 5. ASSESSMENTS --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        Assessments
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 text-xs text-gray-500 font-bold uppercase border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 w-16 text-center">No</th>
                                <th class="px-6 py-4">Type</th>
                                <th class="px-6 py-4">Date</th>
                                <th class="px-6 py-4 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                            @forelse($assessments as $assessment)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 text-center text-gray-500">{{ $loop->iteration + $assessments->firstItem() - 1 }}</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            {{ $assessment->type == 'mid' ? 'bg-yellow-100 text-yellow-800 border border-yellow-200' : 'bg-purple-100 text-purple-800 border border-purple-200' }}">
                                            {{ $assessment->type == 'mid' ? 'Mid Term' : 'Final Term' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">{{ \Carbon\Carbon::parse($assessment->date)->format('d F Y') }}</td>
                                    
                                    {{-- ACTION BUTTON (PENSIL) - Menggunakan SVG agar PASTI MUNCUL --}}
                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ route('teacher.classes.assessment.detail', [$class->id, $assessment->id]) }}" 
                                           class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white text-gray-500 border border-gray-200 hover:bg-purple-50 hover:text-purple-600 hover:border-purple-200 transition-all shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">No assessments scheduled.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($assessments->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                        {{ $assessments->links() }}
                    </div>
                @endif
            </div>

        </div>

        {{-- MODAL CREATE SESSION (Tetap sama, pastikan ada di dalam div x-data) --}}
        <div x-show="openModal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
            <div x-show="openModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/75 transition-opacity backdrop-blur-sm"></div>
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div x-show="openModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100">
                        <form action="{{ route('teacher.classes.session.store', $class->id) }}" method="POST">
                            @csrf
                            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start">
                                    <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                        <h3 class="text-lg font-bold leading-6 text-gray-900" id="modal-title">Create Attendance Session</h3>
                                        <div class="mt-4 space-y-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Date (Today)</label>
                                                <input type="date" name="date" readonly class="w-full rounded-lg border-gray-300 bg-gray-50 text-gray-500 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm cursor-not-allowed" value="{{ date('Y-m-d') }}">
                                            </div>
                                            <div>
                                                <label for="topics" class="block text-sm font-medium text-gray-700 mb-1">Topic Material <span class="text-red-500">*</span></label>
                                                <textarea name="topics" id="topics" rows="3" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm placeholder-gray-400" placeholder="Example: Simple Present Tense..."></textarea>
                                            </div>
                                            <div class="rounded-lg bg-yellow-50 p-4 border border-yellow-100">
                                                <div class="flex">
                                                    <div class="flex-shrink-0">
                                                        <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    </div>
                                                    <div class="ml-3">
                                                        <h3 class="text-sm font-medium text-yellow-800">Notice</h3>
                                                        <div class="mt-1 text-sm text-yellow-700"><p>Your attendance will be marked as <strong>Present</strong> automatically.</p></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                                <button type="submit" class="inline-flex w-full justify-center rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 sm:ml-3 sm:w-auto transition-colors">Create Session</button>
                                <button type="button" @click="openModal = false" class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition-colors">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>