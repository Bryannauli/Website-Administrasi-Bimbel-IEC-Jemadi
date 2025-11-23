<x-app-layout>
    <x-slot name="header"></x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Breadcrumb & Title --}}
            <div class="mb-8">
                <div class="flex items-center gap-2 text-sm font-medium text-gray-500 mb-2">
                     <a href="{{ route('dashboard') }}" class="hover:text-gray-900  border-gray-800 text-gray-900">Dashboard</a>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    <a href="{{ route('admin.classes.index') }}" class="hover:text-gray-900  border-gray-800 text-gray-900">Class</a>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    <span class="text-gray-500">Class Details</span>
                </div>

                {{-- Judul Gradient --}}
                <h1 class="text-3xl font-bold bg-gradient-to-b from-red-500 to-blue-600 bg-clip-text text-transparent drop-shadow-sm">
                    Class Details
                </h1>
            </div>

            {{-- Action Bar (Filters, Add, Search) --}}
            <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
                
                <div class="flex items-center gap-3 w-full md:w-auto">
                    {{-- Filter Button --}}
                    <button class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" /></svg>
                        Filters
                    </button>

                    {{-- Add Schedule Button --}}
                    <button class="inline-flex items-center px-4 py-2 bg-blue-700 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-blue-800 focus:outline-none shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        Add Schedule
                    </button>
                </div>

                {{-- Search Bar --}}
                <div class="relative w-full md:w-72">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>
                    <input type="text" class="block w-full pl-10 pr-3 py-2 border border-gray-200 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm shadow-sm" placeholder="Search">
                </div>
            </div>

            {{-- Table Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50 text-xs text-gray-400 font-medium uppercase border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4 w-16 font-normal text-center">No</th>
                                <th class="px-6 py-4 font-normal">Class</th>
                                <th class="px-6 py-4 font-normal">Schedule</th>
                                <th class="px-6 py-4 font-normal">Teacher</th>
                                <th class="px-6 py-4 font-normal">Room</th>
                                <th class="px-6 py-4 font-normal">Category</th>
                                <th class="px-6 py-4 font-normal">Status</th>
                                <th class="px-6 py-4 font-normal text-center w-32"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                            @forelse($paginatedSchedules as $index => $schedule)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-5 text-center text-gray-500">
                                        {{ $paginatedSchedules->firstItem() + $index }}
                                    </td>
                                    <td class="px-6 py-5 font-medium text-gray-900">
                                        {{ $schedule->class_name }}
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="text-gray-900 font-medium">{{ $schedule->days }}</div>
                                        <div class="text-gray-500 text-xs mt-1">{{ $schedule->time }}</div>
                                    </td>
                                    <td class="px-6 py-5 text-gray-900">
                                        {{ $schedule->teacher }}
                                    </td>
                                    <td class="px-6 py-5 text-gray-600">
                                        {{ $schedule->room }}
                                    </td>
                                    <td class="px-6 py-5 text-gray-600">
                                        {{ $schedule->category }}
                                    </td>
                                    <td class="px-6 py-5">
                                        <span class="px-3 py-1 bg-purple-100 text-purple-600 rounded-md text-xs font-medium">
                                            {{ $schedule->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex items-center justify-center gap-4">
                                            <a href="{{ route('admin.class.detailclass', $schedule->id) }}"  class="text-gray-400 hover:text-blue-600 transition-colors" title="View Details">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                            </a>
                                            <button class="text-gray-400 hover:text-red-600 transition-colors" title="Delete">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                            <button class="text-gray-400 hover:text-green-600 transition-colors" title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="px-6 py-10 text-center text-gray-500">No schedule found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                {{-- Pagination --}}
                <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between bg-white">
                    <button class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors" disabled>
                        Previous
                    </button>
                    <span class="text-sm text-gray-500">Page 1 of 1</span>
                    <button class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors" disabled>
                        Next
                    </button>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>