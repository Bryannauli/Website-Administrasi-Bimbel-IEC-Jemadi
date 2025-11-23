<x-app-layout>
    <x-slot name="header"></x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Breadcrumb --}}
            <div class="mb-6 flex items-center gap-2 text-sm font-medium text-gray-500">
                <a href="{{ route('dashboard') }}" class="hover:text-gray-900  border-gray-800 text-gray-900">Home</a>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                <a href="{{ route('admin.classes.index') }}" class="hover:text-gray-900  border-gray-800 text-gray-900">Class</a>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                <span class="text-gray-500">Class Detail</span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- KOLOM KIRI (INFO KELAS & STATS) --}}
                <div class="lg:col-span-2 space-y-6">
                    
                    {{-- Kartu Info Kelas --}}
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex justify-between items-start relative overflow-hidden">
                        <div class="absolute left-0 top-0 bottom-0 w-3 bg-red-600 rounded-l-2xl"></div>
                        
                        <div class="pl-4">
                            <h1 class="text-2xl font-bold text-gray-900">{{ $class->name }}</h1>
                            <p class="text-gray-400 text-sm mt-1">{{ $class->date }}</p>
                            
                            <div class="mt-4 flex items-center gap-2 text-gray-500 text-sm">
                                <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>{{ $class->time }}</span>
                            </div>
                        </div>

                        <div class="flex flex-col items-end gap-2">
                            <span class="px-4 py-1.5 bg-purple-100 text-purple-600 rounded-lg text-sm font-semibold">
                                {{ $class->status }}
                            </span>
                            <span class="px-4 py-1.5 bg-blue-50 text-blue-600 rounded-lg text-sm font-semibold">
                                {{ $class->level }}
                            </span>
                        </div>
                    </div>

                    {{-- Grid Kartu Kecil (Students & Teacher) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- Students Card -->
                        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                            <p class="text-sm text-gray-500 font-medium mb-1">Students</p>
                            <div class="flex justify-between items-center">
                                <h3 class="text-3xl font-bold text-gray-900">{{ $class->students_count }}</h3>
                             <a href="{{ route('admin.classes.students', $class->id) }}"  class="px-3 py-1 bg-blue-600 text-white hover:bg-blue-200 hover:text-blue-600 rounded-lg text-xs font-semibold  transition">
                                    View All
</a>
                            </div>
                        </div>

                        <!-- Teacher Card -->
                        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                            <p class="text-sm text-gray-500 font-medium mb-1">Teacher</p>
                            <div class="flex justify-between items-center">
                                <h3 class="text-3xl font-bold text-gray-900">{{ $class->teachers_count }}</h3>
                                <button class="px-3 py-1 bg-blue-600 text-white hover:bg-blue-200 hover:text-blue-600 rounded-lg text-xs font-semibold  transition">
                                    View All
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Class Progress Card --}}
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                        <h3 class="text-lg font-bold text-gray-900 mb-6">Class Progress</h3>
                        
                        <div class="flex flex-col sm:flex-row items-center gap-8">
                            <!-- Donut Chart -->
                            <div class="relative w-32 h-32">
                                <svg class="w-full h-full" viewBox="0 0 100 100">
                                    <circle class="text-gray-200" stroke-width="12" stroke="currentColor" fill="transparent" r="40" cx="50" cy="50" />
                                    <circle class="text-blue-500" stroke-width="12" stroke-linecap="round" stroke="currentColor" fill="transparent" r="40" cx="50" cy="50"
                                        stroke-dasharray="251.2"
                                        stroke-dashoffset="{{ 251.2 - (251.2 * $class->progress_percent) / 100 }}"
                                        transform="rotate(-90 50 50)" />
                                </svg>
                            </div>

                            <!-- Legend -->
                            <div>
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-2xl font-bold text-blue-600">{{ $class->progress_percent }}%</span>
                                    <span class="text-gray-600 font-medium">Sessions Completed</span>
                                </div>
                                <div class="inline-block px-3 py-1 bg-purple-100 text-purple-600 rounded-lg text-xs font-bold">
                                    {{ $class->completed_sessions }}/{{ $class->total_sessions }}
                                </div>
                                <p class="text-xs text-gray-400 mt-1">Completed Sessions</p>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- KOLOM KANAN (REPORTS) --}}
                <div class="space-y-6">
                    
                    {{-- Teacher Attendance Report --}}
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="font-bold text-gray-900 leading-tight w-2/3">Teacher <br> Attendance Report</h3>
                            <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <button class="w-full py-2 bg-blue-600 text-white hover:bg-blue-200 hover:text-blue-600 rounded-lg text-sm font-semibold transition">
                            View
                        </button>
                    </div>

                    {{-- Student Attendance Report --}}
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="font-bold text-gray-900 leading-tight w-2/3">Student <br> Attendance Report</h3>
                            <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <button class="w-full py-2 bg-blue-600 text-white hover:bg-blue-200 hover:text-blue-600 rounded-lg text-sm font-semibold  transition">
                            View
                        </button>
                    </div>

                </div>

            </div>
        </div>
    </div>
</x-app-layout>