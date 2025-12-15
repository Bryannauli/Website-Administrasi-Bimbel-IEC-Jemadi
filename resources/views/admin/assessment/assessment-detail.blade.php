<x-app-layout>
    <x-slot name="header"></x-slot>

    {{-- 
        SETUP ALPINE JS UTAMA
        State: isEditing (Mode Edit/Baca), assessmentType (mid/final)
        Logika: Jika ada request 'mode=edit' ATAU ada error validasi, paksa masuk mode EDIT.
    --}}
    <div class="py-6" x-data="{ 
        isEditing: {{ (request('mode') == 'edit' || $errors->any()) ? 'true' : 'false' }},
        assessmentType: '{{ $type }}' 
    }">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- 1. BREADCRUMB NAVIGATION --}}
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

            {{-- 2. HEADER TITLE & STATUS BADGES --}}
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                <div>
                    <h2 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                        Assessment Details: {{ ucfirst($type) }}
                    </h2>
                    <p class="text-gray-500 text-sm mt-1">Class: <span class="font-bold text-gray-800">{{ $class->name }}</span></p>
                </div>
                
                {{-- FORM TERSEMBUNYI UNTUK QUICK ACTION (Read Mode) --}}
                <form id="quickStatusForm" method="POST" action="{{ route('admin.classes.assessment.storeOrUpdateGrades', ['classId' => $class->id, 'type' => $type]) }}" style="display: none;">
                    @csrf
                    <input type="hidden" name="action_type" value=""> 
                </form>

                {{-- A. READ MODE ACTIONS (Hanya Tampil saat !isEditing) --}}
                <div x-show="!isEditing" class="flex items-center gap-2" x-transition>
                    
                    {{-- CASE 1: DRAFT (Guru sedang kerja) --}}
                    @if($session->status === 'draft')
                        <span class="px-4 py-2 bg-yellow-100 text-yellow-700 text-sm font-bold rounded-lg border border-yellow-200 shadow-sm flex items-center gap-2 cursor-help" title="Waiting for teacher to submit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            Draft (Teacher working)
                        </span>
                    @endif

                    {{-- CASE 2: SUBMITTED (Siap Review) --}}
                    @if($session->status === 'submitted')
                        {{-- Tombol Masuk Mode Edit --}}
                        <button type="button" 
                                @click="isEditing = true"
                                class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-lg transition shadow-md flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            Review & Edit
                        </button>

                        {{-- Tombol Quick Approve --}}
                        <button type="submit"
                                form="quickStatusForm"
                                name="action_type" 
                                value="finalize_quick"
                                onclick="return confirmFinalize(document.getElementById('quickStatusForm'))"
                                class="px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white text-sm font-bold rounded-lg transition shadow-md flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Approve & Finalize
                        </button>
                    @endif
                    
                    {{-- CASE 3: FINAL (Locked) --}}
                    @if($session->status === 'final')
                        <span class="px-5 py-2.5 bg-purple-100 text-purple-700 text-sm font-bold rounded-lg border border-purple-200 shadow-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Finalized (Locked)
                        </span>

                        {{-- Print Button --}}
                        <button type="button" 
                                onclick="alert('Fungsi print akan diimplementasikan di sini.')"
                                class="px-4 py-2.5 bg-gray-100 text-gray-700 hover:bg-gray-200 border border-gray-300 font-bold rounded-lg transition shadow-sm flex items-center gap-2"
                                title="Print Final Report">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Print
                        </button>
                    @endif

                    {{-- Quick Revert (Only for Submitted/Final) --}}
                    @if($session->status === 'submitted' || $session->status === 'final')
                        <button type="submit" 
                                form="quickStatusForm"
                                name="action_type" 
                                value="draft_quick"
                                title="Send back to teacher (Revert to Draft)"
                                onclick="return confirmRevert(document.getElementById('quickStatusForm'))"
                                class="px-4 py-2.5 bg-red-100 text-red-600 hover:bg-red-200 border border-red-200 text-sm font-bold rounded-lg transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                            Revert to Draft
                        </button>
                    @endif
                </div>
            </div>

            {{-- 3. MAIN FORM INPUT NILAI --}}
            <form id="assessmentForm" method="POST" action="{{ route('admin.classes.assessment.storeOrUpdateGrades', ['classId' => $class->id, 'type' => $type]) }}">
                @csrf
                
                {{-- A. CONFIGURATION BOX (EDIT MODE ONLY) --}}
                <div x-show="isEditing" x-transition class="bg-white border border-gray-200 p-6 rounded-2xl mb-6 shadow-sm">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        {{-- Written Test Config --}}
                        <div class="space-y-4">
                            <h3 class="text-xs font-bold text-blue-600 uppercase tracking-widest pb-1 border-b border-gray-100">Written Test Info</h3>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">Exam Date <span class="text-red-500">*</span></label>
                                <input type="date" name="written_date" value="{{ old('written_date', $session->date) }}"
                                       class="block w-full rounded-xl border border-gray-300 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 sm:text-sm transition-all @error('written_date') border-red-500 @enderror">
                                @error('written_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        {{-- Speaking Test Config --}}
                        <div class="space-y-4 border-l-0 md:border-l md:pl-8 border-gray-100">
                            <h3 class="text-xs font-bold text-purple-600 uppercase tracking-widest pb-1 border-b border-gray-100">Speaking Test Info</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">Test Date <span class="text-red-500">*</span></label>
                                    <input type="date" name="speaking_date" value="{{ old('speaking_date', $speakingTest->date ?? '') }}"
                                           class="block w-full rounded-xl border border-gray-300 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-purple-500 sm:text-sm transition-all @error('speaking_date') border-red-500 @enderror">
                                    @error('speaking_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">Interviewer <span class="text-red-500">*</span></label>
                                    <select name="interviewer_id" class="block w-full rounded-xl border border-gray-300 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-purple-500 sm:text-sm transition-all @error('interviewer_id') border-red-500 @enderror">
                                        @if(!$currentInterviewerId)
                                            <option value="">-- Select --</option>
                                        @endif
                                        @foreach($allTeachers as $teacher)
                                            <option value="{{ $teacher->id }}" {{ old('interviewer_id', $currentInterviewerId) == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('interviewer_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
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

                {{-- B. READ-ONLY SUMMARY (READ MODE ONLY) --}}
                <div x-show="!isEditing" class="space-y-4 mb-8" x-transition>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Form Teacher Info --}}
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
                                    {{ $session->date ? \Carbon\Carbon::parse($session->date)->format('d F Y') : '-' }}
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
                        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3 border-l-4 border-l-green-700">
                            <div class="p-2.5 bg-green-50/80 text-green-700 rounded-xl">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase">Speaking Interviewer</p>
                                <p class="text-sm font-bold text-gray-800">{{ $speakingTest->interviewer->name ?? '-' }}</p>
                            </div>
                        </div>
                        {{-- Topic --}}
                        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3 border-l-4 border-l-orange-700">
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

                {{-- 4. GRADES TABLE --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-6">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse min-w-max">
                            <thead class="bg-gray-50 text-[10px] text-gray-500 font-bold uppercase border-b border-gray-200 tracking-widest">
                                <tr>
                                    <th class="px-6 py-4 w-16 text-center">No</th>
                                    <th class="px-4 py-4 w-24">Student ID</th>
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
                                    <th class="px-4 py-4 text-center bg-green-50 text-green-700 rounded-tr-lg">Predicate</th>
                                </tr>
                            </thead>
                            
                            {{-- ALPINE JS LOGIC --}}
                            <tbody class="divide-y divide-gray-100 text-sm text-gray-700 bg-white">
                                @foreach($studentData as $index => $student)
                                <tr class="hover:bg-gray-50 transition-colors group"
                                    x-data="{
                                        vocab: '{{ old("grades.{$student['id']}.vocabulary", $student['written']['vocabulary'] ?? '') }}',
                                        grammar: '{{ old("grades.{$student['id']}.grammar", $student['written']['grammar'] ?? '') }}',
                                        listening: '{{ old("grades.{$student['id']}.listening", $student['written']['listening'] ?? '') }}',
                                        reading: '{{ old("grades.{$student['id']}.reading", $student['written']['reading'] ?? '') }}',
                                        spelling: '{{ old("grades.{$student['id']}.spelling", $student['written']['spelling'] ?? '') }}',
                                        s_content: '{{ old("grades.{$student['id']}.speaking_content", $student['speaking']['content'] ?? '') }}',
                                        s_partic: '{{ old("grades.{$student['id']}.speaking_participation", $student['speaking']['participation'] ?? '') }}',
                                        
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
                                    <input type="hidden" name="grades[{{ $student['id'] }}][student_id]" value="{{ $student['id'] }}">
                                    <input type="hidden" name="grades[{{ $student['id'] }}][form_id]" value="{{ $student['written']['form_id'] ?? '' }}">

                                    <td class="px-6 py-4 text-center text-gray-500 font-medium">{{ $index + 1 }}</td>
                                    <td class="px-4 py-4 font-mono text-xs text-gray-500">{{ $student['student_number'] ?? '-' }}</td>
                                    <td class="px-6 py-4 font-bold text-gray-900">{{ $student['name'] }}</td>
                                    
                                    {{-- INPUT FIELDS --}}
                                    <td class="px-3 py-3 text-center border-l">
                                        <span x-show="!isEditing" class="font-medium text-gray-700" x-text="vocab || '-'"></span>
                                        <input x-show="isEditing" x-model="vocab" @input="vocab = limit($el.value, 100); $el.value = vocab" type="number" min="0" max="100" name="grades[{{ $student['id'] }}][vocabulary]" class="w-14 h-8 text-center border-gray-300 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 text-xs p-1">
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <span x-show="!isEditing" class="font-medium text-gray-700" x-text="grammar || '-'"></span>
                                        <input x-show="isEditing" x-model="grammar" @input="grammar = limit($el.value, 100); $el.value = grammar" type="number" min="0" max="100" name="grades[{{ $student['id'] }}][grammar]" class="w-14 h-8 text-center border-gray-300 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 text-xs p-1">
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <span x-show="!isEditing" class="font-medium text-gray-700" x-text="listening || '-'"></span>
                                        <input x-show="isEditing" x-model="listening" @input="listening = limit($el.value, 100); $el.value = listening" type="number" min="0" max="100" name="grades[{{ $student['id'] }}][listening]" class="w-14 h-8 text-center border-gray-300 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 text-xs p-1">
                                    </td>
                                    <td class="px-3 py-3 text-center bg-purple-50/50">
                                        <span x-show="!isEditing" class="font-bold text-purple-700" x-text="s_content || '-'"></span>
                                        <input x-show="isEditing" x-model="s_content" @input="s_content = limit($el.value, 50); $el.value = s_content" type="number" min="0" max="50" name="grades[{{ $student['id'] }}][speaking_content]" class="w-14 h-8 text-center border-purple-200 rounded-lg bg-white focus:ring-2 focus:ring-purple-500 text-xs p-1">
                                    </td>
                                    <td class="px-3 py-3 text-center bg-purple-50/50">
                                        <span x-show="!isEditing" class="font-bold text-purple-700" x-text="s_partic || '-'"></span>
                                        <input x-show="isEditing" x-model="s_partic" @input="s_partic = limit($el.value, 50); $el.value = s_partic" type="number" min="0" max="50" name="grades[{{ $student['id'] }}][speaking_participation]" class="w-14 h-8 text-center border-purple-200 rounded-lg bg-white focus:ring-2 focus:ring-purple-500 text-xs p-1">
                                    </td>
                                    <td class="px-4 py-3 text-center bg-purple-100/50 font-black text-purple-900 border-x">
                                        <span x-text="speakingTotal > 0 ? speakingTotal : '-'"></span>
                                    </td>
                                    <td class="px-3 py-3 text-center border-l">
                                        <span x-show="!isEditing" class="font-medium text-gray-700" x-text="reading || '-'"></span>
                                        <input x-show="isEditing" x-model="reading" @input="reading = limit($el.value, 100); $el.value = reading" type="number" min="0" max="100" name="grades[{{ $student['id'] }}][reading]" class="w-14 h-8 text-center border-gray-300 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 text-xs p-1">
                                    </td>
                                    <td class="px-3 py-3 text-center border-l">
                                        <span x-show="!isEditing" class="font-medium text-gray-700" x-text="spelling || '-'"></span>
                                        <input x-show="isEditing" x-model="spelling" @input="spelling = limit($el.value, 100); $el.value = spelling" type="number" min="0" max="100" name="grades[{{ $student['id'] }}][spelling]" class="w-14 h-8 text-center border-gray-300 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 text-xs p-1">
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
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- 5. BOTTOM ACTIONS (HANYA MUNCUL SAAT EDIT MODE) --}}
                <div x-show="isEditing" x-transition class="flex justify-end items-center mt-6 p-4 bg-white rounded-2xl shadow-sm border border-gray-200">
                    <div class="flex items-center gap-3">
                        
                        {{-- 1. TOMBOL CANCEL --}}
                        <a href="{{ route('admin.classes.assessment.detail', ['classId' => $class->id, 'type' => $type]) }}"
                           class="px-4 py-2.5 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-bold rounded-lg transition shadow-sm">
                            Cancel
                        </a>

                        {{-- 2. TOMBOL SAVE ONLY (WARNA HIJAU) --}}
                        <button type="submit" 
                                name="action_type" 
                                value="save"
                                class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-bold rounded-lg transition shadow-md flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                            Save Only
                        </button>

                        {{-- 3. TOMBOL APPROVE & FINALIZE (Ungu - Hanya jika status belum final) --}}
                        @if($session->status !== 'final')
                        <button type="submit" 
                                name="action_type" 
                                value="finalize" 
                                onclick="return confirmFinalize(document.getElementById('assessmentForm'))"
                                class="px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white text-sm font-bold rounded-lg transition shadow-md flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Approve & Finalize
                        </button>
                        @endif

                        {{-- 4. TOMBOL REVERT TO DRAFT (Paling Kanan) --}}
                        @if($session->status === 'submitted' || $session->status === 'final')
                        <button type="submit" 
                                name="action_type" 
                                value="draft"
                                title="Send back to teacher"
                                onclick="return confirmRevert(document.getElementById('assessmentForm'))"
                                class="px-4 py-2.5 bg-red-100 text-red-600 hover:bg-red-200 border border-red-200 text-sm font-bold rounded-lg transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                            Revert to Draft
                        </button>
                        @endif
                    </div>
                </div>

            </form>
        </div>
    </div>

    {{-- SCRIPTS (SweetAlert2 & Enter Key Navigation & Confirmation) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // --- 1. NEW CONFIRMATION FUNCTIONS ---

        function confirmFinalize(formElement) {
            Swal.fire({
                title: 'Approve & Finalize?',
                text: "Are you sure you want to FINALIZE this assessment? This will lock the grades and set the official status to FINAL.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#9333ea', // Ungu (Purple-600)
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, Finalize!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Processing...',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    });
                    // Set action_type pada input hidden form yang relevan
                    // Jika form yg disubmit adalah form utama (edit mode), valuenya 'finalize'
                    // Jika form yg disubmit adalah quick form (read mode), valuenya 'finalize_quick'
                    const actionValue = formElement.id === 'quickStatusForm' ? 'finalize_quick' : 'finalize';
                    
                    // Cek apakah input hidden sudah ada, jika belum buat baru (untuk quickForm biasanya sudah ada di HTML)
                    let input = formElement.querySelector('input[name="action_type"]');
                    if (!input) {
                        input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'action_type';
                        formElement.appendChild(input);
                    }
                    input.value = actionValue;
                    
                    formElement.submit();
                }
            });
            return false;
        }

        function confirmRevert(formElement) {
            Swal.fire({
                title: 'Revert to Draft?',
                text: "This action will set the status back to DRAFT. The teacher will be able to edit the grades again.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#dc2626', // Merah (Red-600)
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, Revert to Draft'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Processing...',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    });
                    const actionValue = formElement.id === 'quickStatusForm' ? 'draft_quick' : 'draft';

                    let input = formElement.querySelector('input[name="action_type"]');
                    if (!input) {
                        input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'action_type';
                        formElement.appendChild(input);
                    }
                    input.value = actionValue;

                    formElement.submit();
                }
            });
            return false;
        }


        // --- 2. MAIN DOM CONTENT LOADED ---
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('assessmentForm');

            // A. Mencegah submit saat Enter ditekan pada input field (Pindah Fokus)
            if(form){
                form.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' && e.target.tagName === 'INPUT') {
                        e.preventDefault(); 
                        
                        const inputs = Array.from(form.querySelectorAll('input:not([type="hidden"]):not([disabled])'));
                        const index = inputs.indexOf(e.target);

                        if (index > -1 && index < inputs.length - 1) {
                            const nextInput = inputs[index + 1];
                            nextInput.focus();
                            nextInput.select(); 
                        }
                    }
                });
            }

            // B. SweetAlert Notifications
            const successMessage = <?php echo json_encode(session('success')); ?>;
            const errorMessage   = <?php echo json_encode(session('error')); ?>;
            const validationErrors = <?php echo json_encode($errors->all()); ?>;

            if (successMessage) {
                Swal.fire({
                    icon: 'success',
                    title: 'Saved!',
                    text: successMessage,
                    timer: 2000,
                    showConfirmButton: false
                });
            }

            if (validationErrors.length > 0) {
                let errorListHtml = '<div class="text-left text-sm"><p class="mb-2 font-bold">Please check the red fields:</p><ul class="list-disc pl-5 text-red-600">';
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
                    confirmButtonText: 'OK, I will fix it'
                });
            }

            if (errorMessage) {
                Swal.fire({
                    icon: 'error',
                    title: 'System Error',
                    text: errorMessage,
                });
            }
        });
    </script>
</x-app-layout>