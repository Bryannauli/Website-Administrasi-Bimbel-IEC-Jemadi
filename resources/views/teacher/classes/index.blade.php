<!-- resources/views/teacher/classes/index.blade.php -->
@extends('layouts.teacher')

@section('title', 'Classes')

@section('breadcrumb')
<nav class="flex items-center space-x-2 text-sm">
    <a href="{{ route('teacher.dashboard') }}" class="text-gray-600 hover:text-gray-900">Home</a>
    <i class="fas fa-chevron-right text-xs text-gray-400"></i>
    <span class="text-gray-900 font-medium">Classes</span>
</nav>
@endsection

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold bg-gradient-to-r from-red-500 to-purple-600 bg-clip-text text-transparent">
                Classes
            </h1>
        </div>
        <button class="bg-gradient-to-r from-blue-500 to-purple-600 text-white px-4 py-2 rounded-lg hover:shadow-lg transition">
            <i class="fas fa-download mr-2"></i> Export
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="relative">
                <button class="w-full flex items-center justify-between px-4 py-3 border border-gray-300 rounded-lg hover:border-blue-500 transition">
                    <span class="text-gray-600"><i class="fas fa-filter mr-2"></i> Filters</span>
                </button>
            </div>
            <div class="relative">
                <input type="text" placeholder="Search" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <i class="fas fa-search absolute left-3 top-4 text-gray-400"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">No</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Class ID</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Classname</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Schedule</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Room</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($classes as $index => $class)
                    <tr class="hover:bg-gray-50 transition">
                        {{-- No Urut --}}
                        <td class="px-6 py-4 text-sm text-gray-800">
                            {{ ($classes->currentPage() - 1) * $classes->perPage() + $index + 1 }}
                        </td>
                        
                        {{-- Class ID (Format C-001) --}}
                        <td class="px-6 py-4 text-sm text-gray-800 font-mono">
                            C-{{ str_pad($class->id, 3, '0', STR_PAD_LEFT) }}
                        </td>

                        {{-- Classname & Category --}}
                        <td class="px-6 py-4 text-sm text-gray-800">
                            <div class="font-medium">{{ $class->name }}</div>
                            <div class="text-xs text-gray-500 mt-0.5">{{ ucfirst(str_replace('_', ' ', $class->category)) }}</div>
                        </td>

                        {{-- Schedule: Gabungan Hari & Jam --}}
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <div class="font-medium text-gray-900">
                                @if($class->schedules->count() > 0)
                                    {{ $class->schedules->pluck('day_of_week')->implode(', ') }}
                                @else
                                    <span class="text-gray-400 italic">No days set</span>
                                @endif
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                {{ \Carbon\Carbon::parse($class->start_time)->format('H:i') }} - 
                                {{ \Carbon\Carbon::parse($class->end_time)->format('H:i') }}
                            </div>
                        </td>

                        {{-- Room --}}
                        <td class="px-6 py-4 text-sm text-gray-800">
                            {{ $class->classroom }}
                        </td>

                        {{-- Status --}}
                        <td class="px-6 py-4">
                            @if($class->is_active)
                                <span class="bg-blue-100 text-blue-600 px-3 py-1 rounded-full text-xs font-medium">Active</span>
                            @else
                                <span class="bg-red-100 text-red-600 px-3 py-1 rounded-full text-xs font-medium">Inactive</span>
                            @endif
                        </td>

                        {{-- Action --}}
                        <td class="px-6 py-4">
                            {{-- Gunakan route dengan ID class --}}
                            <a href="{{ route('teacher.classes.detail', $class->id) }}" class="text-gray-600 hover:text-blue-600 transition" title="View Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-folder-open text-4xl mb-3 text-gray-300"></i>
                                <p>You have no classes assigned yet.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-200">
            {{ $classes->links() }}
        </div>
    </div>
</div>

<!-- Modal Add Schedule -->
<div x-data="{ open: false }" x-show="open" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black opacity-50" @click="open = false"></div>
        
        <div class="relative bg-white rounded-xl shadow-xl max-w-2xl w-full p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-gray-800">Add new Schedule</h3>
                <button @click="open = false" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Class Name</label>
                        <input type="text" placeholder="Jhon" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <input type="text" placeholder="Regular" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Room</label>
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option>Choose Room</option>
                            <option>E-101</option>
                            <option>E-102</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Academic Year</label>
                        <input type="text" placeholder="2025/2026" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Teacher</label>
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option>Choose a Teacher</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Schedule</label>
                        <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                        Save all
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection