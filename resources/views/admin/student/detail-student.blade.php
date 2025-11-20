<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-600">
            <a href="{{ route('dashboard') }}" class="hover:text-gray-900">Home</a>
            
            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
            </svg>
            
            <a href="{{ route('admin.student.index') }}" class="hover:text-gray-900">Student</a>
            
            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
            </svg>
            
            <span class="text-gray-900 font-medium">Student Details</span>
        </div>
    </x-slot>

    <div class="p-6">
        <h1 class="text-3xl font-bold mb-8">
            <span class="text-red-500">Student </span>
            <span class="text-purple-600">Details</span>
        </h1>

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

        <div class="bg-white rounded-2xl shadow-lg">
            <div class="border-b border-gray-200">
                <div class="flex gap-8 px-6">
                    <button class="px-4 py-4 text-blue-600 font-semibold border-b-2 border-blue-600 focus:outline-none">
                        Details
                    </button>
                    <button class="px-4 py-4 text-gray-500 font-semibold hover:text-gray-700 focus:outline-none">
                        Assigned Classes
                    </button>
                </div>
            </div>

            <div class="p-6">
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

                <div class="border-t border-gray-100 pt-8">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-800">Attendance</h3>
                        <select class="px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                        <div class="text-center p-4 bg-gray-50 rounded-xl">
                            <p class="text-sm text-gray-500 mb-2">Present</p>
                            <p class="text-3xl font-bold text-blue-600">25</p>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-xl">
                            <p class="text-sm text-gray-500 mb-2">Absent</p>
                            <p class="text-3xl font-bold text-red-500">2</p>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-xl">
                            <p class="text-sm text-gray-500 mb-2">Holiday</p>
                            <p class="text-3xl font-bold text-gray-800">0</p>
                        </div>
                    </div>

                    <div class="flex flex-col items-center">
                        <div class="relative w-64 h-64 mb-8">
                            <svg class="transform -rotate-90 w-64 h-64">
                                <circle cx="128" cy="128" r="100" stroke="#e5e7eb" stroke-width="24" fill="none"/>
                                <circle cx="128" cy="128" r="100" stroke="#3b82f6" stroke-width="24" fill="none" 
                                        stroke-dasharray="628.32" stroke-dashoffset="31.42" stroke-linecap="round"/>
                                <circle cx="128" cy="128" r="100" stroke="#ef4444" stroke-width="24" fill="none" 
                                        stroke-dasharray="628.32" stroke-dashoffset="596.9" stroke-linecap="round"/>
                            </svg>
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <span class="text-sm text-gray-500 mb-1">Attendance</span>
                                <span class="text-4xl font-bold text-gray-800">95%</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-6 mb-8 flex-wrap justify-center">
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
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="font-semibold text-gray-800">Last 7 Days</h4>
                            <p class="text-sm text-gray-500">14 May 2024 - 21 May 2024</p>
                        </div>
                        
                        <div class="flex items-center gap-2 justify-between md:justify-start">
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
    </div>

    <div class="fixed bottom-6 left-6 z-50">
        <button class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center shadow-lg hover:bg-blue-700 transition-colors">
            <div class="relative">
                <img src="https://ui-avatars.com/api/?name=Raja&background=fff&color=3b82f6&size=40" alt="Chat" class="w-10 h-10 rounded-full">
                <span class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full border-2 border-white"></span>
            </div>
        </button>
        <div class="mt-2 text-center bg-white/80 backdrop-blur-sm rounded px-2 py-1">
            <p class="text-xs font-semibold text-gray-700">Raja</p>
            <p class="text-xs text-gray-500">Teacher</p>
        </div>
    </div>
</x-app-layout>