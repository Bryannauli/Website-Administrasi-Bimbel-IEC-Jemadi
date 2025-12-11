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
<div class="max-w-7xl mx-auto space-y-8">
    
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
                                @if($class->schedules->count() > 0)
                                    {{ $class->schedules->pluck('day_of_week')->implode(' & ') }}
                                @else
                                    -
                                @endif
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

    <div class="space-y-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h3 class="text-xl font-bold text-gray-800">Student List</h3>
            
            <form method="GET" action="{{ url()->current() }}" class="flex items-center space-x-2">
                <span class="text-sm text-gray-600">Show</span>
                <select name="per_page" onchange="this.form.submit()" class="px-3 py-1.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
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
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($students as $student)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $loop->iteration + $students->firstItem() - 1 }}
                            </td>
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
                            <td class="px-6 py-4 text-right">
                                <button class="text-gray-400 hover:text-blue-600 transition">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">No students found in this class.</td>
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
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Time</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($attendanceSessions as $session)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $loop->iteration + $attendanceSessions->firstItem() - 1 }}
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                {{ \Carbon\Carbon::parse($session->date)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ \Carbon\Carbon::parse($class->start_time)->format('H:i') }} - 
                                {{ \Carbon\Carbon::parse($class->end_time)->format('H:i') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <a href="{{ route('teacher.classes.session.detail', [$class->id, $session->id]) }}" class="text-gray-500 hover:text-blue-600 transition" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                No attendance sessions recorded yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-200">
                {{ $attendanceSessions->links() }}
            </div>
        </div>
    </div>
</div>

<div x-data="{ openModal: false }" @keydown.escape="openModal = false">
    <div x-show="openModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black opacity-50" @click="openModal = false"></div>
            
            <div class="relative bg-white rounded-xl shadow-xl max-w-lg w-full p-6 animate-fade-in-down">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800">Add Attendance Session</h3>
                    <button @click="openModal = false" class="text-gray-400 hover:text-gray-600 transition">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <form action="{{ route('teacher.classes.session.store', $class->id) }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                            <input type="date" name="date" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ date('Y-m-d') }}">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Start Time</label>
                                <input type="time" name="start_time" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ \Carbon\Carbon::parse($class->start_time)->format('H:i') }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">End Time</label>
                                <input type="time" name="end_time" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ \Carbon\Carbon::parse($class->end_time)->format('H:i') }}">
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 italic">*Times are preset based on class schedule but can be adjusted.</p>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" @click="openModal = false" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                            Cancel
                        </button>
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition shadow-lg shadow-blue-200">
                            Create Session
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection