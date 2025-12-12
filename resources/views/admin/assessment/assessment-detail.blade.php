<x-app-layout>
    <x-slot name="header"></x-slot>

    <div class="py-6" x-data="{ 
        isEditing: false, 
        assessmentType: '{{ $type }}' 
    }">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- 1. BREADCRUMB DINAMIS (Berdasarkan parameter 'from') --}}
            <nav class="flex mb-5" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-blue-600 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                            Dashboard
                        </a>
                    </li>
                    
                    @if(request('from') == 'assessment')
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                <a href="{{ route('admin.assessment.index') }}" class="ml-1 text-sm font-medium text-gray-500 hover:text-blue-600 md:ml-2">Assessment Recap</a>
                            </div>
                        </li>
                    @else
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                <a href="{{ route('admin.classes.index') }}" class="ml-1 text-sm font-medium text-gray-500 hover:text-blue-600 md:ml-2">Classes</a>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                <a href="{{ route('admin.classes.detailclass', $class->id) }}" class="ml-1 text-sm font-medium text-gray-500 hover:text-blue-600 md:ml-2 truncate max-w-[100px] sm:max-w-xs">{{ $class->name }}</a>
                            </div>
                        </li>
                    @endif

                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            <span class="ml-1 text-sm font-medium text-gray-900 md:ml-2 uppercase">{{ $type }} Assessment</span>
                        </div>
                    </li>
                </ol>
            </nav>

            {{-- 2. HEADER HALAMAN (GRADIENT BIRU-INDIGO) --}}
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                <div>
                    <h2 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                        Assessment Details: {{ ucfirst($type) }}
                    </h2>
                    <p class="text-gray-500 text-sm mt-1">Class: <span class="font-bold text-gray-800">{{ $class->name }}</span></p>
                </div>
                
                <div class="flex items-center gap-3">
                    <button type="submit" form="assessmentForm"
                            @click="if (!isEditing) { isEditing = true; return false; }"
                            :class="isEditing ? 'bg-green-600 hover:bg-green-700' : 'bg-blue-600 hover:bg-blue-700'"
                            class="px-6 py-2.5 text-white text-sm font-bold rounded-lg transition shadow-md flex items-center gap-2">
                        <span x-text="isEditing ? 'Save Changes' : 'Edit Assessment Info'"></span>
                    </button>
                </div>
            </div>

            {{-- 3. FORM INPUT NILAI --}}
            {{-- Form action dimatikan sementara agar tidak error --}}
            <form id="assessmentForm" method="POST" action="#">
                @csrf
                
                {{-- CONFIGURATION BOX (EDIT MODE) --}}
                <div x-show="isEditing" x-transition class="bg-white border border-gray-200 p-6 rounded-2xl mb-6 shadow-sm">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-4">
                            <h3 class="text-xs font-bold text-blue-600 uppercase tracking-widest pb-1 border-b border-gray-100">Written Test Info</h3>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">Exam Date</label>
                                <input type="date" name="written_date" value="{{ old('written_date', $session->date) }}"
                                       class="block w-full rounded-xl border border-gray-300 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-all">
                            </div>
                        </div>
                        <div class="space-y-4 border-l-0 md:border-l md:pl-8 border-gray-100">
                            <h3 class="text-xs font-bold text-purple-600 uppercase tracking-widest pb-1 border-b border-gray-100">Speaking Test Info</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">Test Date</label>
                                    <input type="date" name="speaking_date" value="{{ old('speaking_date', $speakingTest->date ?? now()->toDateString()) }}"
                                           class="block w-full rounded-xl border border-gray-300 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-purple-500 sm:text-sm transition-all">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">Interviewer</label>
                                    <select name="interviewer_id" class="block w-full rounded-xl border border-gray-300 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-purple-500 sm:text-sm transition-all">
                                        @foreach($allTeachers as $teacher)
                                            <option value="{{ $teacher->id }}" {{ old('interviewer_id', $currentInterviewerId) == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">Speaking Topic</label>
                                <input type="text" name="topic" value="{{ old('topic', $speakingTest->topic ?? '') }}" placeholder="e.g. Daily Activity"
                                       class="block w-full rounded-xl border border-gray-300 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-purple-500 sm:text-sm transition-all">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- READ-ONLY SUMMARY (2 ROWS) --}}
                <div x-show="!isEditing" class="space-y-4 mb-8" x-transition>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3">
                            <div class="p-2.5 bg-yellow-50 text-yellow-600 rounded-xl"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h-4v-6h-2v6H7v-6H5v6H3m14 0h-4v-6h-2v6H7v-6H5v6H3M12 4a4 4 0 11-8 0 4 4 0 018 0zm4 0a4 4 0 11-8 0 4 4 0 018 0z" /></svg></div>
                            <div><p class="text-[10px] font-bold text-gray-400 uppercase">Form Teacher</p><p class="text-sm font-bold text-gray-800">{{ $class->formTeacher->name ?? 'Not Assigned' }}</p></div>
                        </div>
                        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3 border-l-4 border-l-blue-500">
                            <div class="p-2.5 bg-blue-50/80 text-blue-700 rounded-xl"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg></div>
                            <div><p class="text-[10px] font-bold text-gray-400 uppercase">Written Exam Date</p><p class="text-sm font-bold text-gray-800">{{ \Carbon\Carbon::parse($session->date)->format('d F Y') }}</p></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3 border-l-4 border-l-purple-500">
                            <div class="p-2.5 bg-purple-50/80 text-purple-700 rounded-xl"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path></svg></div>
                            <div><p class="text-[10px] font-bold text-gray-400 uppercase">Speaking Date</p><p class="text-sm font-bold text-gray-800">{{ \Carbon\Carbon::parse($speakingTest->date ?? now())->format('d F Y') }}</p></div>
                        </div>
                        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3">
                            <div class="p-2.5 bg-green-50/80 text-green-700 rounded-xl"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg></div>
                            <div><p class="text-[10px] font-bold text-gray-400 uppercase">Speaking Interviewer</p><p class="text-sm font-bold text-gray-800">{{ $speakingTest->interviewer->name ?? 'Not Set' }}</p></div>
                        </div>
                        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3">
                            <div class="p-2.5 bg-orange-50/80 text-orange-700 rounded-xl"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg></div>
                            <div><p class="text-[10px] font-bold text-gray-400 uppercase">Speaking Topic</p><p class="text-sm font-bold text-gray-800">{{ $speakingTest->topic ?? '-' }}</p></div>
                        </div>
                    </div>
                </div>

                {{-- 4. GRADES TABLE (Urutan: Vocab, Grammar, Listening, Speaking, Reading, Spelling) --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse min-w-max">
                            <thead class="bg-gray-50 text-[10px] text-gray-500 font-bold uppercase border-b border-gray-200 tracking-widest">
                                <tr>
                                    <th class="px-6 py-4 w-16 text-center">No</th>
                                    <th class="px-6 py-4">Student Name</th>
                                    <th class="px-3 py-4 text-center border-l">Vocab</th>
                                    <th class="px-3 py-4 text-center">Grammar</th>
                                    <th class="px-3 py-4 text-center">Listening</th>
                                    <th class="px-3 py-4 text-center bg-purple-50 text-purple-600">S. Content</th>
                                    <th class="px-3 py-4 text-center bg-purple-50 text-purple-600">S. Partic.</th>
                                    <th class="px-4 py-4 text-center bg-purple-100 text-purple-800 border-x">Speaking</th>
                                    <th class="px-3 py-4 text-center">Reading</th>
                                    <th class="px-3 py-4 text-center">Spelling</th>
                                    <th class="px-4 py-4 text-center bg-blue-50 text-blue-700">Avg. Score</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm text-gray-700 bg-white">
                                @foreach($studentData as $index => $student)
                                <tr class="hover:bg-gray-50 transition-colors group">
                                    <td class="px-6 py-4 text-center text-gray-500 font-medium">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 font-bold text-gray-900">{{ $student['name'] }}</td>
                                    
                                    @foreach(['vocabulary', 'grammar', 'listening'] as $skill)
                                    <td class="px-3 py-3 text-center border-l">
                                        <span x-show="!isEditing" class="font-medium text-gray-700">{{ $student['written'][$skill] ?? '-' }}</span>
                                        <input x-show="isEditing" type="number" min="0" max="100" name="grades[{{ $student['id'] }}][{{ $skill }}]"
                                               value="{{ $student['written'][$skill] ?? '' }}"
                                               class="w-14 h-8 text-center border-gray-300 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 text-xs p-1">
                                    </td>
                                    @endforeach

                                    @foreach(['content', 'participation'] as $speakingPart)
                                    <td class="px-3 py-3 text-center bg-purple-50/50">
                                        <span x-show="!isEditing" class="font-bold text-purple-700">{{ $student['speaking'][$speakingPart] ?? '-' }}</span>
                                        <input x-show="isEditing" type="number" min="0" max="50" name="grades[{{ $student['id'] }}][speaking_{{ $speakingPart }}]"
                                               value="{{ $student['speaking'][$speakingPart] ?? '' }}"
                                               class="w-14 h-8 text-center border-purple-200 rounded-lg bg-white focus:ring-2 focus:ring-purple-500 text-xs p-1">
                                    </td>
                                    @endforeach

                                    <td class="px-4 py-3 text-center bg-purple-100/50 font-black text-purple-900 border-x">
                                        {{ $student['speaking']['total'] > 0 ? $student['speaking']['total'] : '-' }}
                                    </td>

                                    @foreach(['reading', 'spelling'] as $skill)
                                    <td class="px-3 py-3 text-center border-l">
                                        <span x-show="!isEditing" class="font-medium text-gray-700">{{ $student['written'][$skill] ?? '-' }}</span>
                                        <input x-show="isEditing" type="number" min="0" max="100" name="grades[{{ $student['id'] }}][{{ $skill }}]"
                                               value="{{ $student['written'][$skill] ?? '' }}"
                                               class="w-14 h-8 text-center border-gray-300 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 text-xs p-1">
                                    </td>
                                    @endforeach

                                    <td class="px-4 py-3 text-center bg-blue-50/30">
                                        <div class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-blue-600 text-white text-[11px] font-black shadow-md">
                                            {{ $student['avg_score'] ?? '-' }}
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>