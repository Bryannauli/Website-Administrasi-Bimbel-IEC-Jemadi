<!-- resources/views/teacher/students/detail.blade.php -->
@extends('layouts.teacher')

@section('title', 'Student Details')

@section('breadcrumb')
<nav class="flex items-center space-x-2 text-sm">
    <a href="{{ route('teacher.dashboard') }}" class="text-gray-600 hover:text-gray-900">Home</a>
    <i class="fas fa-chevron-right text-xs text-gray-400"></i>
    <a href="{{ route('teacher.students.index') }}" class="text-gray-600 hover:text-gray-900">Student</a>
    <i class="fas fa-chevron-right text-xs text-gray-400"></i>
    <span class="text-gray-900 font-medium">Student Details</span>
</nav>
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Student Profile Card -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-start justify-between">
            <div class="flex items-center space-x-4">
                <img src="https://ui-avatars.com/api/?name=Hanjin&background=8B5CF6&color=fff" class="w-20 h-20 rounded-full">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Hanjin</h2>
                    <p class="text-blue-600 font-medium">127893683</p>
                </div>
            </div>
            <span class="bg-blue-100 text-blue-600 px-4 py-1 rounded-full text-sm font-medium">Active</span>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-xl shadow-sm">
        <div class="border-b border-gray-200">
            <nav class="flex space-x-8 px-6">
                <button class="py-4 border-b-2 border-blue-600 text-blue-600 font-medium">Details</button>
                <button class="py-4 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">Assigned Classes</button>
            </nav>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Personal Information -->
                <div class="lg:col-span-2">
                    <h3 class="text-sm text-gray-500 mb-4">Personal Information</h3>
                    
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <p class="text-gray-800">Lee Hanjin</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                            <p class="text-gray-800">Student</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <p class="text-gray-800">hnjn@gmail.com</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <p class="text-gray-800">Jl. Gatot Subroto</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                            <p class="text-gray-800">08527837748</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Join Date</label>
                            <p class="text-gray-800">10 Januari 2023</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                            <p class="text-gray-800">Man</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Class</label>
                            <span class="bg-purple-100 text-purple-600 px-3 py-1 rounded-full text-sm font-medium">English-A</span>
                        </div>
                    </div>
                </div>

                <!-- Attendance Card -->
                <div>
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center justify-between">
                        <span>Attendance</span>
                        <select class="text-sm border border-gray-300 rounded-lg px-3 py-1">
                            <option>This Month</option>
                            <option>Last Month</option>
                        </select>
                    </h3>
                    
                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                        <div class="flex items-center space-x-2 text-sm text-gray-600 mb-2">
                            <i class="fas fa-calendar"></i>
                            <span>No of total working days: <strong>28 Days</strong></span>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4 text-center mb-6">
                        <div>
                            <p class="text-gray-500 text-xs mb-1">Present</p>
                            <p class="text-2xl font-bold text-gray-800">25</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs mb-1">Absent</p>
                            <p class="text-2xl font-bold text-gray-800">2</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs mb-1">Holiday</p>
                            <p class="text-2xl font-bold text-gray-800">0</p>
                        </div>
                    </div>

                    <!-- Circular Progress -->
                    <div class="flex justify-center mb-6">
                        <div class="relative w-48 h-48">
                            <svg class="transform -rotate-90 w-48 h-48">
                                <circle cx="96" cy="96" r="80" stroke="#e5e7eb" stroke-width="16" fill="none"></circle>
                                <circle cx="96" cy="96" r="80" stroke="#3b82f6" stroke-width="16" fill="none" stroke-dasharray="502.65" stroke-dashoffset="50.265" stroke-linecap="round"></circle>
                                <circle cx="96" cy="96" r="80" stroke="#ef4444" stroke-width="16" fill="none" stroke-dasharray="502.65" stroke-dashoffset="452.385" stroke-linecap="round"></circle>
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center flex-col">
                                <p class="text-xs text-gray-500">Attendance</p>
                                <p class="text-3xl font-bold text-gray-800">95%</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-center space-x-4 text-xs mb-6">
                        <div class="flex items-center space-x-1">
                            <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                            <span class="text-gray-600">Present</span>
                        </div>
                        <div class="flex items-center space-x-1">
                            <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                            <span class="text-gray-600">Absent</span>
                        </div>
                        <div class="flex items-center space-x-1">
                            <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                            <span class="text-gray-600">Late</span>
                        </div>
                        <div class="flex items-center space-x-1">
                            <div class="w-3 h-3 bg-gray-300 rounded-full"></div>
                            <span class="text-gray-600">Half Day</span>
                        </div>
                    </div>

                    <!-- Last 7 Days -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-sm font-medium text-gray-700">Last 7 Days</h4>
                            <p class="text-xs text-gray-500">14 May 2024 - 21 May 2024</p>
                        </div>
                        <div class="flex space-x-1">
                            <div class="w-8 h-8 bg-green-500 rounded flex items-center justify-center text-white text-xs font-medium">M</div>
                            <div class="w-8 h-8 bg-green-500 rounded flex items-center justify-center text-white text-xs font-medium">T</div>
                            <div class="w-8 h-8 bg-green-500 rounded flex items-center justify-center text-white text-xs font-medium">W</div>
                            <div class="w-8 h-8 bg-green-500 rounded flex items-center justify-center text-white text-xs font-medium">T</div>
                            <div class="w-8 h-8 bg-red-500 rounded flex items-center justify-center text-white text-xs font-medium">F</div>
                            <div class="w-8 h-8 bg-gray-300 rounded flex items-center justify-center text-white text-xs font-medium">S</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Chart -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-800">Performance</h3>
            <select class="border border-gray-300 rounded-lg px-4 py-2 text-sm">
                <option>2024 - 2025</option>
                <option>2023 - 2024</option>
            </select>
        </div>

        <!-- Chart Area -->
        <div class="relative h-64 mb-4">
            <canvas id="performanceChart"></canvas>
        </div>

        <div class="flex items-center justify-center space-x-6 text-sm">
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                <span class="text-gray-600">Avg Score: <strong>72%</strong></span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                <span class="text-gray-600">Avg. Attendance: <strong>95%</strong></span>
            </div>
        </div>
    </div>

    <!-- Assessment Scores Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="p-6 flex items-center justify-between border-b border-gray-200">
            <div class="flex items-center space-x-4">
                <button class="flex items-center space-x-2 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    <i class="fas fa-filter"></i>
                    <span>Filters</span>
                </button>
            </div>
            <button class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition flex items-center space-x-2">
                <i class="fas fa-plus"></i>
                <span>Add New Assessment Score</span>
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">No</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Vocabulary</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Grammar</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Listening</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Speaking</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @for($i = 1; $i <= 2; $i++)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm text-gray-800">{{ $i }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">2025-11-16</td>
                        <td class="px-6 py-4 text-sm text-gray-800">80</td>
                        <td class="px-6 py-4 text-sm text-gray-800">90</td>
                        <td class="px-6 py-4 text-sm text-gray-800">85</td>
                        <td class="px-6 py-4 text-sm text-gray-800">90</td>
                        <td class="px-6 py-4">
                            <span class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-xs font-medium">Pass</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center space-x-3">
                                <button class="text-gray-600 hover:text-blue-600">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="text-gray-600 hover:text-red-600">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <button class="text-gray-600 hover:text-green-600">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
            <button class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50">
                Previous
            </button>
            <span class="text-sm text-gray-600">Page 1 of 10</span>
            <button class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50">
                Next
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('performanceChart');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Quarter 1', 'Quarter 2', 'Half yearly', 'Model', 'Final Exam'],
        datasets: [{
            label: 'Exam Score',
            data: [80, 75, 60, 70, 85],
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4,
            fill: true
        }, {
            label: 'Attendance',
            data: [95, 90, 85, 92, 95],
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                max: 100
            }
        }
    }
});
</script>
@endpush