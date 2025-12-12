<x-app-layout>
    <x-slot name="header"></x-slot>

    <div x-data="{ 
            isEditModalOpen: false, 
            isCreateSessionModalOpen: false, 
            teacherName: '', 
            currentStatus: '',
            currentCheckIn: '',
            currentCheckOut: '',

            openEditModal(name, status, inTime, outTime) {
                this.isEditModalOpen = true;
                this.teacherName = name;
                this.currentStatus = status;
                this.currentCheckIn = inTime;
                this.currentCheckOut = outTime;
            }
        }" 
        class="py-6 bg-[#F3F4FF] min-h-screen font-sans relative">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Breadcrumb --}}
            <nav class="flex mb-5 text-sm font-medium text-gray-500">
                <a href="{{ route('dashboard') }}" class="hover:text-blue-600 flex items-center gap-2">Dashboard</a>
                <span class="mx-2">/</span>
                <span class="text-gray-900">Teacher Attendance</span>
            </nav>

            {{-- Alert --}}
            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-2 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            {{-- Title & Actions --}}
            <div class="flex flex-col md:flex-row justify-between items-end md:items-center mb-8 gap-4">
                <div>
                    <h1 class="text-3xl font-bold bg-gradient-to-b from-blue-500 to-red-500 bg-clip-text text-transparent">
                        Attendance Recap
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">Based on <span class="font-bold text-gray-800">Class Levels & Steps</span></p>
                </div>

                <div class="flex gap-3">
                    {{-- TOMBOL ADD SESSION --}}
                    <button @click="isCreateSessionModalOpen = true" 
                        class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl shadow-lg shadow-blue-200 transition-all font-bold text-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        Generate from Schedule
                    </button>

                    {{-- Filter Date --}}
                    <form action="{{ route('admin.teacher.attendance') }}" method="GET" class="flex items-center gap-2 bg-white p-2 rounded-xl shadow-sm border border-gray-100">
                        <input type="date" name="date" value="{{ $date }}" class="border-none text-gray-600 text-sm font-medium focus:ring-0 rounded-lg bg-transparent cursor-pointer">
                        <button type="submit" class="p-2 bg-blue-700 text-gray-600 rounded-lg hover:bg-blue-500 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" color="white"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Stats Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white p-4 rounded-xl shadow-sm border border-blue-100">
                    <p class="text-xs font-bold text-blue-500 uppercase tracking-wide">Present</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalPresent }}</p>
                </div>
                <div class="bg-white p-4 rounded-xl shadow-sm border border-yellow-100">
                    <p class="text-xs font-bold text-yellow-600 uppercase tracking-wide">Late</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalLate }}</p>
                </div>
                <div class="bg-white p-4 rounded-xl shadow-sm border border-purple-100">
                    <p class="text-xs font-bold text-purple-600 uppercase tracking-wide">Sick/Permit</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalSick }}</p>
                </div>
                <div class="bg-white p-4 rounded-xl shadow-sm border border-red-100">
                    <p class="text-xs font-bold text-red-600 uppercase tracking-wide">Absent</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalAbsent }}</p>
                </div>
            </div>

            {{-- TABLE SECTION --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h3 class="font-bold text-gray-700">Records for {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-max">
                        <thead class="bg-white text-xs text-gray-500 font-bold uppercase border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4 w-12">No</th>
                                <th class="px-6 py-4 w-48">Class / Level</th> {{-- Kolom Class --}}
                                <th class="px-6 py-4">Teacher Name</th>      
                                <th class="px-6 py-4 text-center">Check In</th>
                                <th class="px-6 py-4 text-center">Check Out</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                            @php $startNumber = ($records->currentPage() - 1) * $records->perPage() + 1; @endphp

                            @forelse ($records as $index => $record)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 text-gray-500">{{ $startNumber + $index }}</td>
                                    
                                    {{-- KOLOM CLASS LEVEL --}}
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span class="font-bold text-blue-700 text-xs uppercase bg-blue-100 px-2 py-1 rounded w-fit border border-gray-200">
                                                {{ $record->class_name }}
                                            </span>
                                            <span class="text-xs text-gray-500 font-mono mt-1 pl-1">
                                                {{ $record->schedule_time }}
                                            </span>
                                        </div>
                                    </td>

                                    {{-- KOLOM NAMA TEACHER (Tanpa NIP) --}}
                                    <td class="px-6 py-4">
                                        <p class="font-bold text-gray-900">{{ $record->teacher->name }}</p>
                                    </td>

                                    <td class="px-6 py-4 text-center font-mono text-gray-600">{{ $record->check_in }}</td>
                                    <td class="px-6 py-4 text-center font-mono text-gray-600">{{ $record->check_out }}</td>
                                    
                                    <td class="px-6 py-4 text-center">
                                        @php
                                            $badge = match($record->status) {
                                                'present' => 'bg-green-100 text-green-700',
                                                'late' => 'bg-yellow-100 text-yellow-700',
                                                'absent' => 'bg-red-100 text-red-700',
                                                'sick' => 'bg-purple-100 text-purple-700',
                                                default => 'bg-gray-100 text-gray-500',
                                            };
                                        @endphp
                                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase {{ $badge }}">{{ $record->status }}</span>
                                    </td>

                                    <td class="px-6 py-4 text-center">
                                        <button @click="openEditModal('{{ $record->teacher->name }}', '{{ $record->status }}', '{{ $record->check_in }}', '{{ $record->check_out }}')" 
                                            class="text-blue-600 hover:bg-blue-50 p-2 rounded-lg transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="px-6 py-10 text-center text-gray-500">No scheduled classes found for this date.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 bg-white">{{ $records->links() }}</div>
            </div>

        </div>

        {{-- =========================================== --}}
        {{-- MODAL 1: GENERATE FROM SCHEDULE (OVERLAY) --}}
        {{-- =========================================== --}}
        <div x-show="isCreateSessionModalOpen" style="display: none;" 
             class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="isCreateSessionModalOpen = false"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl sm:w-full sm:max-w-md border border-gray-100">
                    
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900">Generate Attendance</h3>
                        <button @click="isCreateSessionModalOpen = false" class="text-gray-400 hover:text-gray-600"><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                    </div>

                    <form action="{{ route('admin.teacher.attendance.store') }}" method="POST">
                        @csrf
                        <div class="px-6 py-6 space-y-4">
                            
                            <div class="p-4 bg-blue-50 text-blue-800 text-sm rounded-xl border border-blue-100 flex gap-3 items-start">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div>
                                    <strong>How this works:</strong>
                                    <p class="mt-1 text-blue-700/80">
                                        The system will check the <strong>Class Levels & Steps</strong> for the selected date and create records.
                                    </p>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Select Date</label>
                                <input type="date" name="date" value="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" 
                                    class="block w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm py-3 bg-gray-50 text-gray-700">
                            </div>

                        </div>

                        <div class="bg-gray-50 px-6 py-4 sm:flex sm:flex-row-reverse border-t border-gray-100">
                            <button type="submit" class="inline-flex w-full justify-center rounded-xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-sm hover:bg-blue-700 sm:ml-3 sm:w-auto transition">
                                Generate Records
                            </button>
                            <button type="button" @click="isCreateSessionModalOpen = false" class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-5 py-3 text-sm font-bold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- MODAL 2: EDIT ABSENSI --}}
        <div x-show="isEditModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="isEditModalOpen = false"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl sm:w-full sm:max-w-lg">
                    <div class="bg-gray-50 px-4 py-4 border-b border-gray-100 flex justify-between">
                        <h3 class="font-bold text-gray-900">Update Status</h3>
                        <button @click="isEditModalOpen = false" class="text-gray-400 hover:text-gray-600"><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                    </div>
                    <form action="#" method="POST">
                        <div class="p-6 space-y-4">
                            <div class="flex items-center gap-3 p-3 bg-blue-50 rounded-lg">
                                <div class="w-10 h-10 bg-blue-200 rounded-full flex items-center justify-center text-blue-700 font-bold" x-text="teacherName.substring(0,2)"></div>
                                <div><p class="font-bold text-lg" x-text="teacherName"></p></div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Status</label>
                                <select x-model="currentStatus" class="w-full rounded-lg border-gray-300 bg-gray-50">
                                    <option value="present">Present</option>
                                    <option value="late">Late</option>
                                    <option value="absent">Absent</option>
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div><label class="block text-xs font-bold text-gray-500 mb-1">IN</label><input type="time" x-model="currentCheckIn" class="w-full rounded-lg border-gray-300"></div>
                                <div><label class="block text-xs font-bold text-gray-500 mb-1">OUT</label><input type="time" x-model="currentCheckOut" class="w-full rounded-lg border-gray-300"></div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-6 py-4 flex justify-end">
                            <button type="button" @click="isEditModalOpen = false" class="mr-3 px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 font-bold hover:bg-gray-50">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>