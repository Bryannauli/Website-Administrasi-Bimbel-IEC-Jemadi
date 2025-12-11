<!-- resources/views/teacher/students/index.blade.php -->
@extends('layouts.teacher')

@section('title', 'Students')

@section('breadcrumb')
<nav class="flex items-center space-x-2 text-sm">
    <a href="{{ route('teacher.dashboard') }}" class="text-gray-600 hover:text-gray-900">Home</a>
    <i class="fas fa-chevron-right text-xs text-gray-400"></i>
    <span class="text-gray-900 font-medium">Students</span>
</nav>
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold bg-gradient-to-r from-red-500 to-purple-600 bg-clip-text text-transparent">
                Students
            </h1>
            <p class="text-gray-600 mt-1">Manage all students and their information</p>
        </div>
        <div class="flex items-center space-x-3">
            <button class="border border-gray-300 text-gray-600 px-4 py-2 rounded-lg hover:bg-gray-50 transition">
                <i class="fas fa-download mr-2"></i> Export
            </button>
            <button class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition flex items-center space-x-2">
                <i class="fas fa-plus"></i>
                <span>Add New Student</span>
            </button>
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
                <h3 class="text-gray-500 text-sm">Active Students</h3>
                <i class="fas fa-user-check text-green-500 text-xl"></i>
            </div>
            <p class="text-3xl font-bold text-gray-800">318</p>
            <p class="text-gray-500 text-sm mt-2">93% of total</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-gray-500 text-sm">Avg Attendance</h3>
                <i class="fas fa-clipboard-check text-purple-500 text-xl"></i>
            </div>
            <p class="text-3xl font-bold text-gray-800">92%</p>
            <p class="text-green-500 text-sm mt-2">
                <i class="fas fa-arrow-up"></i> 5% increase
            </p>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-gray-500 text-sm">Pass Rate</h3>
                <i class="fas fa-trophy text-orange-500 text-xl"></i>
            </div>
            <p class="text-3xl font-bold text-gray-800">89%</p>
            <p class="text-green-500 text-sm mt-2">
                <i class="fas fa-arrow-up"></i> 2% increase
            </p>
        </div>
    </div>

    <!-- Filter and Search -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="md:col-span-2">
                <div class="relative">
                    <input type="text" placeholder="Search by name, ID, or email..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
            </div>
            <div>
                <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option>All Classes</option>
                    <option>English A</option>
                    <option>English B</option>
                    <option>English C</option>
                </select>
            </div>
            <div>
                <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option>All Status</option>
                    <option>Active</option>
                    <option>Inactive</option>
                    <option>Graduated</option>
                </select>
            </div>
            <div>
                <button class="w-full bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition">
                    <i class="fas fa-filter mr-2"></i> Apply
                </button>
            </div>
        </div>
    </div>

    <!-- View Toggle -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-2 bg-white rounded-lg p-1 shadow-sm">
            <button class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm" id="gridView">
                <i class="fas fa-th"></i> Grid
            </button>
            <button class="px-4 py-2 text-gray-600 rounded-lg text-sm hover:bg-gray-100" id="listView">
                <i class="fas fa-list"></i> List
            </button>
        </div>
        <div class="text-sm text-gray-600">
            Showing <span class="font-medium">1-12</span> of <span class="font-medium">342</span> students
        </div>
    </div>

    <!-- Students Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" id="studentsGrid">
        @for($i = 1; $i <= 12; $i++)
        <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <img src="https://ui-avatars.com/api/?name=Student{{ $i }}&background=8B5CF6&color=fff" 
                         class="w-16 h-16 rounded-full">
                    <span class="bg-green-100 text-green-600 px-2 py-1 rounded-full text-xs font-medium">
                        Active
                    </span>
                </div>

                <div class="mb-4">
                    <h3 class="text-lg font-bold text-gray-800">Student Name {{ $i }}</h3>
                    <p class="text-sm text-gray-500">ID: 12789{{ 3680 + $i }}</p>
                </div>

                <div class="space-y-2 mb-4">
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-envelope w-5 text-gray-400"></i>
                        <span class="truncate">student{{ $i }}@email.com</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-book w-5 text-gray-400"></i>
                        <span class="bg-purple-100 text-purple-600 px-2 py-0.5 rounded text-xs">English-A</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-calendar w-5 text-gray-400"></i>
                        <span>Joined: Jan 2023</span>
                    </div>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-2 gap-3 mb-4 pt-4 border-t border-gray-100">
                    <div class="text-center">
                        <p class="text-xl font-bold text-blue-600">{{ rand(85, 98) }}%</p>
                        <p class="text-xs text-gray-500">Attendance</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xl font-bold text-green-600">{{ rand(70, 95) }}%</p>
                        <p class="text-xs text-gray-500">Avg Score</p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center space-x-2">
                    <a href="{{ route('teacher.students.show', $i) }}" 
                       class="flex-1 bg-blue-600 text-white text-center py-2 rounded-lg hover:bg-blue-700 transition text-sm">
                        View Profile
                    </a>
                    <button class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        <i class="fas fa-ellipsis-v text-gray-600"></i>
                    </button>
                </div>
            </div>
        </div>
        @endfor
    </div>

    <!-- Pagination -->
    <div class="bg-white rounded-xl shadow-sm p-4 flex items-center justify-between">
        <div class="text-sm text-gray-600">
            Showing <span class="font-medium">1</span> to <span class="font-medium">12</span> of <span class="font-medium">342</span> students
        </div>
        <div class="flex items-center space-x-2">
            <button class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50 disabled:opacity-50">
                Previous
            </button>
            <button class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">1</button>
            <button class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50">2</button>
            <button class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50">3</button>
            <span class="px-2">...</span>
            <button class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50">29</button>
            <button class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50">
                Next
            </button>
        </div>
    </div>

    <!-- Class Distribution Chart -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-6">Students by Class</h3>
            <div class="relative h-64">
                <canvas id="classDistributionChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-6">Recent Enrollments</h3>
            <div class="space-y-4">
                @for($i = 1; $i <= 5; $i++)
                <div class="flex items-center space-x-4 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <img src="https://ui-avatars.com/api/?name=NewStudent{{ $i }}&background=8B5CF6&color=fff" 
                         class="w-12 h-12 rounded-full">
                    <div class="flex-1">
                        <p class="font-medium text-gray-800">New Student {{ $i }}</p>
                        <p class="text-sm text-gray-500">Enrolled in English A</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500">{{ $i }} day{{ $i > 1 ? 's' : '' }} ago</p>
                        <span class="bg-green-100 text-green-600 px-2 py-1 rounded text-xs font-medium">New</span>
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
// Class Distribution Chart
const classCtx = document.getElementById('classDistributionChart');
new Chart(classCtx, {
    type: 'pie',
    data: {
        labels: ['English A', 'English B', 'English C', 'English D', 'Others'],
        datasets: [{
            data: [120, 95, 78, 35, 14],
            backgroundColor: [
                'rgba(59, 130, 246, 0.8)',
                'rgba(139, 92, 246, 0.8)',
                'rgba(16, 185, 129, 0.8)',
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

// View Toggle
document.getElementById('gridView').addEventListener('click', function() {
    document.getElementById('studentsGrid').className = 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6';
    this.classList.add('bg-blue-600', 'text-white');
    this.classList.remove('text-gray-600');
    document.getElementById('listView').classList.remove('bg-blue-600', 'text-white');
    document.getElementById('listView').classList.add('text-gray-600');
});

document.getElementById('listView').addEventListener('click', function() {
    document.getElementById('studentsGrid').className = 'grid grid-cols-1 gap-4';
    this.classList.add('bg-blue-600', 'text-white');
    this.classList.remove('text-gray-600');
    document.getElementById('gridView').classList.remove('bg-blue-600', 'text-white');
    document.getElementById('gridView').classList.add('text-gray-600');
});
</script>
@endpush