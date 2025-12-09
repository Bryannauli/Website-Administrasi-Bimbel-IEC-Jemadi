<x-app-layout>
    <x-slot name="header">
      
    </x-slot>
  <div class="flex items-center gap-2 text-sm text-gray-600 mt-6 ml-6">
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
    <div class="p-6">
        <h2 class="text-2xl mb-6 font-bold bg-gradient-to-r from-blue-500 to-red-500 bg-clip-text text-transparent">
          Student Details
        </h2>

        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($student->name) }}&background=6366f1&color=fff&size=64"
                        alt="{{ $student->name }}" class="w-16 h-16 rounded-full">

                    <div>
                        <h2 class="text-xl font-bold text-gray-800">{{ $student->name }}</h2>
                        <p class="text-blue-600 font-medium">{{ $student->student_id }}</p>
                    </div>
                </div>
                <span class="px-4 py-2 
                    {{ $student->is_active ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700' }}
                    rounded-lg font-medium text-sm">
                    {{ $student->is_active ? 'Active' : 'Inactive' }}
                </span>
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
                                <p class="text-gray-800 font-medium">{{ $student->name }}</p>
                            </div>
                            <div class="mb-4">
                                <p class="text-sm text-gray-500 mb-1">Phone Number</p>
                                <p class="text-gray-800 font-medium">{{ $student->phone }}</p>

                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Gender</p>
                                <p class="text-gray-800 font-medium">{{ $student->gender }}</p>
                            </div>
                        </div>

                        <div>
                            <div class="mb-4">
                                <p class="text-sm text-gray-500 mb-1">Address</p>
                                <p class="text-gray-800 font-medium">{{ $student->Address }}</p>
                            </div>
                            <div class="mb-4">
                                <p class="text-sm text-gray-500 mb-1">Join Date</p>
                                <p class="text-gray-800 font-medium">{{ $student->created_at }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Class</p>
                                <span class="inline-block px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm font-medium">
                                    {{ $student->classModel->name ?? 'No Class Assigned' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-8">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-800">Attendance</h3>
                        <select class="px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 pr-8">
                            <option>This Month</option>
                            <option>Last Month</option>
                            <option>This Year</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2 mb-4 text-sm text-gray-600">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                        </svg>
                        <span>No of total working days <strong>{{ $totalDays }} Days</strong></span>
                    </div>

                    <div class="grid grid-cols-3 gap-6 mb-8">
                        <div class="text-center p-4 bg-gray-50 rounded-xl">
                            <p class="text-sm text-gray-500 mb-2">Present</p>
                            <p class="text-3xl font-bold text-blue-600">{{ $summary['present'] }}</p>
                        </div>

                        <div class="text-center p-4 bg-gray-50 rounded-xl">
                            <p class="text-sm text-gray-500 mb-2">Absent</p>
                            <p class="text-3xl font-bold text-red-500">{{ $summary['absent'] }}</p>
                        </div>

                        <div class="text-center p-4 bg-gray-50 rounded-xl">
                            <p class="text-sm text-gray-500 mb-2">Late</p>
                            <p class="text-3xl font-bold text-yellow-500">{{ $summary['late'] }}</p>
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
                                <span class="text-4xl font-bold text-gray-800">{{ $presentPercent }}%</span>
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

                    <div class="flex items-center justify-center mb-4">
                        <h4 class="font-semibold text-gray-800">Last 7 Days</h4>
                    </div>

                    <div class="flex items-center justify-center mb-4">
                        <p class="text-sm text-gray-500">{{ $rangeStart }} - {{ $rangeEnd }}</p>
                    </div>

                    <div class="flex justify-center gap-2 mt-4">

                        @foreach ($last7Days as $day)

                            @php
                                $color = match($day['status']) {
                                    'present' => 'bg-green-500 text-white',
                                    'late' => 'bg-yellow-400 text-white',
                                    'absent' => 'bg-red-500 text-white',
                                    default => 'bg-gray-300 text-white', // no data
                                };
                            @endphp

                            <div class="w-12 h-12 rounded-lg flex items-center justify-center {{ $color }}">
                                <span class="font-bold text-sm">
                                    {{ strtoupper(substr($day['day'], 0, 1)) }} <!-- M T W T F S -->
                                </span>
                            </div>

                        @endforeach

                    </div>
                    
                    </div>
                </div>
            </div>
        </div>
    </div>

   
</x-app-layout>