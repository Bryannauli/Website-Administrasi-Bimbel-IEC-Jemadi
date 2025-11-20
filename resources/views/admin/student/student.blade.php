<!-- admin/student/student -->
<x-app-layout>

    <x-slot name="header">
        <div class="ml-64 p-6">

            <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900">Home</a>
            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
            <span class="text-gray-900 font-medium">Students</span>

            <h1 class="text-3xl font-bold mb-8">
                <span class="text-red-500">Students</span>
            </h1>

            <div class="bg-white rounded-xl shadow-md p-4 mb-6 max-w-md">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-6">
                        <div>
                            <h3 class="text-base font-semibold text-gray-800 mb-1">Students</h3>
                            <p class="text-2xl font-bold text-gray-900">5,909</p>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="flex items-center gap-2 px-3 py-1.5 bg-blue-50 rounded-md">
                                <span class="text-xs font-medium text-gray-700">Active</span>
                                <span class="text-base font-bold text-gray-900">4287</span>
                            </div>

                            <div class="flex items-center gap-2 px-3 py-1.5 bg-red-50 rounded-md">
                                <span class="text-xs font-medium text-gray-700">Inactive</span>
                                <span class="text-base font-bold text-gray-900">752</span>
                            </div>
                        </div>
                    </div>

                    <button class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                        </svg>
                    </button>
                </div>
            </div>


            <!-- Table Card -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden max-w-4xl mx-auto">

                <!-- Table Header Actions -->
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="relative inline-block">
                                <select
                                    class="appearance-none px-4 pr-10 py-2 border border-gray-300 rounded-lg text-sm text-gray-700
                focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white cursor-pointer">
                                    <option>All Classes</option>
                                    <option>English</option>
                                    <option>Math</option>
                                </select>

                                <!-- Custom Arrow -->
                                <svg class="w-4 h-4 text-gray-500 absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>

                            <a href="{{ route('admin.student.add', 1) }}" class="pl-3 pr-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium flex items-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                                Add Student
                            </a>

                        </div>

                        <div class="relative">
                            <input type="text" placeholder="Search" class="w-80 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-4 text-left text-sm font-semibold text-gray-700">
                                    <div class="flex items-center gap-2">
                                        No.

                                    </div>
                                </th>
                                <th class="px-4 py-4 text-left text-sm font-semibold text-gray-700">
                                    <div class="flex items-center gap-2">
                                        Student Number
                                        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M5 12a1 1 0 102 0V6.414l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L5 6.414V12zM15 8a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L15 13.586V8z" />
                                        </svg>
                                    </div>
                                </th>
                                <th class="px-4 py-4 text-left text-sm font-semibold text-gray-700">
                                    <div class="flex items-center gap-2">
                                        Name
                                    </div>
                                </th>
                                <th class="px-4 py-4 text-left text-sm font-semibold text-gray-700">Gender</th>
                                <th class="px-4 py-4 text-left text-sm font-semibold text-gray-700">
                                    <div class="flex items-center gap-2">
                                        Class
                                    </div>
                                </th>
                                <th class="px-4 py-4 text-left text-sm font-semibold text-gray-700">Status</th>
                                <th class="px-4 py-4 text-left text-sm font-semibold text-gray-700">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <!-- Row 1 -->
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-4">
                                    <span class="text-gray-600">1</span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="text-gray-600">127893683</span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="text-gray-600">Lee Hanjin</span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="text-gray-600">Man</span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="text-gray-600">English</span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-medium">Active</span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('admin.student.detail', 1) }}" class="text-gray-400 hover:text-blue-600 transition-colors">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                            </svg>
                                        </a>
                                        <button class="text-gray-400 hover:text-red-600 transition-colors">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                        <button class="text-gray-400 hover:text-green-600 transition-colors">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Repeat rows -->
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-4">
                                    <span class="text-gray-600">1</span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="text-gray-600">127893683</span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="text-gray-600">Lee Hanjin</span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="text-gray-600">Man</span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="text-gray-600">English</span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-medium">Active</span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-3">
                                        <button class="text-gray-400 hover:text-blue-600 transition-colors">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                        <button class="text-gray-400 hover:text-red-600 transition-colors">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                        <button class="text-gray-400 hover:text-green-600 transition-colors">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-4">
                                    <span class="text-gray-600">1</span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="text-gray-600">127893683</span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="text-gray-600">Lee Hanjin</span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="text-gray-600">Man</span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="text-gray-600">English</span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-medium">Active</span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-3">
                                        <button class="text-gray-400 hover:text-blue-600 transition-colors">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                        <button class="text-gray-400 hover:text-red-600 transition-colors">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                        <button class="text-gray-400 hover:text-green-600 transition-colors">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-4">
                                    <span class="text-gray-600">1</span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="text-gray-600">127893683</span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="text-gray-600">Lee Hanjin</span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="text-gray-600">Man</span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="text-gray-600">English</span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-medium">Active</span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-3">
                                        <button class="text-gray-400 hover:text-blue-600 transition-colors">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                        <button class="text-gray-400 hover:text-red-600 transition-colors">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                        <button class="text-gray-400 hover:text-green-600 transition-colors">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-4">
                                    <span class="text-gray-600">1</span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="text-gray-600">127893683</span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="text-gray-600">Lee Hanjin</span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="text-gray-600">Man</span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="text-gray-600">English</span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-medium">Active</span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-3">
                                        <button class="text-gray-400 hover:text-blue-600 transition-colors">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                        <button class="text-gray-400 hover:text-red-600 transition-colors">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                        <button class="text-gray-400 hover:text-green-600 transition-colors">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                    <button class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        Previous
                    </button>
                    <span class="text-sm text-gray-600">Page 1 of 10</span>
                    <button class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        Next
                    </button>
                </div>
            </div>

</x-app-layout>