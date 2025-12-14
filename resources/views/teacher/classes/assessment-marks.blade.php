@extends('layouts.teacher')

@section('title', 'Input Marks')

@section('breadcrumb')
<nav class="flex items-center space-x-2 text-sm">
    <a href="{{ route('teacher.dashboard') }}" class="text-gray-600 hover:text-gray-900">Dashboard</a>
    <i class="fas fa-chevron-right text-xs text-gray-400"></i>
    <a href="{{ route('teacher.classes.index') }}" class="text-gray-600 hover:text-gray-900">Class</a>
    <i class="fas fa-chevron-right text-xs text-gray-400"></i>
    <a href="{{ route('teacher.classes.detail', $class->id) }}" class="text-gray-600 hover:text-gray-900">Detail</a>
    <i class="fas fa-chevron-right text-xs text-gray-400"></i>
    <span class="text-gray-900 font-medium">Input Marks</span>
</nav>
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-3xl font-bold text-gray-800">
                    @if($assessment->type == 'mid') Mid Term Exam @else Final Exam @endif
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Date: {{ \Carbon\Carbon::parse($assessment->date)->format('d F Y') }}
                </p>
            </div>
            
            <div class="bg-purple-50 px-4 py-2 rounded-lg border border-purple-100">
                <p class="text-xs text-purple-600 uppercase tracking-wide font-semibold">Class</p>
                <p class="text-lg font-bold text-purple-800">{{ $class->name }}</p>
            </div>
        </div>
    </div>

    <form action="{{ route('teacher.classes.assessment.update', [$class->id, $assessment->id]) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-max">
                    {{-- HEADERS (Sama persis dengan Admin) --}}
                    <thead class="bg-gray-50 text-[10px] text-gray-500 font-bold uppercase border-b border-gray-200 tracking-widest">
                        <tr>
                            <th class="px-6 py-4 w-16 text-center">No</th>
                            <th class="px-6 py-4">Student Name</th>
                            <th class="px-3 py-4 text-center border-l">Vocab</th>
                            <th class="px-3 py-4 text-center">Grammar</th>
                            <th class="px-3 py-4 text-center">Listening</th>
                            {{-- Kolom Baru: Speaking Components --}}
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
                        @forelse($students as $index => $student)
                        {{-- 
                            ALPINE JS LOGIC 
                            Disini kita inisialisasi data nilai agar kalkulasi Average & Predikat berjalan otomatis (Real-time).
                        --}}
                        <tr class="hover:bg-gray-50 transition-colors group"
                            x-data="{
                                vocab: '{{ $student->form->vocabulary ?? '' }}',
                                grammar: '{{ $student->form->grammar ?? '' }}',
                                listening: '{{ $student->form->listening ?? '' }}',
                                reading: '{{ $student->form->reading ?? '' }}',
                                spelling: '{{ $student->form->spelling ?? '' }}',
                                
                                {{-- Asumsi: Anda sudah/akan menambah kolom speaking_content & speaking_participation di database --}}
                                s_content: '{{ $student->form->speaking_content ?? '' }}', 
                                s_partic: '{{ $student->form->speaking_participation ?? '' }}',

                                // Fungsi membatasi input max angka
                                limit(val, max) {
                                    if (val === '') return ''; 
                                    let n = parseInt(val);
                                    if (n < 0) return 0;
                                    if (n > max) return max;
                                    return n;
                                },

                                // Hitung Total Speaking (Content + Participation)
                                get speakingTotal() {
                                    let c = parseInt(this.s_content) || 0;
                                    let p = parseInt(this.s_partic) || 0;
                                    if (this.s_content === '' && this.s_partic === '') return 0;
                                    return c + p;
                                },

                                // Hitung Rata-rata
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

                                // Tentukan Predikat
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

                                // Warna Badge Predikat
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
                            
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-900">{{ $student->name }}</div>
                                <div class="text-xs text-gray-500 font-mono">{{ $student->student_number ?? '-' }}</div>
                            </td>

                            {{-- VOCABULARY --}}
                            <td class="px-3 py-3 text-center border-l">
                                <input type="number" min="0" max="100" 
                                    x-model="vocab" @input="vocab = limit($el.value, 100)"
                                    name="marks[{{ $student->id }}][vocabulary]" 
                                    class="w-14 h-9 text-center border-gray-300 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 text-sm p-1 transition-all">
                            </td>

                            {{-- GRAMMAR --}}
                            <td class="px-3 py-3 text-center">
                                <input type="number" min="0" max="100" 
                                    x-model="grammar" @input="grammar = limit($el.value, 100)"
                                    name="marks[{{ $student->id }}][grammar]" 
                                    class="w-14 h-9 text-center border-gray-300 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 text-sm p-1 transition-all">
                            </td>

                            {{-- LISTENING --}}
                            <td class="px-3 py-3 text-center">
                                <input type="number" min="0" max="100" 
                                    x-model="listening" @input="listening = limit($el.value, 100)"
                                    name="marks[{{ $student->id }}][listening]" 
                                    class="w-14 h-9 text-center border-gray-300 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 text-sm p-1 transition-all">
                            </td>

                            {{-- SPEAKING CONTENT (Max 50) --}}
                            <td class="px-3 py-3 text-center bg-purple-50/50">
                                <input type="number" min="0" max="50" 
                                    x-model="s_content" @input="s_content = limit($el.value, 50)"
                                    name="marks[{{ $student->id }}][speaking_content]" 
                                    class="w-14 h-9 text-center border-purple-200 rounded-lg bg-white focus:ring-2 focus:ring-purple-500 text-sm p-1 transition-all font-medium text-purple-700">
                            </td>

                            {{-- SPEAKING PARTICIPATION (Max 50) --}}
                            <td class="px-3 py-3 text-center bg-purple-50/50">
                                <input type="number" min="0" max="50" 
                                    x-model="s_partic" @input="s_partic = limit($el.value, 50)"
                                    name="marks[{{ $student->id }}][speaking_participation]" 
                                    class="w-14 h-9 text-center border-purple-200 rounded-lg bg-white focus:ring-2 focus:ring-purple-500 text-sm p-1 transition-all font-medium text-purple-700">
                            </td>

                            {{-- TOTAL SPEAKING (Otomatis / Readonly) --}}
                            {{-- Kita simpan totalnya di hidden input agar controller lama 'speaking' tetap jalan --}}
                            <td class="px-4 py-3 text-center bg-purple-100/50 font-black text-purple-900 border-x">
                                <span x-text="speakingTotal > 0 ? speakingTotal : '-'"></span>
                                {{-- Input Hidden untuk kompatibilitas dengan Controller --}}
                                <input type="hidden" name="marks[{{ $student->id }}][speaking]" x-model="speakingTotal"> 
                            </td>

                            {{-- READING --}}
                            <td class="px-3 py-3 text-center border-l">
                                <input type="number" min="0" max="100" 
                                    x-model="reading" @input="reading = limit($el.value, 100)"
                                    name="marks[{{ $student->id }}][reading]" 
                                    class="w-14 h-9 text-center border-gray-300 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 text-sm p-1 transition-all">
                            </td>

                            {{-- SPELLING --}}
                            <td class="px-3 py-3 text-center border-l">
                                <input type="number" min="0" max="100" 
                                    x-model="spelling" @input="spelling = limit($el.value, 100)"
                                    name="marks[{{ $student->id }}][spelling]" 
                                    class="w-14 h-9 text-center border-gray-300 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 text-sm p-1 transition-all">
                            </td>

                            {{-- AVERAGE SCORE (Calculated) --}}
                            <td class="px-4 py-3 text-center bg-blue-50/30">
                                <div class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-blue-600 text-white text-[11px] font-black shadow-md">
                                    <span x-text="average"></span>
                                </div>
                            </td>

                            {{-- PREDICATE (Calculated) --}}
                            <td class="px-4 py-3 text-center bg-gray-50/30 font-bold text-[10px] uppercase tracking-wide">
                                <span class="px-2 py-1 rounded-md border transition-all duration-300"
                                    :class="predicateColor" 
                                    x-text="predicate">
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="12" class="px-6 py-8 text-center text-gray-500">
                                No active students found in this class.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end space-x-3">
                <a href="{{ route('teacher.classes.detail', $class->id) }}" class="bg-white border border-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-100 transition shadow-sm font-medium">
                    Cancel
                </a>
                
                <button type="submit" class="bg-purple-600 text-white px-8 py-2 rounded-lg hover:bg-purple-700 transition shadow-sm shadow-purple-200 font-medium">
                    Save Marks
                </button>
            </div>
        </div>
    </form>
</div>
@endsection