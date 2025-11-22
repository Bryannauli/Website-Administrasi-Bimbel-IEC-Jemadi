<x-app-layout>
    {{-- Header slot kosong karena kita pakai custom header di body --}}
    <x-slot name="header"></x-slot>

    {{-- Konten Utama --}}
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Breadcrumb & Title --}}
            <div class="mb-8">
                {{-- Breadcrumb --}}
                <div class="flex items-center gap-2 text-sm font-medium text-gray-500 mb-2">
                    <a href="{{ route('dashboard') }}" class="hover:text-gray-900  border-gray-800 text-gray-900">Dashboard</a>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    <span class="text-gray-500">Assesment</span>
                </div>

                {{-- Title --}}
                <h1 class="text-3xl font-bold bg-gradient-to-b from-blue-500 to-red-500 bg-clip-text text-transparent">
                    Assesment
                </h1>
            </div>

            {{-- Filters & Action Bar --}}
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-6">
                
                <div class="flex items-center gap-4 w-full sm:w-auto">
                    {{-- Filter All --}}
                    <div class="relative">
                        <select class="appearance-none pl-4 pr-10 py-2 border border-gray-200 rounded-lg text-sm text-gray-600 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 cursor-pointer shadow-sm">
                            <option>All</option>
                            <option>Active</option>
                            <option>Inactive</option>
                        </select>
                        
                    </div>

                    {{-- Create Button --}}
                    <a href="#" class="inline-flex items-center px-4 py-2 bg-blue-700 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        Create New Assesment
                    </a>
                </div>

                {{-- Filter Year --}}
                <div class="relative w-full sm:w-auto">
                    <select class="appearance-none w-full sm:w-40 pl-4 pr-10 py-2 border border-gray-200 rounded-lg text-sm text-gray-600 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 cursor-pointer shadow-sm">
                        <option>2025/2026</option>
                        <option>2024/2025</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </div>
                </div>
            </div>

            {{-- Table Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50/50 text-xs text-gray-400 font-medium uppercase border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4 w-16 font-normal">No</th>
                                <th class="px-6 py-4 font-normal">Date</th>
                                <th class="px-6 py-4 font-normal">Time</th>
                                <th class="px-6 py-4 font-normal">Category</th>
                                <th class="px-6 py-4 font-normal">Type</th>
                                <th class="px-6 py-4 font-normal">Status</th>
                                <th class="px-6 py-4 font-normal text-center w-32"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                            
                            <!-- Row 1 -->
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-5 text-gray-900">1</td>
                                <td class="px-6 py-5">2025-11-10</td>
                                <td class="px-6 py-5">
                                    08.00 - <br> 09.00
                                </td>
                                <td class="px-6 py-5">Pre-Level</td>
                                <td class="px-6 py-5">Mid</td>
                                <td class="px-6 py-5">
                                    <span class="px-3 py-1 bg-indigo-100 text-indigo-600 rounded-md text-xs font-medium">Active</span>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center justify-center gap-4">
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

                            <!-- Row 2 -->
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-5 text-gray-900">1</td>
                                <td class="px-6 py-5">2025-11-10</td>
                                <td class="px-6 py-5">
                                    08.00 - <br> 09.00
                                </td>
                                <td class="px-6 py-5">Level</td>
                                <td class="px-6 py-5">Final</td>
                                <td class="px-6 py-5">
                                    <span class="px-3 py-1 bg-indigo-100 text-indigo-600 rounded-md text-xs font-medium">Active</span>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center justify-center gap-4">
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

                            <!-- Row 3 -->
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-5 text-gray-900">1</td>
                                <td class="px-6 py-5">2025-11-10</td>
                                <td class="px-6 py-5">
                                    08.00 - <br> 09.00
                                </td>
                                <td class="px-6 py-5">Step</td>
                                <td class="px-6 py-5">Mid</td>
                                <td class="px-6 py-5">
                                    <span class="px-3 py-1 bg-indigo-100 text-indigo-600 rounded-md text-xs font-medium">Active</span>
                                </td>
<td class="px-6 py-5">
    <div class="flex items-center justify-center gap-4">
        
        <!-- 1. TOMBOL VIEW (Mata) -->
        <!-- Bungkus dengan div 'group relative' untuk tooltip -->
        <div class="group relative">
            {{-- Link ke halaman detail (ganti ID sesuai data asli nanti) --}}
            <a href="{{ route('admin.assessment.show', 1) }}" class="text-gray-400 hover:text-blue-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            </a>
            
            <!-- Tooltip -->
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

                {{-- Pagination --}}
                <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
                    <button class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors w-auto">
                        Previous
                    </button>
                    <span class="text-sm text-gray-500">Page 1 of 10</span>
                    <button class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors w-auto">
                        Next
                    </button>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>