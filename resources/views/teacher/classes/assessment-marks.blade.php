<x-app-layout>
    
    {{-- HAPUS SLOT HEADER AGAR BREADCRUMB BISA DI BAWAH NAVBAR --}}
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
                           <svg class="w-3 h-3 mr-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
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
                            <span class="ml-1 text-sm font-bold text-gray-800 md:ml-2">Input Marks</span>
                        </div>
                    </li>
                </ol>
            </nav>

            {{-- 2. HEADER INFO ASSESSMENT --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800">
                            @if($assessment->type == 'mid') Mid Term Exam @else Final Exam @endif
                        </h2>
                        <div class="flex items-center mt-2 text-gray-500 text-sm">
                            <i class="far fa-calendar-alt mr-2 text-gray-400"></i>
                            Date: {{ \Carbon\Carbon::parse($assessment->date)->format('d F Y') }}
                        </div>
                    </div>
                    
                    <div class="flex items-center bg-purple-50 px-5 py-3 rounded-xl border border-purple-100">
                        <div class="mr-3 p-2 bg-purple-100 rounded-lg text-purple-600">
                            <i class="fas fa-chalkboard"></i>
                        </div>
                        <div>
                            <p class="text-xs text-purple-600 uppercase tracking-wide font-semibold">Class</p>
                            <p class="text-lg font-bold text-purple-800">{{ $class->name }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. FORM INPUT NILAI --}}
            <form action="{{ route('teacher.classes.assessment.update', [$class->id, $assessment->id]) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse min-w-max">
                            
                            {{-- TABLE HEADERS --}}
                            <thead class="bg-gray-50 text-[10px] text-gray-500 font-bold uppercase border-b border-gray-200 tracking-widest">
                                <tr>
                                    <th class="px-6 py-4 w-16 text-center">No</th>
                                    <th class="px-6 py-4 min-w-[200px]">Student Name</th>
                                    <th class="px-3 py-4 text-center border-l border-gray-100">Vocab</th>
                                    <th class="px-3 py-4 text-center">Grammar</th>
                                    <th class="px-3 py-4 text-center border-r border-gray-100">Listening</th>
                                    
                                    {{-- Speaking Section --}}
                                    <th class="px-3 py-4 text-center bg-purple-50 text-purple-600 border-l border-purple-100">S. Content</th>
                                    <th class="px-3 py-4 text-center bg-purple-50 text-purple-600 border-r border-purple-100">S. Partic.</th>
                                    <th class="px-4 py-4 text-center bg-purple-100 text-purple-800 font-black border-r border-purple-200">Speaking</th>
                                    
                                    <th class="px-3 py-4 text-center">Reading</th>
                                    <th class="px-3 py-4 text-center border-r border-gray-100">Spelling</th>
                                    
                                    <th class="px-4 py-4 text-center bg-blue-50 text-blue-700 border-l border-blue-100">Avg. Score</th>
                                    <th class="px-4 py-4 text-center bg-green-50 text-green-700">Predicate</th>
                                </tr>
                            </thead>
                            
                            {{-- TABLE BODY --}}
                            <tbody class="divide-y divide-gray-100 text-sm text-gray-700 bg-white">
                                @forelse($students as $index => $student)
                                <tr class="hover:bg-gray-50 transition-colors group"
                                    x-data="{
                                        vocab: '{{ $student->form->vocabulary ?? '' }}',
                                        grammar: '{{ $student->form->grammar ?? '' }}',
                                        listening: '{{ $student->form->listening ?? '' }}',
                                        reading: '{{ $student->form->reading ?? '' }}',
                                        spelling: '{{ $student->form->spelling ?? '' }}',
                                        
                                        s_content: '{{ $student->form->speaking_content ?? '' }}', 
                                        s_partic: '{{ $student->form->speaking_participation ?? '' }}',

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
                                                if (val !== '' && val !== null && val !== 0 && val !== '0') {
                                                    total += parseInt(val);
                                                    count++;
                                                }
                                            });
                                            // Jika belum ada nilai sama sekali
                                            if (count === 0) return '-';
                                            
                                            // Pembagi tetap 6 komponen (standard) atau dinamis? 
                                            // Biasanya rata-rata dibagi jumlah mata pelajaran. 
                                            // Di sini kita bagi dengan jumlah komponen yg diisi (count) atau fix 6.
                                            // Asumsi: Dibagi 6 mapel.
                                            return Math.round(total / 6); 
                                        },

                                        get predicate() {
                                            let score = this.average;
                                            if (score === '-') return '-';
                                            if (score >= 90) return 'Outstanding';
                                            if (score >= 80) return 'Distinction';
                                            if (score >= 70) return 'Credit';
                                            if (score >= 60) return 'Pass'; // Tambahan standard
                                            if (score >= 50) return 'Acceptable';
                                            return 'Fail';
                                        },

                                        get predicateColor() {
                                            let p = this.predicate;
                                            if (p === 'Outstanding')    return 'bg-purple-100 text-purple-700 border-purple-200';
                                            if (p === 'Distinction')    return 'bg-blue-100 text-blue-700 border-blue-200';
                                            if (p === 'Credit')         return 'bg-green-100 text-green-700 border-green-200';
                                            if (p === 'Pass')           return 'bg-teal-100 text-teal-700 border-teal-200';
                                            if (p === 'Acceptable')     return 'bg-yellow-100 text-yellow-700 border-yellow-200';
                                            return 'bg-red-100 text-red-700 border-red-200';
                                        }
                                    }"
                                >
                                    {{-- No --}}
                                    <td class="px-6 py-4 text-center text-gray-500 font-medium">{{ $index + 1 }}</td>
                                    
                                    {{-- Student Name --}}
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xs mr-3 border border-blue-200">
                                                {{ substr($student->name, 0, 2) }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-bold text-gray-900">{{ $student->name }}</div>
                                                <div class="text-xs text-gray-500 font-mono">{{ $student->student_number ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- INPUTS --}}
                                    <td class="px-3 py-3 text-center border-l border-gray-100">
                                        <input type="number" min="0" max="100" x-model="vocab" @input="vocab = limit($el.value, 100)" name="marks[{{ $student->id }}][vocabulary]" class="w-14 h-9 text-center border-gray-300 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 text-sm p-1 transition-all">
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <input type="number" min="0" max="100" x-model="grammar" @input="grammar = limit($el.value, 100)" name="marks[{{ $student->id }}][grammar]" class="w-14 h-9 text-center border-gray-300 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 text-sm p-1 transition-all">
                                    </td>
                                    <td class="px-3 py-3 text-center border-r border-gray-100">
                                        <input type="number" min="0" max="100" x-model="listening" @input="listening = limit($el.value, 100)" name="marks[{{ $student->id }}][listening]" class="w-14 h-9 text-center border-gray-300 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 text-sm p-1 transition-all">
                                    </td>

                                    {{-- Speaking (Purple BG) --}}
                                    <td class="px-3 py-3 text-center bg-purple-50/30 border-l border-purple-50">
                                        <input type="number" min="0" max="50" x-model="s_content" @input="s_content = limit($el.value, 50)" name="marks[{{ $student->id }}][speaking_content]" class="w-14 h-9 text-center border-purple-200 rounded-lg bg-white focus:ring-2 focus:ring-purple-500 text-sm p-1 transition-all font-medium text-purple-700" placeholder="50">
                                    </td>
                                    <td class="px-3 py-3 text-center bg-purple-50/30 border-r border-purple-50">
                                        <input type="number" min="0" max="50" x-model="s_partic" @input="s_partic = limit($el.value, 50)" name="marks[{{ $student->id }}][speaking_participation]" class="w-14 h-9 text-center border-purple-200 rounded-lg bg-white focus:ring-2 focus:ring-purple-500 text-sm p-1 transition-all font-medium text-purple-700" placeholder="50">
                                    </td>
                                    <td class="px-4 py-3 text-center bg-purple-100/50 font-black text-purple-900 border-r border-purple-200 text-lg">
                                        <span x-text="speakingTotal > 0 ? speakingTotal : '-'"></span>
                                        <input type="hidden" name="marks[{{ $student->id }}][speaking]" x-model="speakingTotal"> 
                                    </td>

                                    {{-- Reading & Spelling --}}
                                    <td class="px-3 py-3 text-center">
                                        <input type="number" min="0" max="100" x-model="reading" @input="reading = limit($el.value, 100)" name="marks[{{ $student->id }}][reading]" class="w-14 h-9 text-center border-gray-300 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 text-sm p-1 transition-all">
                                    </td>
                                    <td class="px-3 py-3 text-center border-r border-gray-100">
                                        <input type="number" min="0" max="100" x-model="spelling" @input="spelling = limit($el.value, 100)" name="marks[{{ $student->id }}][spelling]" class="w-14 h-9 text-center border-gray-300 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 text-sm p-1 transition-all">
                                    </td>

                                    {{-- Results --}}
                                    <td class="px-4 py-3 text-center bg-blue-50/30 border-l border-blue-50">
                                        <div class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-blue-600 text-white text-xs font-black shadow-md shadow-blue-200">
                                            <span x-text="average"></span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center bg-gray-50/30">
                                        <span class="px-3 py-1 rounded-md border text-[10px] font-bold uppercase tracking-wide shadow-sm transition-all duration-300 block w-full"
                                            :class="predicateColor" 
                                            x-text="predicate">
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="12" class="px-6 py-12 text-center text-gray-500">
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
                        <a href="{{ route('teacher.classes.detail', $class->id) }}" class="inline-flex justify-center items-center bg-white border border-gray-300 text-gray-700 px-6 py-2.5 rounded-xl hover:bg-gray-50 transition shadow-sm font-semibold text-sm">
                            Cancel
                        </a>
                        
                        <button type="submit" class="inline-flex justify-center items-center bg-purple-600 text-white px-8 py-2.5 rounded-xl hover:bg-purple-700 transition shadow-lg shadow-purple-200 font-semibold text-sm">
                            <i class="fas fa-save mr-2"></i> Save Marks
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>