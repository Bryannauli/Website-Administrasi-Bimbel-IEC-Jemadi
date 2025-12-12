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
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase w-10">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase w-48">Student Name</th>
                            <th class="px-2 py-3 text-center text-xs font-semibold text-gray-600 uppercase w-20">Vocab</th>
                            <th class="px-2 py-3 text-center text-xs font-semibold text-gray-600 uppercase w-20">Grammar</th>
                            <th class="px-2 py-3 text-center text-xs font-semibold text-gray-600 uppercase w-20">Listen</th>
                            <th class="px-2 py-3 text-center text-xs font-semibold text-gray-600 uppercase w-20">Speak</th>
                            <th class="px-2 py-3 text-center text-xs font-semibold text-gray-600 uppercase w-20">Read</th>
                            <th class="px-2 py-3 text-center text-xs font-semibold text-gray-600 uppercase w-20">Spell</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($students as $index => $student)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-sm text-gray-800">{{ $index + 1 }}</td>
                            
                            <td class="px-4 py-3">
                                <div class="text-sm font-medium text-gray-800">{{ $student->name }}</div>
                                <div class="text-xs text-gray-500 font-mono">{{ $student->student_number ?? '-' }}</div>
                            </td>

                            <td class="px-2 py-3">
                                <input type="number" name="marks[{{ $student->id }}][vocabulary]" 
                                       value="{{ $student->form->vocabulary ?? '' }}" min="0" max="100" 
                                       class="w-full px-2 py-1 border border-gray-300 rounded text-center focus:ring-purple-500 focus:border-purple-500 text-sm">
                            </td>

                            <td class="px-2 py-3">
                                <input type="number" name="marks[{{ $student->id }}][grammar]" 
                                       value="{{ $student->form->grammar ?? '' }}" min="0" max="100" 
                                       class="w-full px-2 py-1 border border-gray-300 rounded text-center focus:ring-purple-500 focus:border-purple-500 text-sm">
                            </td>

                            <td class="px-2 py-3">
                                <input type="number" name="marks[{{ $student->id }}][listening]" 
                                       value="{{ $student->form->listening ?? '' }}" min="0" max="100" 
                                       class="w-full px-2 py-1 border border-gray-300 rounded text-center focus:ring-purple-500 focus:border-purple-500 text-sm">
                            </td>

                            <td class="px-2 py-3">
                                <input type="number" name="marks[{{ $student->id }}][speaking]" 
                                       value="{{ $student->form->speaking ?? '' }}" min="0" max="100" 
                                       class="w-full px-2 py-1 border border-gray-300 rounded text-center focus:ring-purple-500 focus:border-purple-500 text-sm">
                            </td>

                            <td class="px-2 py-3">
                                <input type="number" name="marks[{{ $student->id }}][reading]" 
                                       value="{{ $student->form->reading ?? '' }}" min="0" max="100" 
                                       class="w-full px-2 py-1 border border-gray-300 rounded text-center focus:ring-purple-500 focus:border-purple-500 text-sm">
                            </td>

                            <td class="px-2 py-3">
                                <input type="number" name="marks[{{ $student->id }}][spelling]" 
                                       value="{{ $student->form->spelling ?? '' }}" min="0" max="100" 
                                       class="w-full px-2 py-1 border border-gray-300 rounded text-center focus:ring-purple-500 focus:border-purple-500 text-sm">
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500">
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