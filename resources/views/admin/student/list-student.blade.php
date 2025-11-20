<!-- admin/student/list-student -->
@extends('layouts.app')

@section('title', 'Student Details - AIMS')

@section('breadcrumb')
    <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900">Home</a>
    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
    </svg>
    <a href="#" class="text-gray-600 hover:text-gray-900">Student</a>
    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
    </svg>
    <span class="text-gray-900 font-medium">Student Details</span>
@endsection

@section('content')
    <h1 class="text-3xl font-bold mb-8">
        <span class="text-red-500">Student </span>
        <span class="text-purple-600">Details</span>
    </h1>

    <!-- Student Header Card -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <img src="https://ui-avatars.com/api/?name=Hanjin&background=6366f1&color=fff&size=64" alt="Hanjin" class="w-16 h-16 rounded-full">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Hanjin</h2>
                    <p class="text-blue-600 font-medium">127893683</p>
                </div>
            </div>
            <span class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg font-medium text-sm">Active</span>
        </div>
    </div>

    <!-- Tabs and Content -->
    <div class="bg-white rounded-2xl shadow-lg">
        <!-- Tabs -->
        <div class="border-b border-gray-200">
            <div class="flex gap-8 px-6">
                <button class="px-4 py-4 text-blue-600 font-semibold border-b-2 border-blue-600">
                    Details
                </button>
                <button class="px-4 py-4 text-gray-500 font-semibold hover:text-gray-700">
                    Assigned Classes
                </button>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="p-6">
            <!-- Personal Information -->
            <div class="mb-8">
                <h3 class="text-sm font-semibold text-gray-500 mb-4">Personal Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <div class="mb-4">
                            <p class="text-sm text-gray-500 mb-1">Full Name</p>
                            <p class="text-gray-800 font-medium">Lee Hanjin</p>
                        </div>
                        <div class="mb-4">
                            <p class="text-sm text-gray-500 mb-1">Email</p>
                            <p class="text-gray-800 font-medium">hnjn@gmail.com</p>
                        </div>
                        <div class="mb-4">
                            <p class="text-sm text-gray-500 mb-1">Phone Number</p>
                            <p class="text-gray-800 font-medium">08527837748</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Gender</p>
                            <p class="text-gray-800 font-medium">Man</p>
                        </div>
                    </div>

                    <div>
                        <div class="mb-4">
                            <p class="text-sm text-gray-500 mb-1">Type</p>
                            <p class="text-gray-800 font-medium">Student</p>
                        </div>
                        <div class="mb-4">
                            <p class="text-sm text-gray-500 mb-1">Address</p>
                            <p class="text-gray-800 font-medium">Jl. Gatot Subroto</p>
                        </div>
                        <div class="mb-4">
                            <p class="text-sm text-gray-500 mb-1">Join Date</p>
                            <p class="text-gray-800 font-medium">10 Januari 2023</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Class</p>
                            <span class="inline-block px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm font-medium">English1-A</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance Section -->
            <div>
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-800">Attendance</h3>
                    <select class="px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-600">
                        <option>📅 This Month</option>
                        <option>Last Month</option>
                        <option>This Year</option>
                    </select>
                </div>

                <div class="flex items-center gap-2 mb-4 text-sm text-gray-600">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                    </svg>
                    <span>No of total working days <strong>28 Days</strong></span>
                </div>

                <div class="grid grid-cols-3 gap-6 mb-8">
                    <div class="text-center">
                        <p class="text-sm text-gray-500 mb-2">Present</p>
                        <p class="text-3xl font-bold text-gray-800">25</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-500 mb-2">Absent</p>
                        <p class="text-3xl font-bold text-gray-800">2</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-500 mb-2">Holiday</p>
                        <p class="text-3xl font-bold text-gray-800">0</p>
                    </div>
                </div>

                <!-- Attendance Chart -->
                <div class="flex items-center justify-center mb-8">
                    <div class="relative w-64 h-64">
                        <svg class="transform -rotate-90 w-64 h-64">
                            <circle cx="128" cy="128" r="100" stroke="#e5e7eb" stroke-width="24" fill="none"/>
                            <!-- Present (95%) - Blue -->
                            <circle cx="128" cy="128" r="100" stroke="#3b82f6" stroke-width="24" fill="none" 
                                    stroke-dasharray="628.32" stroke-dashoffset="31.42" stroke-linecap="round"/>
                            <!-- Absent (5%) - Red -->
                            <circle cx="128" cy="128" r="100" stroke="#ef4444" stroke-width="24" fill="none" 
                                    stroke-dasharray="628.32" stroke-dashoffset="596.9" stroke-linecap="round"/>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-sm text-gray-500 mb-1">Attendance</span>
                            <span class="text-4xl font-bold text-gray-800">95%</span>
                        </div>
                    </div>
                </div>

                <!-- Legend -->
                <div class="flex items-center justify-center gap-8 mb-8">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                        <span class="text-sm text-gray-600">Present</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-red-500"></div>
                        <span class="text-sm text-gray-600">Absent</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                        <span class="text-sm text-gray-600">Late</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-gray-400"></div>
                        <span class="text-sm text-gray-600">Half Day</span>
                    </div>
                </div>

                <!-- Last 7 Days -->
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="font-semibold text-gray-800">Last 7 Days</h4>
                        <p class="text-sm text-gray-500">14 May 2024 - 21 May 2024</p>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-sm">M</span>
                        </div>
                        <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-sm">T</span>
                        </div>
                        <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-sm">W</span>
                        </div>
                        <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-sm">T</span>
                        </div>
                        <div class="w-12 h-12 bg-red-500 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-sm">F</span>
                        </div>
                        <div class="w-12 h-12 bg-gray-300 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-sm">S</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chat Widget -->
    <div class="fixed bottom-6 left-6">
        <button class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center shadow-lg hover:bg-blue-700 transition-colors">
            <div class="relative">
                <img src="https://ui-avatars.com/api/?name=Raja&background=fff&color=3b82f6&size=40" alt="Chat" class="w-10 h-10 rounded-full">
                <span class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full border-2 border-white"></span>
            </div>
        </button>
        <div class="mt-2 text-center">
            <p class="text-xs font-semibold text-gray-700">Raja</p>
            <p class="text-xs text-gray-500">bhnjn@gmail.com</p>
        </div>
    </div>
@endsection