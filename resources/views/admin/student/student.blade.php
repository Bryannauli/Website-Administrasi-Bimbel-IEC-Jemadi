<x-app-layout>

    {{-- Header Slot (Opsional, bisa dikosongkan jika desain custom) --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight hidden">
            {{ __('Students') }}
        </h2>
    </x-slot>

    {{-- KONTEN UTAMA --}}
    {{-- HAPUS 'ml-64'. Gunakan container standar Breeze/Tailwind --}}
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Breadcrumb & Title --}}
            <div class="mb-6">
                <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
                    <a href="{{ route('dashboard') }}" class="hover:text-gray-900">Dashboard</a>
                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-gray-900 font-medium">Students</span>
                </div>

                <h1 class="text-3xl font-bold bg-gradient-to-b from-blue-500 to-red-500 bg-clip-text text-transparent">
                    Students
                </h1>
            </div>

            {{-- Stats Card --}}
            {{-- Responsive: flex-col di HP, flex-row di Desktop --}}
            <div class="bg-white rounded-xl shadow-sm p-4 mb-8 max-w-xl border border-gray-100">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    
                    <div class="flex items-center gap-6 w-full sm:w-auto">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-500 mb-1">Total Students</h3>
                            <p class="text-3xl font-bold text-gray-900">5,909</p>
                        </div>

                        {{-- Badges --}}
                        <div class="flex gap-3">
                            <div class="flex items-center gap-2 px-3 py-1 bg-blue-50 text-blue-700 rounded-lg border border-blue-100">
                                <span class="text-xs font-medium">Active</span>
                                <span class="text-sm font-bold">4,287</span>
                            </div>

                            <div class="flex items-center gap-2 px-3 py-1 bg-red-50 text-red-700 rounded-lg border border-red-100">
                                <span class="text-xs font-medium">Inactive</span>
                                <span class="text-sm font-bold">752</span>
                            </div>
                        </div>
                    </div>

                    <button class="text-gray-400 hover:text-gray-600 self-end sm:self-center">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                        </svg>
                    </button>
                </div>
            </div>


            {{-- Table Section --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

                {{-- Table Header Actions --}}
                <div class="p-4 sm:p-6 border-b border-gray-200">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        
                        <div class="flex flex-col sm:flex-row items-center gap-4 w-full sm:w-auto">
                            {{-- Filter Class --}}
                            <div class="relative w-full sm:w-auto">
                                <select class="appearance-none w-full sm:w-48 px-4 pr-10 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white cursor-pointer">
                                    <option>All Classes</option>
                                    <option>English</option>
                                    <option>Math</option>
                                </select>
                               
                            </div>

                            {{-- Add Button --}}
                            <a href="{{ route('admin.student.add', 1) }}" class="w-full sm:w-auto flex justify-center items-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium text-sm shadow-sm">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                                Add Student
                            </a>
                        </div>

                        {{-- Search --}}
                        <div class="relative w-full sm:w-auto">
                            <input type="text" placeholder="Search student..." class="w-full sm:w-64 pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500 font-medium">
                            <tr>
                                <th class="px-6 py-4 w-16">No.</th>
                                <th class="px-6 py-4">
                                    <div class="flex items-center gap-1 cursor-pointer hover:text-gray-700">
                                        Student Number
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M5 12a1 1 0 102 0V6.414l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L5 6.414V12zM15 8a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L15 13.586V8z" /></svg>
                                    </div>
                                </th>
                                <th class="px-6 py-4">Name</th>
                                <th class="px-6 py-4">Gender</th>
                                <th class="px-6 py-4">Class</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                            <!-- Row 1 -->
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-gray-500">1</td>
                                <td class="px-6 py-4 font-medium text-gray-900">127893683</td>
                                <td class="px-6 py-4 font-medium">Lee Hanjin</td>
                                <td class="px-6 py-4">Man</td>
                                <td class="px-6 py-4">English</td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-0.5 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">Active</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-3">
                                        <div class="group relative">
            {{-- Link ke halaman detail (ganti ID sesuai data asli nanti) --}}
            <a href="" class="text-gray-400 hover:text-blue-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            </a>
            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 hidden group-hover:block w-auto">
                <div class="bg-gray-800 text-white text-xs rounded py-1 px-2 whitespace-nowrap">
                    View Details
                </div>
                <!-- Panah kecil ke bawah -->
                <div class="w-2 h-2 bg-gray-800 transform rotate-45 absolute left-1/2 -translate-x-1/2 -bottom-1"></div>
            </div>
        </div>

        <!-- 2. TOMBOL DELETE (Sampah) -->
        <div class="group relative">
            <button class="text-gray-400 hover:text-red-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
            
            <!-- Tooltip -->
            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 hidden group-hover:block w-auto">
                <div class="bg-gray-800 text-white text-xs rounded py-1 px-2 whitespace-nowrap">
                    Delete
                </div>
                 <div class="w-2 h-2 bg-gray-800 transform rotate-45 absolute left-1/2 -translate-x-1/2 -bottom-1"></div>
            </div>
        </div>

        <!-- 3. TOMBOL EDIT (Pensil) -->
        <div class="group relative">
            <a href="#" class="text-gray-400 hover:text-green-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
            </a>

            <!-- Tooltip -->
           <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 hidden group-hover:block w-auto">
                <div class="bg-gray-800 text-white text-xs rounded py-1 px-2 whitespace-nowrap">
                    Edit Data
                </div>
                <div class="w-2 h-2 bg-gray-800 transform rotate-45 absolute left-1/2 -translate-x-1/2 -bottom-1"></div>
        </div>
                                    </div>
                                </td>
                            </tr>
                            <!-- Tambahkan row lain di sini -->
                             <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-gray-500">2</td>
                                <td class="px-6 py-4 font-medium text-gray-900">127893684</td>
                                <td class="px-6 py-4 font-medium">Kim Jiwon</td>
                                <td class="px-6 py-4">Woman</td>
                                <td class="px-6 py-4">Math</td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-0.5 bg-red-100 text-red-700 rounded-full text-xs font-semibold">Inactive</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-3">
                                         <div class="group relative">
            {{-- Link ke halaman detail (ganti ID sesuai data asli nanti) --}}
            <a href="" class="text-gray-400 hover:text-blue-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            </a>
            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 hidden group-hover:block w-auto">
                <div class="bg-gray-800 text-white text-xs rounded py-1 px-2 whitespace-nowrap">
                    View Details
                </div>
                <!-- Panah kecil ke bawah -->
                <div class="w-2 h-2 bg-gray-800 transform rotate-45 absolute left-1/2 -translate-x-1/2 -bottom-1"></div>
            </div>
        </div>

        <!-- 2. TOMBOL DELETE (Sampah) -->
        <div class="group relative">
            <button class="text-gray-400 hover:text-red-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
            
            <!-- Tooltip -->
            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 hidden group-hover:block w-auto">
                <div class="bg-gray-800 text-white text-xs rounded py-1 px-2 whitespace-nowrap">
                    Delete
                </div>
                 <div class="w-2 h-2 bg-gray-800 transform rotate-45 absolute left-1/2 -translate-x-1/2 -bottom-1"></div>
            </div>
        </div>

        <!-- 3. TOMBOL EDIT (Pensil) -->
        <div class="group relative">
            <a href="#" class="text-gray-400 hover:text-green-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
            </a>

            <!-- Tooltip -->
           <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 hidden group-hover:block w-auto">
                <div class="bg-gray-800 text-white text-xs rounded py-1 px-2 whitespace-nowrap">
                    Edit Data
                </div>
                <div class="w-2 h-2 bg-gray-800 transform rotate-45 absolute left-1/2 -translate-x-1/2 -bottom-1"></div>
        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
<!-- //PAGINATION -->
 <div class="px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4 bg-white">
                    
                    {{-- Tombol Previous --}}
                    @if ($students->onFirstPage())
                        <button class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-400 bg-gray-50 cursor-not-allowed w-full sm:w-auto" disabled>
                            Previous
                        </button>
                    @else
                        <a href="{{ $students->previousPageUrl() }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-blue-50 hover:text-blue-600 transition-colors w-full sm:w-auto text-center">
                            Previous
                        </a>
                    @endif

                    {{-- Info Halaman (Page X of Y) --}}
                    <span class="text-sm text-gray-600">
                        Page {{ $students->currentPage() }} of {{ $students->lastPage() }}
                    </span>

                    {{-- Tombol Next --}}
                    @if ($students->hasMorePages())
                        <a href="{{ $students->nextPageUrl() }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-blue-50 hover:text-blue-600 transition-colors w-full sm:w-auto text-center">
                            Next
                        </a>
                    @else
                        <button class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-400 bg-gray-50 cursor-not-allowed w-full sm:w-auto" disabled>
                            Next
                        </button>
                    @endif

                </div>


            </div>

        </div>
    </div>
</x-app-layout>