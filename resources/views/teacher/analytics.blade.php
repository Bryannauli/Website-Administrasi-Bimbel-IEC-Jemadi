<!-- resources/views/teacher/analytics.blade.php -->
@extends('layouts.teacher')

@section('title', 'Analytics')

@section('breadcrumb')
<nav class="flex items-center space-x-2 text-sm">
    <a href="{{ route('teacher.dashboard') }}" class="text-gray-600 hover:text-gray-900">Home</a>
    <i class="fas fa-chevron-right text-xs text-gray-400"></i>
    <span class="text-gray-900 font-medium">Analytics</span>
</nav>
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Analytics Dashboard</h1>
            <p class="text-gray-600 mt-1">Overview of your teaching performance and class statistics</p>
        </div>
        <div class="flex items-center space-x-3">
            <select class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option>This Month</option>
                <option>Last Month</option>
                <option>This Year</option>
            </select>
            <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-download mr-2"></i> Export Report
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Classes -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chalkboard text-blue-600 text-xl"></i>
                </div>
                <span class="text-green-500 text-sm font-medium">
                    <i class="fas fa-arrow-up"></i> 12%
                </span>
            </div>
            <h3 class="text-gray-500 text-sm mb-1">Total Classes</h3>
            <p class="text-3xl font-bold text-gray-800">24</p>
            <p class="text-gray-400 text-xs mt-2">8 active this week</p>
        </div>

        <!-- Total Students -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-graduate text-purple-600 text-xl"></i>
                </div>
                <span class="text-green-500 text-sm font-medium">
                    <i class="fas fa-arrow-up"></i> 8%
                </span>
            </div>
            <h3 class="text-gray-500 text-sm mb-1">Total Students</h3>
            <p class="text-3xl font-bold text-gray-800">342</p>
            <p class="text-gray-400 text-xs mt-2">15 new this month</p>
        </div>

        <!-- Avg Attendance -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <span class="text-green-500 text-sm font-medium">
                    <i class="fas fa-arrow-up"></i> 5%
                </span>
            </div>
            <h3 class="text-gray-500 text-sm mb-1">Avg Attendance</h3>
            <p class="text-3xl font-bold text-gray-800">92%</p>
            <p class="text-gray-400 text-xs mt-2">Up from last month</p>
        </div>

        <!-- Avg Score -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-line text-orange-600 text-xl"></i>
                </div>
                <span class="text-green-500 text-sm font-medium">
                    <i class="fas fa-arrow-up"></i> 3%
                </span>
            </div>
            <h3 class="text-gray-500 text-sm mb-1">Avg Score</h3>
            <p class="text-3xl font-bold text-gray-800">78%</p>
            <p class="text-gray-400 text-xs mt-2">Across all classes</p>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Attendance Trend -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-800">Attendance Trend</h3>
                <select class="text-sm border border-gray-300 rounded-lg px-3 py-1">
                    <option>Last 6 Months</option>
                    <option>Last 12 Months</option>
                </select>
            </div>
            <div class="relative h-64">
                <canvas id="attendanceChart"></canvas>
            </div>
        </div>

        <!-- Performance by Subject -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-800">Performance by Subject</h3>
                <select class="text-sm border border-gray-300 rounded-lg px-3 py-1">
                    <option>This Semester</option>
                    <option>Last Semester</option>
                </select>
            </div>
            <div class="relative h-64">
                <canvas id="performanceChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Class Performance Table -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-800">Class Performance Overview</h3>
            <button class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                View All <i class="fas fa-arrow-right ml-1"></i>
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Class Name</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Total Students</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Avg Attendance</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Avg Score</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Performance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-800">English A - Vocabulary</div>
                            <div class="text-sm text-gray-500">E-101 • Mon & Tue</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-800">32</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <div class="flex-1 bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: 95%"></div>
                                </div>
                                <span class="text-sm text-gray-600 font-medium">95%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-800">82%</td>
                        <td class="px-6 py-4">
                            <span class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-xs font-medium">Excellent</span>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-800">English B - Speaking</div>
                            <div class="text-sm text-gray-500">E-102 • Wednesday</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-800">28</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <div class="flex-1 bg-gray-200 rounded-full h-2">
                                    <div class="bg-yellow-500 h-2 rounded-full" style="width: 88%"></div>
                                </div>
                                <span class="text-sm text-gray-600 font-medium">88%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-800">76%</td>
                        <td class="px-6 py-4">
                            <span class="bg-blue-100 text-blue-600 px-3 py-1 rounded-full text-xs font-medium">Good</span>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-800">English C - Grammar</div>
                            <div class="text-sm text-gray-500">E-103 • Thursday</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-800">30</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <div class="flex-1 bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: 92%"></div>
                                </div>
                                <span class="text-sm text-gray-600 font-medium">92%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-800">79%</td>
                        <td class="px-6 py-4">
                            <span class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-xs font-medium">Excellent</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Activity Log -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-6">Recent Activities</h3>
        <div class="space-y-4">
            <div class="flex items-start space-x-4 pb-4 border-b border-gray-100">
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-user-check text-blue-600"></i>
                </div>
                <div class="flex-1">
                    <p class="text-gray-800 font-medium">Attendance submitted for English A - Session 12</p>
                    <p class="text-sm text-gray-500">2 hours ago</p>
                </div>
            </div>
            <div class="flex items-start space-x-4 pb-4 border-b border-gray-100">
                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-file-alt text-green-600"></i>
                </div>
                <div class="flex-1">
                    <p class="text-gray-800 font-medium">New assessment scores added for 28 students</p>
                    <p class="text-sm text-gray-500">5 hours ago</p>
                </div>
            </div>
            <div class="flex items-start space-x-4">
                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-calendar-plus text-purple-600"></i>
                </div>
                <div class="flex-1">
                    <p class="text-gray-800 font-medium">New session created for Speaking class</p>
                    <p class="text-sm text-gray-500">1 day ago</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Attendance Trend Chart
const attendanceCtx = document.getElementById('attendanceChart');
new Chart(attendanceCtx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
            label: 'Attendance Rate',
            data: [87, 90, 88, 92, 91, 92],
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
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
                max: 100,
                ticks: {
                    callback: function(value) {
                        return value + '%';
                    }
                }
            }
        }
    }
});

// Performance by Subject Chart
const performanceCtx = document.getElementById('performanceChart');
new Chart(performanceCtx, {
    type: 'bar',
    data: {
        labels: ['Vocabulary', 'Grammar', 'Speaking', 'Listening', 'Writing'],
        datasets: [{
            label: 'Average Score',
            data: [82, 79, 76, 81, 78],
            backgroundColor: [
                'rgba(59, 130, 246, 0.8)',
                'rgba(139, 92, 246, 0.8)',
                'rgba(16, 185, 129, 0.8)',
                'rgba(251, 146, 60, 0.8)',
                'rgba(239, 68, 68, 0.8)'
            ],
            borderRadius: 8
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
                max: 100,
                ticks: {
                    callback: function(value) {
                        return value + '%';
                    }
                }
            }
        }
    }
});
</script>
@endpush