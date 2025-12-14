@extends('layouts.teacher')

@section('title', 'Class Details')

@section('breadcrumb')
<nav class="flex items-center space-x-2 text-sm">
    <a href="{{ route('teacher.dashboard') }}" class="text-gray-600 hover:text-gray-900">Dashboard</a>
    <i class="fas fa-chevron-right text-xs text-gray-400"></i>
    <a href="{{ route('teacher.classes.index') }}" class="text-gray-600 hover:text-gray-900">Class</a>
    <i class="fas fa-chevron-right text-xs text-gray-400"></i>
    <span class="text-gray-900 font-medium">Detail</span>
</nav>
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-8" x-data="{ openModal: false }">
    
    {{-- Info Kelas (Header) --}}
    <div class="border-l-4 border-red-500 bg-white rounded-lg p-6 shadow-sm">
        <div class="flex items-start justify-between">
            <div class="w-full">
                <div class="flex justify-between items-start">
                    <h2 class="text-3xl font-bold text-gray-800 mb-4">{{ $class->name }}</h2>
                    <span class="bg-blue-100 text-blue-600 px-4 py-1 rounded-full text-sm font-medium uppercase">
                        {{ str_replace('_', ' ', $class->category) }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
                    <div class="flex items-center space-x-3 bg-gray-50 p-3 rounded-lg">
                        <div class="bg-white p-2 rounded-md shadow-sm text-red-500">
                            <i class="fas fa-calendar-alt w-4 h-4 text-center"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Schedule Days</p>
                            <p class="font-medium text-gray-800">
                                {{ $class->schedules->count() > 0 ? $class->schedules->pluck('day_of_week')->implode(' & ') : '-' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3 bg-gray-50 p-3 rounded-lg">
                        <div class="bg-white p-2 rounded-md shadow-sm text-blue-500">
                            <i class="fas fa-clock w-4 h-4 text-center"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Time</p>
                            <p class="font-medium text-gray-800">
                                {{ \Carbon\Carbon::parse($class->start_time)->format('H:i') }} - 
                                {{ \Carbon\Carbon::parse($class->end_time)->format('H:i') }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3 bg-gray-50 p-3 rounded-lg">
                        <div class="bg-white p-2 rounded-md shadow-sm text-green-500">
                            <i class="fas fa-door-open w-4 h-4 text-center"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Room</p>
                            <p class="font-medium text-gray-800">{{ $class->classroom }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Siswa --}}
    <div class="space-y-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h3 class="text-xl font-bold text-gray-800">Student List</h3>
            <form method="GET" action="{{ url()->current() }}" class="flex items-center space-x-2">
                <span class="text-sm text-gray-600">Show</span>
                <select name="per_page" onchange="this.form.submit()" class="px-7 py-1.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5</option>
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                </select>
                <span class="text-sm text-gray-600">entries</span>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Student Name</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">ID Number</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($students as $student)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $loop->iteration + $students->firstItem() - 1 }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xs mr-3">
                                        {{ substr($student->name, 0, 2) }}
                                    </div>
                                    <div class="text-sm font-medium text-gray-900">{{ $student->name }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $student->student_number }}</td>
                            <td class="px-6 py-4">
                                @if($student->is_active)
                                    <span class="px-2 py-1 text-xs font-semibold leading-5 text-green-800 bg-green-100 rounded-full">Active</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold leading-5 text-red-800 bg-red-100 rounded-full">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">No students found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $students->links() }}
            </div>
        </div>
    </div>

    <hr class="border-gray-200">

    {{-- Tabel Absensi --}}
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-xl font-bold text-gray-800">Attendance History</h3>
            <button @click="openModal = true" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center space-x-2 text-sm shadow-sm shadow-blue-200">
                <i class="fas fa-plus"></i>
                <span>Add Session</span>
            </button>
        </div>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-16">No</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Topics</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Teacher</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        {{-- PERHATIKAN: Variabel diganti jadi $classSessions (sesuai controller) --}}
                        @forelse($classSessions as $session)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $loop->iteration + $classSessions->firstItem() - 1 }}
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                {{ \Carbon\Carbon::parse($session->date)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{-- Menampilkan Topic dari kolom comment --}}
                                {{ $session->comment ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{-- Menampilkan Guru --}}
                                {{ $session->teacher->name ?? 'Unknown' }}
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('teacher.classes.session.detail', [$class->id, $session->id]) }}" class="text-gray-500 hover:text-blue-600 transition">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">No attendance sessions recorded yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $classSessions->links() }}
            </div>
        </div>
    </div>

    <hr class="border-gray-200">

    {{-- Tabel Nilai (Assessments) --}}
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-xl font-bold text-gray-800">Assessments</h3>
        </div>
        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-16">No</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($assessments as $assessment)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $loop->iteration + $assessments->firstItem() - 1 }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                <span class="bg-{{ $assessment->type == 'mid' ? 'yellow' : 'indigo' }}-100 text-{{ $assessment->type == 'mid' ? 'yellow' : 'indigo' }}-700 px-3 py-1 rounded-full text-xs font-semibold">
                                    {{ $assessment->type == 'mid' ? 'Mid Term' : 'Final Term' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ \Carbon\Carbon::parse($assessment->date)->format('d F Y') }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('teacher.classes.assessment.detail', [$class->id, $assessment->id]) }}" class="text-gray-500 hover:text-purple-600 transition">
                                    <i class="fas fa-pen-to-square"></i>
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
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $assessments->links() }}
            </div>
        </div>
    </div>

    {{-- MODAL CREATE SESSION (UPDATED: Centered & Larger) --}}
    <div x-show="openModal" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         aria-labelledby="modal-title" role="dialog" aria-modal="true"
         style="display: none;">
         
        {{-- Container: Menggunakan 'items-center' agar posisi di tengah secara vertikal --}}
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            
            {{-- Background Overlay --}}
            <div x-show="openModal" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                 @click="openModal = false" aria-hidden="true"></div>

            {{-- Trik untuk centering pada browser lama (optional di tailwind baru, tapi aman dibiarkan) --}}
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            {{-- Modal Panel: Ubah 'sm:max-w-lg' jadi 'sm:max-w-2xl' agar lebih lebar --}}
            <div x-show="openModal" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:w-full sm:max-w-2xl"> 
                
                <form action="{{ route('teacher.classes.session.store', $class->id) }}" method="POST">
                    @csrf
                    
                    <div class="bg-white px-6 pt-6 pb-6">
                        <div class="sm:flex sm:items-start">
                            
                            {{-- Icon --}}
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-12 sm:w-12">
                                <i class="fas fa-calendar-plus text-blue-600 text-xl"></i>
                            </div>

                            {{-- Content --}}
                            <div class="mt-3 text-center sm:mt-0 sm:ml-5 sm:text-left w-full">
                                <h3 class="text-xl leading-6 font-bold text-gray-900" id="modal-title">
                                    Create Attendance Session
                                </h3>
                                <p class="text-sm text-gray-500 mt-1">
                                    Fill in the details below to create a new class session.
                                </p>
                                
                                <div class="mt-6 space-y-5">
                                    {{-- 1. Date Input (READONLY & LOCKED) --}}
                                    <div>
                                        <label for="date" class="block text-sm font-medium text-gray-700">Date (Locked)</label>
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="fas fa-lock text-gray-400"></i>
                                            </div>
                                            <input type="date" name="date" id="date" required readonly
                                                   class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md bg-gray-100 text-gray-500 cursor-not-allowed py-2.5 border"
                                                   value="{{ date('Y-m-d') }}">
                                        </div>
                                    </div>

                                    {{-- 2. Topic Material (LEBIH BESAR) --}}
                                    <div>
                                        <label for="topics" class="block text-sm font-medium text-gray-700">
                                            Topic Material <span class="text-red-500">*</span>
                                        </label>
                                        <textarea name="topics" id="topics" rows="3" required placeholder="Example: Simple Present Tense, Chapter 4 Vocabulary..."
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-3"></textarea>
                                    </div>

                                    {{-- 3. Info / Warning Box --}}
                                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-md">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-info-circle text-yellow-400 text-lg"></i>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm text-yellow-800 font-medium">
                                                    Attendance Notice
                                                </p>
                                                <p class="text-sm text-yellow-700 mt-1">
                                                    By creating this session, your attendance (Teacher) will automatically be marked as <strong>Present</strong> for today.
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Footer Buttons --}}
                    <div class="bg-gray-50 px-6 py-4 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-5 py-2.5 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Create Session
                        </button>
                        <button type="button" @click="openModal = false" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-5 py-2.5 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection