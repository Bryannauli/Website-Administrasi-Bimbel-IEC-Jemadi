<x-app-layout>
    
    {{-- HAPUS SLOT HEADER --}}
    <x-slot name="header"></x-slot>

    {{-- KONTEN UTAMA --}}
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            {{-- 1. BREADCRUMB --}}
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                    <li class="inline-flex items-center">
                        <a href="{{ route('teacher.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-blue-600 transition-colors">
                           <svg class="w-3 h-3 mr-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                            </svg>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                           <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                            </svg>
                            <a href="{{ route('teacher.classes.index') }}" class="ml-1 text-sm font-medium text-gray-500 hover:text-blue-600 md:ml-2 transition-colors">Class</a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                           <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                            </svg>
                            <a href="{{ route('teacher.classes.detail', $class->id) }}" class="ml-1 text-sm font-medium text-gray-500 hover:text-blue-600 md:ml-2 transition-colors">Detail</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                          <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                            </svg>
                            <span class="ml-1 text-sm font-bold text-gray-800 md:ml-2">Attendance</span>
                        </div>
                    </li>
                </ol>
            </nav>

            {{-- 2. HEADER INFO SESI --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800">
                            {{ \Carbon\Carbon::parse($session->date)->format('d F Y') }}
                        </h2>
                        <p class="text-gray-500 mt-1 text-sm">
                            Recording attendance for <span class="font-bold text-gray-700">{{ $class->name }}</span>
                        </p>
                    </div>
                    
                    <div class="flex items-center bg-blue-50 px-5 py-3 rounded-xl border border-blue-100 shadow-sm">
                        <div class="text-center px-4 border-r border-blue-200">
                            <p class="text-[10px] text-blue-500 uppercase font-bold tracking-wider">Start Time</p>
                            <p class="text-xl font-black text-blue-700 font-mono mt-0.5">
                                {{ \Carbon\Carbon::parse($class->start_time)->format('H:i') }}
                            </p>
                        </div>
                        <div class="text-center px-4">
                            <p class="text-[10px] text-blue-500 uppercase font-bold tracking-wider">End Time</p>
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

                <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse min-w-max">
                            <thead class="bg-gray-50 text-xs text-gray-500 font-bold uppercase border-b border-gray-200 tracking-wider">
                                <tr>
                                    <th class="px-6 py-4 w-16 text-center">No</th>
                                    <th class="px-6 py-4 w-32">Student No.</th>
                                    <th class="px-6 py-4 min-w-[200px]">Name</th>
                                    <th class="px-4 py-4 text-center w-24">Present</th>
                                    <th class="px-4 py-4 text-center w-24">Absent</th>
                                    <th class="px-4 py-4 text-center w-24">Permitted</th>
                                    <th class="px-4 py-4 text-center w-24">Sick</th>
                                    <th class="px-4 py-4 text-center w-24">Late</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm text-gray-700 bg-white">
                                @forelse($students as $index => $student)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 text-center text-gray-500 font-medium">{{ $index + 1 }}</td>
                                    
                                    <td class="px-6 py-4 font-mono text-gray-500">{{ $student->student_number ?? '-' }}</td>
                                    
                                    <td class="px-6 py-4 font-bold text-gray-800">{{ $student->name }}</td>

                                    {{-- RADIO BUTTONS (Dirapikan) --}}
                                    
                                    {{-- Present --}}
                                    <td class="px-4 py-4 text-center bg-blue-50/30">
                                        <div class="flex justify-center">
                                            <input type="radio" name="attendance[{{ $student->id }}]" value="present" 
                                                {{ ($student->current_status == 'present' || $student->current_status == null) ? 'checked' : '' }}
                                                class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 cursor-pointer transition-transform hover:scale-110">
                                        </div>
                                    </td>

                                    {{-- Absent --}}
                                    <td class="px-4 py-4 text-center hover:bg-red-50/50 transition-colors">
                                        <div class="flex justify-center">
                                            <input type="radio" name="attendance[{{ $student->id }}]" value="absent" 
                                                {{ $student->current_status == 'absent' ? 'checked' : '' }}
                                                class="w-5 h-5 text-red-500 bg-gray-100 border-gray-300 focus:ring-red-500 cursor-pointer transition-transform hover:scale-110">
                                        </div>
                                    </td>

                                    {{-- Permitted --}}
                                    <td class="px-4 py-4 text-center hover:bg-yellow-50/50 transition-colors">
                                        <div class="flex justify-center">
                                            <input type="radio" name="attendance[{{ $student->id }}]" value="permitted" 
                                                {{ $student->current_status == 'permitted' ? 'checked' : '' }}
                                                class="w-5 h-5 text-yellow-500 bg-gray-100 border-gray-300 focus:ring-yellow-500 cursor-pointer transition-transform hover:scale-110">
                                        </div>
                                    </td>

                                    {{-- Sick --}}
                                    <td class="px-4 py-4 text-center hover:bg-purple-50/50 transition-colors">
                                        <div class="flex justify-center">
                                            <input type="radio" name="attendance[{{ $student->id }}]" value="sick" 
                                                {{ $student->current_status == 'sick' ? 'checked' : '' }}
                                                class="w-5 h-5 text-purple-500 bg-gray-100 border-gray-300 focus:ring-purple-500 cursor-pointer transition-transform hover:scale-110">
                                        </div>
                                    </td>

                                    {{-- Late --}}
                                    <td class="px-4 py-4 text-center hover:bg-orange-50/50 transition-colors">
                                        <div class="flex justify-center">
                                            <input type="radio" name="attendance[{{ $student->id }}]" value="late" 
                                                {{ $student->current_status == 'late' ? 'checked' : '' }}
                                                class="w-5 h-5 text-orange-500 bg-gray-100 border-gray-300 focus:ring-orange-500 cursor-pointer transition-transform hover:scale-110">
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="bg-gray-50 rounded-full p-4 mb-3 border border-gray-100">
                                                <i class="fas fa-user-slash text-3xl text-gray-300"></i>
                                            </div>
                                            <p class="text-base font-medium text-gray-600">No active students found in this class.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- FOOTER ACTIONS --}}
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex flex-col sm:flex-row justify-end gap-3">
                        <a href="{{ route('teacher.classes.detail', $class->id) }}" 
                           class="inline-flex justify-center items-center bg-white border border-gray-300 text-gray-700 px-6 py-2.5 rounded-xl hover:bg-gray-50 transition shadow-sm font-semibold text-sm">
                            Cancel
                        </a>
                        
                        <button type="submit" 
                                class="inline-flex justify-center items-center bg-blue-600 text-white px-8 py-2.5 rounded-xl hover:bg-blue-700 transition shadow-lg shadow-blue-200 font-semibold text-sm">
                            <i class="fas fa-save mr-2"></i> Save Attendance
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</x-app-layout>