<x-app-layout>
    <x-slot name="header"></x-slot>

    <div class="bg-[#EEF2FF] min-h-screen py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            {{-- 1. BREADCRUMB (Style Konsisten) --}}
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('teacher.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-blue-600 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            <a href="{{ route('teacher.classes.index') }}" class="ml-1 text-sm font-medium text-gray-500 hover:text-blue-600 md:ml-2">Classes</a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            <a href="{{ route('teacher.classes.detail', $class->id) }}" class="ml-1 text-sm font-medium text-gray-500 hover:text-blue-600 md:ml-2 truncate max-w-[150px]">{{ $class->name }}</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            <span class="ml-1 text-sm font-medium text-gray-900 md:ml-2">Attendance Recording</span>
                        </div>
                    </li>
                </ol>
            </nav>

            {{-- 2. HEADER INFO (Gradient Style) --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-bl-full -mr-8 -mt-8"></div>
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
                    <div>
                        <h2 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent inline-block">
                            Record Attendance
                        </h2>
                        <p class="text-gray-500 mt-1">
                            Session Date: <span class="font-bold text-gray-800">{{ \Carbon\Carbon::parse($session->date)->format('l, d F Y') }}</span>
                        </p>
                    </div>
                    
                    {{-- Time Box --}}
                    <div class="flex items-center bg-blue-50 px-6 py-3 rounded-xl border border-blue-100 shadow-sm">
                        <div class="text-center px-4 border-r border-blue-200">
                            <p class="text-[10px] text-blue-600 uppercase font-bold tracking-wider">Start</p>
                            <p class="text-xl font-black text-blue-700 font-mono mt-0.5">
                                {{ \Carbon\Carbon::parse($class->start_time)->format('H:i') }}
                            </p>
                        </div>
                        <div class="text-center px-4">
                            <p class="text-[10px] text-blue-600 uppercase font-bold tracking-wider">End</p>
                            <p class="text-xl font-black text-blue-700 font-mono mt-0.5">
                                {{ \Carbon\Carbon::parse($class->end_time)->format('H:i') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. FORM ABSENSI --}}
            <form action="{{ route('teacher.classes.session.update', [$class->id, $session->id]) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
                    
                    {{-- Sticky Header Table Container --}}
                    <div class="overflow-x-auto overflow-y-auto max-h-[65vh] custom-scrollbar">
                        <table class="w-full text-left border-collapse relative">
                            <thead class="bg-gray-50 text-xs text-gray-500 font-bold uppercase border-b border-gray-200 sticky top-0 z-10 shadow-sm">
                                <tr>
                                    <th class="px-6 py-4 w-16 text-center bg-gray-50">No</th>
                                    <th class="px-6 py-4 w-32 bg-gray-50">Student ID</th>
                                    <th class="px-6 py-4 min-w-[200px] bg-gray-50">Student Name</th>
                                    <th class="px-2 py-4 text-center w-24 bg-blue-50 text-blue-700 border-b-2 border-blue-200">Present</th>
                                    <th class="px-2 py-4 text-center w-24 bg-red-50 text-red-700 border-b-2 border-red-200">Absent</th>
                                    <th class="px-2 py-4 text-center w-24 bg-emerald-50 text-emerald-700 border-b-2 border-emerald-200">Permit</th>
                                    <th class="px-2 py-4 text-center w-24 bg-purple-50 text-purple-700 border-b-2 border-purple-200">Sick</th>
                                    <th class="px-2 py-4 text-center w-24 bg-yellow-50 text-yellow-700 border-b-2 border-yellow-200">Late</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm text-gray-700 bg-white">
                                @forelse($students as $index => $student)
                                <tr class="hover:bg-gray-50 transition-colors group">
                                    <td class="px-6 py-4 text-center text-gray-400 font-medium text-xs">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 font-mono text-xs text-gray-500">{{ $student->student_number ?? '-' }}</td>
                                    <td class="px-6 py-4 font-bold text-gray-800 group-hover:text-blue-600 transition-colors">{{ $student->name }}</td>

                                    {{-- RADIO BUTTONS --}}
                                    
                                    {{-- Present --}}
                                    <td class="px-2 py-3 text-center bg-blue-50/20 group-hover:bg-blue-50/50 transition-colors">
                                        <div class="flex justify-center">
                                            <input type="radio" name="attendance[{{ $student->id }}]" value="present" 
                                                {{ ($student->current_status == 'present' || $student->current_status == null) ? 'checked' : '' }}
                                                class="w-5 h-5 text-blue-600 bg-white border-gray-300 focus:ring-blue-500 cursor-pointer transition-transform hover:scale-110 shadow-sm">
                                        </div>
                                    </td>

                                    {{-- Absent --}}
                                    <td class="px-2 py-3 text-center group-hover:bg-red-50 transition-colors">
                                        <div class="flex justify-center">
                                            <input type="radio" name="attendance[{{ $student->id }}]" value="absent" 
                                                {{ $student->current_status == 'absent' ? 'checked' : '' }}
                                                class="w-5 h-5 text-red-600 bg-white border-gray-300 focus:ring-red-500 cursor-pointer transition-transform hover:scale-110 shadow-sm">
                                        </div>
                                    </td>

                                    {{-- Permitted --}}
                                    <td class="px-2 py-3 text-center group-hover:bg-emerald-50 transition-colors">
                                        <div class="flex justify-center">
                                            <input type="radio" name="attendance[{{ $student->id }}]" value="permission" 
                                                {{ ($student->current_status == 'permission' || $student->current_status == 'permitted') ? 'checked' : '' }}
                                                class="w-5 h-5 text-emerald-600 bg-white border-gray-300 focus:ring-emerald-500 cursor-pointer transition-transform hover:scale-110 shadow-sm">
                                        </div>
                                    </td>

                                    {{-- Sick --}}
                                    <td class="px-2 py-3 text-center group-hover:bg-purple-50 transition-colors">
                                        <div class="flex justify-center">
                                            <input type="radio" name="attendance[{{ $student->id }}]" value="sick" 
                                                {{ $student->current_status == 'sick' ? 'checked' : '' }}
                                                class="w-5 h-5 text-purple-600 bg-white border-gray-300 focus:ring-purple-500 cursor-pointer transition-transform hover:scale-110 shadow-sm">
                                        </div>
                                    </td>

                                    {{-- Late --}}
                                    <td class="px-2 py-3 text-center group-hover:bg-yellow-50 transition-colors">
                                        <div class="flex justify-center">
                                            <input type="radio" name="attendance[{{ $student->id }}]" value="late" 
                                                {{ $student->current_status == 'late' ? 'checked' : '' }}
                                                class="w-5 h-5 text-yellow-500 bg-white border-gray-300 focus:ring-yellow-500 cursor-pointer transition-transform hover:scale-110 shadow-sm">
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center text-gray-400 bg-gray-50 italic">
                                        No active students found in this class.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- FOOTER ACTIONS --}}
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex flex-col sm:flex-row justify-between items-center gap-4">
                        <div class="text-xs text-gray-400 italic hidden sm:block">
                            * Changes are not saved until you click 'Save Attendance'.
                        </div>
                        
                        <div class="flex gap-3 w-full sm:w-auto">
                            <a href="{{ route('teacher.classes.detail', $class->id) }}" 
                               class="flex-1 sm:flex-none inline-flex justify-center items-center bg-white border border-gray-300 text-gray-700 px-6 py-2.5 rounded-xl hover:bg-gray-50 transition shadow-sm font-bold text-sm">
                                Cancel
                            </a>
                            
                            <button type="submit" 
                                    class="flex-1 sm:flex-none inline-flex justify-center items-center bg-blue-600 text-white px-8 py-2.5 rounded-xl hover:bg-blue-700 transition shadow-lg shadow-blue-200 font-bold text-sm gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                                Save Attendance
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>