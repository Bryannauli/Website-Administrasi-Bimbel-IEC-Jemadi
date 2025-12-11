<!-- resources/views/teacher/students/marks.blade.php -->
@extends('layouts.teacher')

@section('title', 'Students Marks')

@section('breadcrumb')
<nav class="flex items-center space-x-2 text-sm">
    <a href="{{ route('teacher.dashboard') }}" class="text-gray-600 hover:text-gray-900">Home</a>
    <i class="fas fa-chevron-right text-xs text-gray-400"></i>
    <a href="{{ route('teacher.students.index') }}" class="text-gray-600 hover:text-gray-900">Students</a>
    <i class="fas fa-chevron-right text-xs text-gray-400"></i>
    <span class="text-gray-900 font-medium">Students Marks</span>
</nav>
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold bg-gradient-to-r from-red-500 to-purple-600 bg-clip-text text-transparent">
                Students Marks
            </h1>
            <p class="text-gray-600 mt-1">Manage and view all student assessment scores</p>
        </div>
        <button class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition flex items-center space-x-2">
            <i class="fas fa-plus"></i>
            <span>Add New Assessment</span>
        </button>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Class</label>
                <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option>All Classes</option>
                    <option>English A - Vocabulary</option>
                    <option>English B - Speaking</option>
                    <option>English C - Grammar</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Assessment Type</label>
                <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option>All Types</option>
                    <option>Quiz</option>
                    <option>Mid Test</option>
                    <option>Final Test</option>
                    <option>Assignment</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option>This Month</option>
                    <option>Last Month</option>
                    <option>This Semester</option>
                    <option>Custom</option>
                </select>
            </div>
            <div class="flex items-end">
                <button class="w-full bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition">
                    <i class="fas fa-filter mr-2"></i> Apply Filters
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-gray-500 text-sm">Total Students</h3>
                <i class="fas fa-users text-blue-500 text-xl"></i>
            </div>
            <p class="text-3xl font-bold text-gray-800">342</p>
            <p class="text-green-500 text-sm mt-2">
                <i class="fas fa-arrow-up"></i> 15 new this month
            </p>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-gray-500 text-sm">Avg Score</h3>
                <i class="fas fa-chart-line text-green-500 text-xl"></i>
            </div>
            <p class="text-3xl font-bold text-gray-800">78.5%</p>
            <p class="text-green-500 text-sm mt-2">
                <i class="fas fa-arrow-up"></i> 3% increase
            </p>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-gray-500 text-sm">Pass Rate</h3>
                <i class="fas fa-check-circle text-purple-500 text-xl"></i>
            </div>
            <p class="text-3xl font-bold text-gray-800">89%</p>
            <p class="text-green-500 text-sm mt-2">
                <i class="fas fa-arrow-up"></i> 2% increase
            </p>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-gray-500 text-sm">Pending Reviews</h3>
                <i class="fas fa-clock text-orange-500 text-xl"></i>
            </div>
            <p class="text-3xl font-bold text-gray-800">12</p>
            <p class="text-orange-500 text-sm mt-2">
                <i class="fas fa-exclamation-circle"></i> Need attention
            </p>
        </div>
    </div>

    <!-- Student Marks Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="p-6 flex items-center justify-between border-b border-gray-200">
            <div class="relative">
                <input type="text" placeholder="Search student name or ID..." 
                       class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg w-80 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
            </div>
            <div class="flex items-center space-x-3">
                <button class="border border-gray-300 text-gray-600 px-4 py-2 rounded-lg hover:bg-gray-50 transition">
                    <i class="fas fa-download mr-2"></i> Export
                </button>
                <button class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-file-import mr-2"></i> Import
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">
                            <input type="checkbox" class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Student ID</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Student Name</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Class</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Vocabulary</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Grammar</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Listening</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Speaking</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Average</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @for($i = 1; $i <= 10; $i++)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <input type="checkbox" class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-800">12789{{ 3680 + $i }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3">
                                <img src="https://ui-avatars.com/api/?name=Student{{ $i }}&background=8B5CF6&color=fff" class="w-8 h-8 rounded-full">
                                <span class="font-medium text-gray-800">Student {{ $i }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="bg-purple-100 text-purple-600 px-2 py-1 rounded text-xs font-medium">English-A</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-800 font-medium">{{ 70 + rand(0, 25) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-800 font-medium">{{ 75 + rand(0, 20) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-800 font-medium">{{ 72 + rand(0, 23) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-800 font-medium">{{ 78 + rand(0, 17) }}</td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-bold text-gray-800">{{ 74 + rand(0, 20) }}%</span>
                        </td>
                        <td class="px-6 py-4">
                            @php $status = rand(0, 2); @endphp
                            @if($status == 0)
                                <span class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-xs font-medium">Pass</span>
                            @elseif($status == 1)
                                <span class="bg-yellow-100 text-yellow-600 px-3 py-1 rounded-full text-xs font-medium">Review</span>
                            @else
                                <span class="bg-red-100 text-red-600 px-3 py-1 rounded-full text-xs font-medium">Fail</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center space-x-2">
                                <a href="{{ route('teacher.students.show', $i) }}" class="text-blue-600 hover:text-blue-700" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button class="text-green-600 hover:text-green-700" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="text-red-600 hover:text-red-700" title="Delete">
                                    <i class="fas fa-trash"></i>
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
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-600">Show</span>
                <select class="border border-gray-300 rounded px-2 py-1 text-sm">
                    <option>10</option>
                    <option>25</option>
                    <option>50</option>
                    <option>100</option>
                </select>
                <span class="text-sm text-gray-600">entries</span>
            </div>
            <div class="flex items-center space-x-2">
                <button class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50 disabled:opacity-50">
                    Previous
                </button>
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">1</button>
                <button class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50">2</button>
                <button class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50">3</button>
                <span class="px-2">...</span>
                <button class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50">10</button>
                <button class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50">
                    Next
                </button>
            </div>
        </div>
    </div>

    <!-- Grade Distribution Chart -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-6">Grade Distribution</h3>
            <div class="relative h-64">
                <canvas id="gradeChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-6">Top Performers</h3>
            <div class="space-y-4">
                @for($i = 1; $i <= 5; $i++)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-full flex items-center justify-center text-white font-bold text-sm">
                            {{ $i }}
                        </div>
                        <img src="https://ui-avatars.com/api/?name=Top{{ $i }}&background=8B5CF6&color=fff" class="w-10 h-10 rounded-full">
                        <div>
                            <p class="font-medium text-gray-800">Top Student {{ $i }}</p>
                            <p class="text-xs text-gray-500">ID: 12789{{ 3680 + $i }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-bold text-gray-800">{{ 95 - ($i * 2) }}%</p>
                        <p class="text-xs text-gray-500">Avg Score</p>
                    </div>
                </div>
                @endfor
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Grade Distribution Chart
const gradeCtx = document.getElementById('gradeChart');
new Chart(gradeCtx, {
    type: 'doughnut',
    data: {
        labels: ['A (90-100)', 'B (80-89)', 'C (70-79)', 'D (60-69)', 'F (<60)'],
        datasets: [{
            data: [45, 78, 112, 67, 40],
            backgroundColor: [
                'rgba(16, 185, 129, 0.8)',
                'rgba(59, 130, 246, 0.8)',
                'rgba(251, 191, 36, 0.8)',
                'rgba(251, 146, 60, 0.8)',
                'rgba(239, 68, 68, 0.8)'
            ],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>
@endpush