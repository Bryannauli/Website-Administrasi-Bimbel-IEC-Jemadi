<x-app-layout>
    <x-slot name="header"></x-slot>

    {{-- Hapus bg-[#F3F4FF] dan min-h-screen agar konsisten dengan layout utama --}}
    <div class="py-6 font-sans">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- BREADCRUMB --}}
            <nav class="flex mb-5 text-sm font-medium text-gray-500" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    {{-- 1. Dashboard --}}
                    <li class="inline-flex items-center">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-blue-600">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                            Dashboard
                        </a>
                    </li>
                    {{-- 2. Daily Class Monitor (Current Page) --}}
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            <span class="ml-1 text-sm font-medium text-gray-900 md:ml-2">Daily Class Monitor</span>
                        </div>
                    </li>
                </ol>
            </nav>

            {{-- Title & Date Filter (PERBAIKAN DI SINI) --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4"> {{-- items-start untuk perataan kiri saat wrap --}}
                <div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                        Daily Class Monitor
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">Schedule based monitoring for <span class="font-bold text-gray-700">{{ \Carbon\Carbon::parse($date)->format('l') }}</span>.</p>
                </div>

                {{-- Filter Date --}}
                <form action="{{ route('admin.classes.daily-recap') }}" method="GET" class="bg-white p-2 rounded-xl shadow-sm border border-gray-200 flex items-center gap-3 w-full md:w-auto"> {{-- Tambahkan w-full md:w-auto --}}
                    <div class="flex flex-col">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider pl-1">Select Date</label>
                        <input type="date" name="date" value="{{ $date }}" 
                               class="border-none p-0 text-gray-700 text-sm font-bold focus:ring-0 bg-transparent cursor-pointer h-6"
                               onchange="this.form.submit()">
                    </div>
                    <button type="submit" class="p-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>
                </form>
            </div>

            {{-- TABLE SECTION --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h3 class="font-bold text-gray-700">Schedule List: {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-max">
                        <thead class="bg-white text-xs text-gray-500 font-bold uppercase border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4 w-12">No</th>
                                <th class="px-6 py-4">Time</th>
                                <th class="px-6 py-4">Category</th>      
                                <th class="px-6 py-4">Class Name</th>
                                <th class="px-6 py-4">Classroom</th>
                                <th class="px-6 py-4">Assigned Teacher</th>
                                <th class="px-6 py-4 text-center">Session Status</th>
                                <th class="px-6 py-4 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                            @forelse ($records as $index => $record)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 text-gray-500">{{ $records->firstItem() + $index }}</td>
                                    
                                    {{-- Time --}}
                                    <td class="px-6 py-4 font-mono text-gray-600">
                                        {{ \Carbon\Carbon::parse($record->start_time)->format('H:i') }} - 
                                        {{ \Carbon\Carbon::parse($record->end_time)->format('H:i') }}
                                    </td>

                                    {{-- Category Data --}}
                                    <td class="px-6 py-4">
                                        <span class="inline-block bg-blue-50 text-blue-700 border border-blue-100 px-2 py-0.5 rounded text-[10px] font-bold uppercase">
                                            {{ str_replace('_', ' ', $record->category) }}
                                        </span>
                                    </td>

                                    {{-- Class Name --}}
                                    <td class="px-6 py-4 font-bold text-gray-800">
                                        {{ $record->class_name }}
                                        <span class="ml-2 text-[10px] text-gray-400 font-normal border border-gray-200 px-1.5 py-0.5 rounded uppercase">
                                            {{ $record->teacher_type }} slot
                                        </span>
                                    </td>

                                    {{-- Classroom Data --}}
                                    <td class="px-6 py-4">
                                        <span class="inline-block bg-indigo-50 text-indigo-700 border border-indigo-100 px-2 py-0.5 rounded text-[10px] font-bold uppercase">
                                            {{ $record->classroom }}
                                        </span>
                                    </td>

                                    {{-- Teacher Name --}}
                                    <td class="px-6 py-4">
                                        @if($record->teacher_name)
                                            <div class="flex items-center gap-2">
                                                <div class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">
                                                    {{ substr($record->teacher_name, 0, 1) }}
                                                </div>
                                                <span class="font-medium text-gray-900">{{ $record->teacher_name }}</span>
                                            </div>
                                        @else
                                            <span class="text-gray-400 font-bold text-lg pl-4">-</span>
                                        @endif
                                    </td>
                                    
                                    {{-- Status --}}
                                    <td class="px-6 py-4 text-center">
                                        @if($record->session_id)
                                            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase bg-green-100 text-green-700">
                                                Started
                                            </span>
                                        @else
                                            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase bg-gray-100 text-gray-500">
                                                Pending
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Action (Eye Icon) --}}
                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ route('admin.classes.detailclass', ['id' => $record->class_id]) }}" 
                                            class="text-gray-400 hover:text-blue-600 transition-colors" title="View Details">
                                            <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="px-6 py-10 text-center text-gray-500">No classes scheduled for this day ({{ \Carbon\Carbon::parse($date)->format('l') }}).</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                {{-- Pagination --}}
                @if($records->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 bg-white">
                        {{ $records->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>