<!-- resources/views/teacher/teachers/index.blade.php -->
@extends('layouts.teacher')

@section('title', 'Teachers')

@section('breadcrumb')
<nav class="flex items-center space-x-2 text-sm">
    <a href="{{ route('teacher.dashboard') }}" class="text-gray-600 hover:text-gray-900">Home</a>
    <i class="fas fa-chevron-right text-xs text-gray-400"></i>
    <span class="text-gray-900 font-medium">Teachers</span>
</nav>
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold bg-gradient-to-r from-red-500 to-purple-600 bg-clip-text text-transparent">
                Teachers
            </h1>
            <p class="text-gray-600 mt-1">Manage all teachers and their information</p>
        </div>
        <button class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition flex items-center space-x-2">
            <i class="fas fa-plus"></i>
            <span>Add New Teacher</span>
        </button>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-gray-500 text-sm">Total Teachers</h3>
                <i class="fas fa-chalkboard-teacher text-blue-500 text-xl"></i>
            </div>
            <p class="text-3xl font-bold text-gray-800">24</p>
            <p class="text-green-500 text-sm mt-2">
                <i class="fas fa-arrow-up"></i> 2 new this month
            </p>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-gray-500 text-sm">Active Today</h3>
                <i class="fas fa-user-check text-green-500 text-xl"></i>
            </div>
            <p class="text-3xl font-bold text-gray-800">18</p>
            <p class="text-gray-500 text-sm mt-2">75% attendance rate</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-gray-500 text-sm">Full-Time</h3>
                <i class="fas fa-briefcase text-purple-500 text-xl"></i>
            </div>
            <p class="text-3xl font-bold text-gray-800">16</p>
            <p class="text-gray-500 text-sm mt-2">67% of total</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-gray-500 text-sm">Part-Time</h3>
                <i class="fas fa-clock text-orange-500 text-xl"></i>
            </div>
            <p class="text-3xl font-bold text-gray-800">8</p>
            <p class="text-gray-500 text-sm mt-2">33% of total</p>
        </div>
    </div>

    <!-- Filter and Search -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <div class="relative">
                    <input type="text" placeholder="Search by name, ID, or subject..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
            </div>
            <div>
                <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option>All Departments</option>
                    <option>English</option>
                    <option>Mathematics</option>
                    <option>Science</option>
                </select>
            </div>
            <div>
                <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option>All Status</option>
                    <option>Active</option>
                    <option>On Leave</option>
                    <option>Inactive</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Teachers Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @for($i = 1; $i <= 9; $i++)
        <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition overflow-hidden">
            <!-- Card Header with gradient -->
            <div class="h-24 bg-gradient-to-r from-blue-500 to-purple-600"></div>
            
            <!-- Profile Section -->
            <div class="px-6 pb-6">
                <div class="flex items-center justify-between -mt-12 mb-4">
                    <img src="https://ui-avatars.com/api/?name=Teacher{{ $i }}&background=8B5CF6&color=fff" 
                         class="w-20 h-20 rounded-full border-4 border-white shadow-lg">
                    <span class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-xs font-medium mt-12">
                        Active
                    </span>
                </div>

                <div class="mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Teacher Name {{ $i }}</h3>
                    <p class="text-sm text-gray-500">ID: TCH{{ 1000 + $i }}</p>
                </div>

                <div class="space-y-3 mb-4">
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-book w-5 text-gray-400"></i>
                        <span>English Department</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-briefcase w-5 text-gray-400"></i>
                        <span>{{ $i % 2 == 0 ? 'Full-Time' : 'Part-Time' }}</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-chalkboard w-5 text-gray-400"></i>
                        <span>{{ rand(3, 8) }} Classes</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-users w-5 text-gray-400"></i>
                        <span>{{ rand(80, 150) }} Students</span>
                    </div>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-2 gap-4 mb-4 pt-4 border-t border-gray-100">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-blue-600">{{ rand(85, 98) }}%</p>
                        <p class="text-xs text-gray-500">Attendance</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-green-600">{{ rand(75, 95) }}%</p>
                        <p class="text-xs text-gray-500">Avg Score</p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center space-x-2">
                    <a href="{{ route('teacher.teachers.show', $i) }}" 
                       class="flex-1 bg-blue-600 text-white text-center py-2 rounded-lg hover:bg-blue-700 transition text-sm">
                        View Profile
                    </a>
                    <button class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
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
            Showing <span class="font-medium">1</span> to <span class="font-medium">9</span> of <span class="font-medium">24</span> teachers
        </div>
        <div class="flex items-center space-x-2">
            <button class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50 disabled:opacity-50">
                Previous
            </button>
            <button class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">1</button>
            <button class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50">2</button>
            <button class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50">3</button>
            <button class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50">
                Next
            </button>
        </div>
    </div>

    <!-- Teacher Performance Overview -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-6">Teacher Performance Overview</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Teacher Name</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Department</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Classes</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Students</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Attendance</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Avg Score</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @for($i = 1; $i <= 5; $i++)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3">
                                <img src="https://ui-avatars.com/api/?name=Teacher{{ $i }}&background=8B5CF6&color=fff" 
                                     class="w-10 h-10 rounded-full">
                                <div>
                                    <p class="font-medium text-gray-800">Teacher Name {{ $i }}</p>
                                    <p class="text-xs text-gray-500">TCH{{ 1000 + $i }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">English</td>
                        <td class="px-6 py-4 text-sm text-gray-800">{{ rand(3, 8) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-800">{{ rand(80, 150) }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <div class="flex-1 bg-gray-200 rounded-full h-2 w-20">
                                    @php $attendance = rand(85, 98); @endphp
                                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ $attendance }}%"></div>
                                </div>
                                <span class="text-sm text-gray-600 font-medium">{{ $attendance }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-medium text-gray-800">{{ rand(75, 95) }}%</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-xs font-medium">Active</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center space-x-2">
                                <a href="{{ route('teacher.teachers.show', $i) }}" class="text-blue-600 hover:text-blue-700" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button class="text-green-600 hover:text-green-700" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="text-gray-600 hover:text-gray-700" title="More">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection