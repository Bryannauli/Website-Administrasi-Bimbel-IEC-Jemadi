<x-app-layout>
    <x-slot name="header"></x-slot>

    {{-- 
        x-data: Menginisialisasi state untuk modal.
        showEditModal: Mengontrol visibilitas modal.
        editData: Menyimpan data siswa yang sedang diedit.
    --}}
    <div class="py-6" x-data="{ 
        showEditModal: false, 
        editData: { name: '', scores: {} } 
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Breadcrumb --}}
            <div class="mb-6 flex items-center gap-2 text-sm font-medium text-gray-500">
                <a href="{{ route('dashboard') }}" class="hover:text-gray-900 border-b border-gray-800 text-gray-900">Dashboard</a>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                <a href="{{ route('admin.assessment.index') }}" class="hover:text-gray-900">Assesment</a>
            </div>

            {{-- Kartu Info Kelas --}}
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex justify-between items-start relative overflow-hidden mb-8">
                <div class="absolute left-0 top-0 bottom-0 w-3 bg-red-600 rounded-l-2xl"></div>
                
                <div class="pl-4">
                    <h1 class="text-2xl font-bold text-gray-900">Pre-Level</h1>
                    <p class="text-gray-400 text-sm mt-1">English</p>
                    
                    <div class="mt-4 space-y-2 text-gray-500 text-sm">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span>2025-11-10</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>08.00 - 09.00 AM</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            <span>E-101</span>
                        </div>
                    </div>
                </div>

                <span class="px-4 py-1.5 bg-purple-100 text-purple-600 rounded-lg text-sm font-semibold">Active</span>
            </div>

            {{-- Filter Dropdown --}}
            <div class="mb-6">
                <div class="relative w-40">
                    <select class="appearance-none w-full pl-4 pr-10 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-600 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 cursor-pointer shadow-sm">
                        <option>All Classes</option>
                        <option>Class A</option>
                        <option>Class B</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </div>
                </div>
            </div>

            {{-- Tabel Nilai Siswa --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50/50 text-xs text-gray-400 font-medium uppercase border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4 w-16 text-center font-normal">No</th>
                                <th class="px-6 py-4 font-normal">Student Number</th>
                                <th class="px-6 py-4 font-normal">Name</th>
                                <th class="px-6 py-4 font-normal">Score</th>
                                <th class="px-6 py-4 font-normal">Status</th>
                                <th class="px-6 py-4 font-normal text-center w-32"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                            
                            <!-- Row 1 -->
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-5 text-center text-gray-500">1</td>
                                <td class="px-6 py-5 font-medium text-gray-900">1234</td>
                                <td class="px-6 py-5 font-medium text-gray-900">Lee Hanjin</td>
                                <td class="px-6 py-5 text-base">89.0</td>
                                <td class="px-6 py-5">
                                    <span class="px-4 py-1 bg-purple-100 text-purple-600 rounded-lg text-xs font-semibold">Pass</span>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center justify-center gap-4">
                                        
                                        {{-- 1. TOMBOL VIEW (Mata) --}}
                                        <div class="group relative">
                                            {{-- Link ke halaman detail siswa (Placeholder ID=1) --}}
                                            <a href="{{ route('admin.student.detail', 1) }}" class="text-gray-400 hover:text-gray-600 block">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                            </a>
                                            {{-- Tooltip View --}}
                                            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 hidden group-hover:block z-10">
                                                <div class="bg-gray-800 text-white text-xs rounded py-1 px-2 whitespace-nowrap">
                                                    View Details
                                                </div>
                                                <!-- Panah kecil -->
                                                <div class="w-2 h-2 bg-gray-800 transform rotate-45 absolute left-1/2 -translate-x-1/2 -bottom-1"></div>
                                            </div>
                                        </div>

                                        {{-- 2. TOMBOL DELETE (Sampah) --}}
                                        <div class="group relative">
                                            <button class="text-gray-400 hover:text-red-600 block">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                            {{-- Tooltip Delete --}}
                                            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 hidden group-hover:block z-10">
                                                <div class="bg-gray-800 text-white text-xs rounded py-1 px-2 whitespace-nowrap">
                                                    Delete
                                                </div>
                                                <div class="w-2 h-2 bg-gray-800 transform rotate-45 absolute left-1/2 -translate-x-1/2 -bottom-1"></div>
                                            </div>
                                        </div>
                                        
                                        {{-- 3. TOMBOL EDIT (Pensil) --}}
                                        <div class="group relative">
                                            <button @click="showEditModal = true; editData = { name: 'Lee Hanjin' }" class="text-gray-400 hover:text-blue-600 block">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                            </button>
                                            {{-- Tooltip Edit --}}
                                            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 hidden group-hover:block z-10">
                                                <div class="bg-gray-800 text-white text-xs rounded py-1 px-2 whitespace-nowrap">
                                                    Edit Data
                                                </div>
                                                <div class="w-2 h-2 bg-gray-800 transform rotate-45 absolute left-1/2 -translate-x-1/2 -bottom-1"></div>
                                            </div>
                                        </div>

                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- MODAL EDIT (SESUAI GAMBAR SEBELUMNYA) --}}
            <div x-show="showEditModal" 
                style="display: none;"
                class="fixed inset-0 z-50 overflow-y-auto" 
                aria-labelledby="modal-title" role="dialog" aria-modal="true">
                
                {{-- Backdrop --}}
                <div class="flex items-center justify-center min-h-screen px-4 text-center sm:block sm:p-0">
                    <div x-show="showEditModal" 
                        x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
                        x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
                        class="fixed inset-0 bg-gray-800 bg-opacity-75 transition-opacity" aria-hidden="true" @click="showEditModal = false"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    {{-- Konten Modal --}}
                    <div x-show="showEditModal" 
                        x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                        x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                        class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl w-full border-t-4 border-blue-500">
                        
                        {{-- Header Modal --}}
                        <div class="px-6 py-4 flex justify-between items-center border-b border-gray-100">
                            <h3 class="text-xl font-bold text-gray-900" x-text="editData.name"></h3>
                            <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        {{-- Body Modal --}}
                        <div class="px-8 py-6">
                            {{-- Info Kelas --}}
                            <div class="grid grid-cols-3 gap-6 mb-6 text-sm">
                                <div>
                                    <label class="block text-gray-500 font-bold mb-1">Class _id</label>
                                    <p class="text-gray-400">C123</p>
                                </div>
                                <div>
                                    <label class="block text-gray-800 font-bold mb-1">Date</label>
                                    <p class="text-gray-500">19 November 2025</p>
                                </div>
                                <div>
                                    <label class="block text-gray-800 font-bold mb-1">Time</label>
                                    <p class="text-gray-500">08.00 - 09.00</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-6 mb-8 text-sm">
                                <div>
                                    <label class="block text-gray-800 font-bold mb-1">Category</label>
                                    <p class="text-gray-500">Pre-Level</p>
                                </div>
                                <div>
                                    <label class="block text-gray-800 font-bold mb-1">Type</label>
                                    <p class="text-gray-500">Mid</p>
                                </div>
                            </div>

                            {{-- Input Nilai --}}
                            <h4 class="text-xl font-bold text-gray-900 mb-4">Score</h4>
                            
                            <div class="grid grid-cols-3 gap-x-6 gap-y-6 mb-6">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Vocabulary</label>
                                    <input type="number" value="80" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500 text-gray-600">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Grammar</label>
                                    <input type="number" value="80" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500 text-gray-600">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Listening</label>
                                    <input type="number" value="80" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500 text-gray-600">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Speaking</label>
                                    <input type="number" value="80" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500 text-gray-600">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Reading</label>
                                    <input type="number" value="80" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500 text-gray-600">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Spelling</label>
                                    <input type="number" value="80" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500 text-gray-600">
                                </div>
                            </div>

                            {{-- Tombol Save --}}
                            <div class="flex justify-end mt-8">
                                <button type="button" class="px-6 py-2 bg-blue-700 hover:bg-blue-800 text-white font-bold rounded-lg shadow-sm transition-colors">
                                    Save
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>