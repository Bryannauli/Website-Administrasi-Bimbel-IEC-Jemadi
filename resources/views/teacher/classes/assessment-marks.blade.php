<x-app-layout>
    <x-slot name="header"></x-slot>

    {{-- SETUP ALPINE JS --}}
    <div class="py-6" x-data="{ 
        isEditing: {{ $errors->any() ? 'true' : 'false' }},
        isDraft: {{ $assessment->status === 'draft' ? 'true' : 'false' }}
    }">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- 1. BREADCRUMB --}}
            <nav class="flex mb-5" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('teacher.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-blue-600 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 001 1v2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
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
                            <a href="{{ route('teacher.classes.detail', $class->id) }}" class="ml-1 text-sm font-medium text-gray-500 hover:text-blue-600 md:ml-2 truncate max-w-[100px] sm:max-w-xs">{{ $class->name }}</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            <span class="ml-1 text-sm font-medium text-gray-900 md:ml-2 uppercase">Assessment</span>
                        </div>
                    </li>
                </ol>
            </nav>

            {{-- 2. HEADER TITLE --}}
            <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                        Assessment: {{ $assessment->type == 'mid' ? 'Mid Term' : 'Final Exam' }}
                    </h2>
                    
                    {{-- Nama Kelas --}}
                    <p class="text-gray-500 text-sm mt-1">
                        Class: <span class="font-bold text-gray-800">{{ $class->name }}</span>
                    </p>

                    {{-- Status Badge (Pindah ke Bawah Nama Kelas) --}}
                    <div class="mt-1.5 flex items-center">
                        <span class="text-gray-500 text-sm mr-2">Status:</span>
                        <span class="uppercase font-bold px-2.5 py-0.5 rounded text-[10px] tracking-wider
                            {{ $assessment->status === 'draft' ? 'bg-yellow-100 text-yellow-700' : '' }}
                            {{ $assessment->status === 'submitted' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $assessment->status === 'final' ? 'bg-purple-100 text-purple-700' : '' }}">
                            {{ $assessment->status }}
                        </span>
                    </div>
                </div>
                
                {{-- TOMBOL EDIT UTAMA (Hanya Muncul jika Draft dan tidak sedang edit) --}}
                <div x-show="!isEditing && isDraft" x-transition>
                    <button @click="isEditing = true" 
                            class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-lg transition shadow-md flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        Edit Grades & Info
                    </button>
                </div>
            </div>

            {{-- 3. FORM --}}
            <form id="marksForm" action="{{ route('teacher.classes.assessment.update', [$class->id, $assessment->id]) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- A. READ ONLY INFO --}}
                <div x-show="!isEditing" class="space-y-4 mb-8" x-transition>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- FORM TEACHER CARD --}}
                        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3 border-l-4 border-l-yellow-600">
                            <div class="p-2.5 bg-yellow-50 text-yellow-600 rounded-xl">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h-4v-6h-2v6H7v-6H5v6H3m14 0h-4v-6h-2v6H7v-6H5v6H3M12 4a4 4 0 11-8 0 4 4 0 018 0zm4 0a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase">Form Teacher</p>
                                <p class="text-sm font-bold text-gray-800">{{ $class->formTeacher->name ?? '-' }}</p>
                            </div>
                        </div>

                        {{-- Written Exam Info --}}
                        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3 border-l-4 border-l-blue-500">
                            <div class="p-2.5 bg-blue-50/80 text-blue-700 rounded-xl">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase">Written Exam Date</p>
                                <p class="text-sm font-bold text-gray-800">
                                    {{ $assessment->date ? \Carbon\Carbon::parse($assessment->date)->format('d F Y') : '-' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                         {{-- Speaking Date --}}
                        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3 border-l-4 border-l-purple-500">
                            <div class="p-2.5 bg-purple-50/80 text-purple-700 rounded-xl">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase">Speaking Date</p>
                                <p class="text-sm font-bold text-gray-800">
                                    {{ optional($speakingTest)->date ? \Carbon\Carbon::parse($speakingTest->date)->format('d F Y') : '-' }}
                                </p>
                            </div>
                        </div>
                        {{-- Interviewer --}}
                        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3 border-l-4 border-l-green-600">
                            <div class="p-2.5 bg-green-50/80 text-green-700 rounded-xl">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase">Interviewer</p>
                                <p class="text-sm font-bold text-gray-800">{{ $speakingTest->interviewer->name ?? '-' }}</p>
                            </div>
                        </div>
                        {{-- Topic --}}
                        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3 border-l-4 border-l-orange-600">
                            <div class="p-2.5 bg-orange-50/80 text-orange-700 rounded-xl">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase">Speaking Topic</p>
                                <p class="text-sm font-bold text-gray-800">{{ optional($speakingTest)->topic ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- B. EDIT CONFIGURATION BOX (Hidden if not editing) --}}
                <div x-show="isEditing" x-transition class="bg-white border border-gray-200 p-6 rounded-2xl mb-6 shadow-sm">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        {{-- Written Test --}}
                        <div class="space-y-4">
                            <h3 class="text-xs font-bold text-blue-600 uppercase tracking-widest pb-1 border-b border-gray-100">Written Test Info</h3>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">Exam Date <span class="text-red-500">*</span></label>
                                <input type="date" name="written_date" value="{{ old('written_date', $assessment->date) }}"
                                       class="block w-full rounded-xl border border-gray-300 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-all @error('written_date') border-red-500 @enderror">
                                @error('written_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        
                        {{-- Speaking Test --}}
                        <div class="space-y-4 border-l-0 md:border-l md:pl-8 border-gray-100">
                            <h3 class="text-xs font-bold text-purple-600 uppercase tracking-widest pb-1 border-b border-gray-100">Speaking Test Info</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">Speaking Date</label>
                                    <input type="date" name="speaking_date" value="{{ old('speaking_date', $speakingTest->date ?? '') }}"
                                           class="block w-full rounded-xl border border-gray-300 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-purple-500 sm:text-sm transition-all">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">Interviewer</label>
                                    <select name="interviewer_id" class="block w-full rounded-xl border border-gray-300 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-purple-500 sm:text-sm transition-all">
                                        <option value="">-- Select Teacher --</option>
                                        @foreach($teachers as $t)
                                            <option value="{{ $t->id }}" {{ (old('interviewer_id', $speakingTest->interviewer_id ?? '') == $t->id) ? 'selected' : '' }}>
                                                {{ $t->name }}
                                            </option>
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

                {{-- C. GRADES TABLE --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-6">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse min-w-max">
                            <thead class="bg-gray-50 text-[10px] text-gray-500 font-bold uppercase border-b border-gray-200 tracking-widest">
                                <tr>
                                    <th class="px-6 py-4 w-16 text-center">No</th>
                                    <th class="px-4 py-4 w-24">ID</th>
                                    <th class="px-6 py-4 min-w-[180px]">Student Name</th>
                                    <th class="px-3 py-4 text-center border-l">Vocab</th>
                                    <th class="px-3 py-4 text-center">Grammar</th>
                                    <th class="px-3 py-4 text-center">Listening</th>
                                    <th class="px-3 py-4 text-center bg-purple-50 text-purple-600">S. Content</th>
                                    <th class="px-3 py-4 text-center bg-purple-50 text-purple-600">S. Partic.</th>
                                    <th class="px-4 py-4 text-center bg-purple-100 text-purple-800 border-x">Speaking</th>
                                    <th class="px-3 py-4 text-center">Reading</th>
                                    <th class="px-3 py-4 text-center">Spelling</th>
                                    <th class="px-4 py-4 text-center bg-blue-50 text-blue-700">Avg. Score</th>
                                    <th class="px-4 py-4 text-center bg-green-50 text-green-700 rounded-tr-lg">Predicate</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm text-gray-700 bg-white">
                                {{-- PERBAIKAN: Gunakan $studentData bukan $students --}}
                                @forelse($studentData as $index => $student)
                                <tr class="hover:bg-gray-50 transition-colors group
                                    {{-- Styling untuk Deleted/Quit --}}
                                    @if($student['deleted_at']) bg-gray-100 text-gray-500
                                    @elseif(!$student['is_active']) bg-red-50 text-red-800
                                    @else hover:bg-gray-50
                                    @endif"
                                    x-data="{
                                        vocab: '{{ old("marks.{$student['id']}.vocabulary", $student['written']['vocabulary'] ?? '') }}',
                                        grammar: '{{ old("marks.{$student['id']}.grammar", $student['written']['grammar'] ?? '') }}',
                                        listening: '{{ old("marks.{$student['id']}.listening", $student['written']['listening'] ?? '') }}',
                                        reading: '{{ old("marks.{$student['id']}.reading", $student['written']['reading'] ?? '') }}',
                                        spelling: '{{ old("marks.{$student['id']}.spelling", $student['written']['spelling'] ?? '') }}',
                                        s_content: '{{ old("marks.{$student['id']}.speaking_content", $student['speaking']['content'] ?? '') }}',
                                        s_partic: '{{ old("marks.{$student['id']}.speaking_participation", $student['speaking']['participation'] ?? '') }}',
                                        
                                        limit(val, max) {
                                            if (val === '') return ''; 
                                            let n = parseInt(val);
                                            if (n < 0) return 0;
                                            if (n > max) return max;
                                            return n;
                                        },
                                        get speakingTotal() {
                                            let c = parseInt(this.s_content) || 0;
                                            let p = parseInt(this.s_partic) || 0;
                                            if (this.s_content === '' && this.s_partic === '') return 0;
                                            return c + p;
                                        },
                                        get average() {
                                            let components = [
                                                this.vocab, this.grammar, this.listening, this.reading, this.spelling,
                                                (this.s_content === '' && this.s_partic === '') ? '' : this.speakingTotal
                                            ];
                                            let total = 0; let count = 0;
                                            components.forEach(val => {
                                                if (val !== '' && val !== null) {
                                                    total += parseInt(val);
                                                    count++;
                                                }
                                            });
                                            if (count === 0) return '-';
                                            return Math.round(total / count);
                                        },
                                        get predicate() {
                                            let score = this.average;
                                            if (score === '-') return '-';
                                            if (score >= 90) return 'Outstanding';
                                            if (score >= 80) return 'Distinction';
                                            if (score >= 70) return 'Credit';
                                            if (score >= 50) return 'Acceptable';
                                            if (score >= 40) return 'Unsatisfactory';
                                            return 'Insufficient';
                                        },
                                        get predicateColor() {
                                            let p = this.predicate;
                                            if (p === 'Outstanding')    return 'bg-purple-100 text-purple-700 border-purple-200';
                                            if (p === 'Distinction')    return 'bg-blue-100 text-blue-700 border-blue-200';
                                            if (p === 'Credit')         return 'bg-green-100 text-green-700 border-green-200';
                                            if (p === 'Acceptable')     return 'bg-yellow-100 text-yellow-700 border-yellow-200';
                                            if (p === 'Unsatisfactory') return 'bg-red-100 text-red-700 border-red-200';
                                            if (p === 'Insufficient')   return 'bg-gray-100 text-gray-600 border-gray-200';
                                            return 'bg-gray-50 text-gray-400 border-gray-100';
                                        }
                                    }"
                                >
                                    <td class="px-6 py-4 text-center text-gray-500 font-medium">{{ $index + 1 }}</td>
                                    <td class="px-4 py-4 font-mono text-xs text-gray-500">{{ $student['student_number'] ?? '-' }}</td>
                                    
                                    {{-- Student Name & Badges --}}
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span class="font-bold {{ $student['deleted_at'] ? 'text-gray-500 line-through' : ($student['is_active'] ? 'text-gray-900' : 'text-red-800') }}">
                                                {{ $student['name'] }}
                                            </span>
                                            
                                            {{-- Status Badges (Hanya Deleted & Quit) --}}
                                            <div class="flex flex-wrap gap-1 mt-1">
                                                @if($student['deleted_at'])
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-gray-200 text-gray-600 border border-gray-300 uppercase">
                                                        DELETED
                                                    </span>
                                                @elseif(!$student['is_active'])
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-white text-red-600 border border-red-200 uppercase">
                                                        QUIT
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    
                                    {{-- INPUT FIELDS --}}
                                    <td class="px-3 py-3 text-center border-l">
                                        <span x-show="!isEditing" class="font-medium opacity-80" x-text="vocab || '-'"></span>
                                        <input x-show="isEditing" x-model="vocab" @input="vocab = limit($el.value, 100); $el.value = vocab" type="number" min="0" max="100" name="marks[{{ $student['id'] }}][vocabulary]" class="w-14 h-8 text-center border-gray-300 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 text-xs p-1 shadow-sm">
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <span x-show="!isEditing" class="font-medium opacity-80" x-text="grammar || '-'"></span>
                                        <input x-show="isEditing" x-model="grammar" @input="grammar = limit($el.value, 100); $el.value = grammar" type="number" min="0" max="100" name="marks[{{ $student['id'] }}][grammar]" class="w-14 h-8 text-center border-gray-300 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 text-xs p-1 shadow-sm">
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <span x-show="!isEditing" class="font-medium opacity-80" x-text="listening || '-'"></span>
                                        <input x-show="isEditing" x-model="listening" @input="listening = limit($el.value, 100); $el.value = listening" type="number" min="0" max="100" name="marks[{{ $student['id'] }}][listening]" class="w-14 h-8 text-center border-gray-300 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 text-xs p-1 shadow-sm">
                                    </td>
                                    <td class="px-3 py-3 text-center bg-purple-50/30">
                                        <span x-show="!isEditing" class="font-bold text-purple-700" x-text="s_content || '-'"></span>
                                        <input x-show="isEditing" x-model="s_content" @input="s_content = limit($el.value, 50); $el.value = s_content" type="number" min="0" max="50" name="marks[{{ $student['id'] }}][speaking_content]" class="w-14 h-8 text-center border-purple-200 rounded-lg bg-white focus:ring-2 focus:ring-purple-500 text-xs p-1 shadow-sm">
                                    </td>
                                    <td class="px-3 py-3 text-center bg-purple-50/30">
                                        <span x-show="!isEditing" class="font-bold text-purple-700" x-text="s_partic || '-'"></span>
                                        <input x-show="isEditing" x-model="s_partic" @input="s_partic = limit($el.value, 50); $el.value = s_partic" type="number" min="0" max="50" name="marks[{{ $student['id'] }}][speaking_participation]" class="w-14 h-8 text-center border-purple-200 rounded-lg bg-white focus:ring-2 focus:ring-purple-500 text-xs p-1 shadow-sm">
                                    </td>
                                    <td class="px-4 py-3 text-center bg-purple-100/50 font-black text-purple-900 border-x">
                                        <span x-text="speakingTotal > 0 ? speakingTotal : '-'"></span>
                                        <input type="hidden" name="marks[{{ $student['id'] }}][speaking]" x-model="speakingTotal">
                                    </td>
                                    <td class="px-3 py-3 text-center border-l">
                                        <span x-show="!isEditing" class="font-medium opacity-80" x-text="reading || '-'"></span>
                                        <input x-show="isEditing" x-model="reading" @input="reading = limit($el.value, 100); $el.value = reading" type="number" min="0" max="100" name="marks[{{ $student['id'] }}][reading]" class="w-14 h-8 text-center border-gray-300 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 text-xs p-1 shadow-sm">
                                    </td>
                                    <td class="px-3 py-3 text-center border-l">
                                        <span x-show="!isEditing" class="font-medium opacity-80" x-text="spelling || '-'"></span>
                                        <input x-show="isEditing" x-model="spelling" @input="spelling = limit($el.value, 100); $el.value = spelling" type="number" min="0" max="100" name="marks[{{ $student['id'] }}][spelling]" class="w-14 h-8 text-center border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 text-xs p-1 shadow-sm">
                                    </td>
                                    
                                    <td class="px-4 py-3 text-center bg-blue-50/30">
                                        <div class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-blue-600 text-white text-[11px] font-black shadow-md">
                                            <span x-text="average"></span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center bg-gray-50/30 font-bold text-[10px] uppercase tracking-wide">
                                        <span class="px-2 py-1 rounded-md border transition-all duration-300" :class="predicateColor" x-text="predicate"></span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="13" class="px-6 py-12 text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="bg-gray-50 rounded-full p-4 mb-3 border border-gray-100">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                            </div>
                                            <p class="text-base font-medium text-gray-600">No students found in this class.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- 5. BOTTOM ACTIONS (Hanya saat isEditing) --}}
                <div x-show="isEditing" class="flex justify-end items-center mt-6 p-4 bg-white rounded-2xl shadow-sm border border-gray-200" x-transition>
                    <div class="flex items-center gap-3">
                        {{-- CANCEL BUTTON --}}
                        <a href="{{ route('teacher.classes.assessment.detail', ['classId' => $class->id, 'assessmentId' => $assessment->id]) }}"
                           class="px-4 py-2.5 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-bold rounded-lg transition shadow-sm">
                            Cancel
                        </a>

                        {{-- SAVE DRAFT BUTTON --}}
                        <button type="submit" name="action_type" value="save"
                                class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-bold rounded-lg transition shadow-sm flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                            Save Draft
                        </button>

                        {{-- SUBMIT BUTTON (Dengan SweetAlert) --}}
                        <button type="submit" name="action_type" value="submit"
                                onclick="confirmSubmit(event)"
                                class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-lg transition shadow-md flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Submit to Admin
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- SCRIPTS (SweetAlert & Logic) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // 1. CONFIRM SUBMIT FUNCTION
        function confirmSubmit(e) {
            e.preventDefault(); // Mencegah submit default
            const form = e.target.form; // Ambil form

            Swal.fire({
                title: 'Submit Assessment?',
                text: "Once submitted, you CANNOT edit grades anymore unless Admin reverts it.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5', // Indigo-600
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, Submit!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan loading state
                    Swal.fire({
                        title: 'Submitting...',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    });

                    // Pastikan action_type terkirim (karena button tidak disubmit secara native)
                    let input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'action_type';
                    input.value = 'submit';
                    form.appendChild(input);

                    form.submit();
                }
            });
        }

        // 2. NAVIGASI ENTER KEY (Pindah field saat Enter)
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('marksForm');
            if(form){
                form.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' && e.target.tagName === 'INPUT') {
                        e.preventDefault();
                        const inputs = Array.from(form.querySelectorAll('input:not([type="hidden"]):not([disabled])'));
                        const index = inputs.indexOf(e.target);
                        if (index > -1 && index < inputs.length - 1) {
                            inputs[index + 1].focus();
                            inputs[index + 1].select();
                        }
                    }
                });
            }

            // 3. FLASH MESSAGES (Sukses / Error)
            const successMessage = <?php echo json_encode(session('success')); ?>;
            const errorMessage   = <?php echo json_encode(session('error')); ?>;
            const validationErrors = <?php echo json_encode($errors->all()); ?>;

            if (successMessage) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: successMessage,
                    timer: 2000,
                    showConfirmButton: false
                });
            }

            if (validationErrors.length > 0) {
                let errorListHtml = '<div class="text-left text-sm"><ul class="list-disc pl-5 text-red-600">';
                validationErrors.slice(0, 3).forEach(error => {
                    errorListHtml += `<li>${error}</li>`;
                });
                if (validationErrors.length > 3) {
                    errorListHtml += `<li>... and ${validationErrors.length - 3} more errors.</li>`;
                }
                errorListHtml += '</ul></div>';

                Swal.fire({
                    icon: 'error',
                    title: 'Validation Failed',
                    html: errorListHtml,
                    confirmButtonText: 'OK'
                });
            }

            if (errorMessage) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage,
                });
            }
        });
    </script>
</x-app-layout>