<x-app-layout>
    {{-- Slot header dari Breeze tidak digunakan di desain ini, jadi kita kosongkan --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight hidden ">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6 ml-64">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Ini adalah awal dari konten dashboard kustom Anda --}}
            <div class="space-y-6">

                <div class="text-sm text-gray-500">> Dashboard
                </div>

                <div class="w-full bg-white p-6 rounded-xl shadow-sm flex justify-between items-center">
                    <div class="text-gray-800">
                        {{-- Ambil nama pengguna yang sedang login --}}
                        <h2 class="text-2xl font-bold text-pink-600">Welcome, {{ Auth::user()->name }}!</h2>
                        <p class="mt-2 text-gray-700 max-w-md">
                            Manage your school operations with ease. Stay updated on academics, attendance, finances, and more—all in one place.
                        </p>
                    </div>
                    <div class="hidden sm:block">
                        {{-- Placeholder untuk ilustrasi --}}
                        <img src="{{asset('images/dashboard.png') }}" alt="Dashboard Illustration" class="rounded-lg object-cover">
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-700">Students</h3>
                            <button class="text-gray-400 hover:text-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                  <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                </svg>
                            </button>
                        </div>
                        
                        <div class="flex flex-col sm:flex-row justify-around items-center space-y-6 sm:space-y-0 sm:space-x-6 mt-4">
                            <div class="flex flex-col items-center">
                                <div class="relative w-36 h-36">
                                    {{-- Simulasi Donut Chart dengan SVG --}}
                                    <svg class="w-full h-full" viewBox="0 0 100 100">
                                        <circle class="text-gray-200" stroke-width="10" stroke="currentColor" fill="transparent" r="40" cx="50" cy="50" />
                                        <circle class="text-blue-500" stroke-width="10" stroke-linecap="round" stroke="currentColor" fill="transparent" r="40" cx="50" cy="50"
                                            stroke-dasharray="251.2"
                                            stroke-dashoffset="calc(251.2 - (251.2 * 53) / 100)"
                                            transform="rotate(-90 50 50)" />
                                    </svg>
                                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                                        <svg class="w-8 h-8 text-blue-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                          <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                        </svg>
                                        <span class="text-2xl font-bold text-gray-900">53%</span>
                                    </div>
                                </div>
                                <span class="mt-2 text-sm text-gray-500">≈ 3,178 (boys)</span>
                            </div>
                            
                            <div class="flex flex-col items-center">
                                <div class="relative w-36 h-36">
                                    <svg class="w-full h-full" viewBox="0 0 100 100">
                                        <circle class="text-gray-200" stroke-width="10" stroke="currentColor" fill="transparent" r="40" cx="50" cy="50" />
                                        <circle class="text-pink-500" stroke-width="10" stroke-linecap="round" stroke="currentColor" fill="transparent" r="40" cx="50" cy="50"
                                            stroke-dasharray="251.2"
                                            stroke-dashoffset="calc(251.2 - (251.2 * 47) / 100)"
                                            transform="rotate(-90 50 50)" />
                                    </svg>
                                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                                        <svg class="w-8 h-8 text-pink-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                          <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                        </svg>
                                        <span class="text-2xl font-bold text-gray-900">47%</span>
                                    </div>
                                </div>
                                <span class="mt-2 text-sm text-gray-500">≈ 2,731 (Girls)</span>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-1 grid grid-cols-2 gap-6">
                        <div class="bg-yellow-100 p-4 rounded-xl shadow-sm flex flex-col justify-between">
                            <div class="flex justify-between items-center">
                                <h4 class="font-semibold text-yellow-800">Students</h4>
                                <button class="text-yellow-600 hover:text-yellow-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" /></svg>
                                </button>
                            </div>
                            <p class="text-3xl font-bold text-yellow-900 mt-2">5,909</p>
                            <button class="mt-4 text-left text-yellow-700 text-sm font-medium">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                            </button>
                        </div>
                        
                        <div class="bg-purple-100 p-4 rounded-xl shadow-sm flex flex-col justify-between">
                            <div class="flex justify-between items-center">
                                <h4 class="font-semibold text-purple-800">Teachers</h4>
                                <button class="text-purple-600 hover:text-purple-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" /></svg>
                                </button>
                            </div>
                            <p class="text-3xl font-bold text-purple-900 mt-2">60</p>
                            <button class="mt-4 text-left text-purple-700 text-sm font-medium">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                            </button>
                        </div>
                        
                        <div class="bg-green-100 p-4 rounded-xl shadow-sm flex flex-col justify-between">
                            <div class="flex justify-between items-center">
                                <h4 class="font-semibold text-green-800">Employee</h4>
                                <button class="text-green-600 hover:text-green-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" /></svg>
                                </button>
                            </div>
                            <p class="text-3xl font-bold text-green-900 mt-2">100</p>
                            <button class="mt-4 text-left text-green-700 text-sm font-medium">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                            </button>
                        </div>
                        
                        <div class="bg-red-100 p-4 rounded-xl shadow-sm flex flex-col justify-between">
                            <div class="flex justify-between items-center">
                                <h4 class="font-semibold text-red-800">Class</h4>
                                <button class="text-red-600 hover:text-red-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" /></svg>
                                </button>
                            </div>
                            <p class="text-3xl font-bold text-red-900 mt-2">10</p>
                            <button class="mt-4 text-left text-red-700 text-sm font-medium">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="lg:col-span-1 bg-white p-6 rounded-xl shadow-sm">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Total Attendance</h3>
                            <div class="flex space-x-2 text-sm">
                                <button class="text-blue-600 font-medium">Today</button>
                                <button class="text-gray-400 hover:text-gray-600">All</button>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row items-center justify-around space-y-4 sm:space-y-0 sm:space-x-4 mt-4">
                            <div class="relative w-36 h-36">
                                {{-- Simulasi Donut Multi-segmen --}}
                                <div class="w-36 h-36 rounded-full"
                                     style="background: conic-gradient(rgb(59, 130, 246) 0% 60%, rgb(244, 114, 182) 60% 75%, rgb(229, 231, 235) 75% 100%);">
                                    <div class="absolute inset-4 rounded-full bg-white"></div>
                                </div>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <button class="bg-blue-600 hover:bg-blue-700 text-white rounded-full p-2 shadow-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                          <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <ul class="space-y-3 text-sm text-gray-700">
                                <li class="flex items-center">
                                    <span class="w-3 h-3 rounded-full bg-blue-500 mr-2"></span>
                                    60% <span class="ml-1 text-gray-500">Present</span>
                                </li>
                                <li class="flex items-center">
                                    <span class="w-3 h-3 rounded-full bg-pink-500 mr-2"></span>
                                    15% <span class="ml-1 text-gray-500">Excused</span>
                                </li>
                                <li class="flex items-center">
                                    <span class="w-3 h-3 rounded-full bg-gray-300 mr-2"></span>
                                    25% <span class="ml-1 text-gray-500">Absent</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Weekly Absence Report</h3>
                            <button class="text-sm text-gray-500 hover:text-gray-700">
                                This Week 
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block ml-1" viewBox="0 0 20 20" fill="currentColor">
                                  <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                        
                        {{-- Simulasi Bar Chart --}}
                        <div class="w-full h-48 flex justify-around items-end space-x-2 border-b border-gray-200 pb-2">
                            <div class="flex flex-col items-center w-1/6">
                                <div class="w-6 bg-blue-200 rounded-t-lg" style="height: 60%;"></div>
                                <span class="mt-1 text-xs text-gray-500">M</span>
                            </div>
                            <div class="flex flex-col items-center w-1/6">
                                <div class="w-6 bg-blue-200 rounded-t-lg" style="height: 80%;"></div>
                                <span class="mt-1 text-xs text-gray-500">T</span>
                            </div>
                            <div class="flex flex-col items-center w-1/6">
                                <div class="w-6 bg-blue-200 rounded-t-lg" style="height: 40%;"></div>
                                <span class="mt-1 text-xs text-gray-500">W</span>
                            </div>
                            <div class="flex flex-col items-center w-1/6">
                                <div class="w-6 bg-blue-200 rounded-t-lg" style="height: 70%;"></div>
                                <span class="mt-1 text-xs text-gray-500">T</span>
                            </div>
                            <div class="flex flex-col items-center w-1/6">
                                <div class="w-6 bg-blue-200 rounded-t-lg" style="height: 90%;"></div>
                                <span class="mt-1 text-xs text-gray-500">F</span>
                            </div>
                            <div class="flex flex-col items-center w-1/6">
                                <div class="w-6 bg-blue-200 rounded-t-lg" style="height: 30%;"></div>
                                <span class="mt-1 text-xs text-gray-500">S</span>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
            {{-- Ini adalah akhir dari konten dashboard kustom Anda --}}

    

        </div>
    </div>
</x-app-layout>