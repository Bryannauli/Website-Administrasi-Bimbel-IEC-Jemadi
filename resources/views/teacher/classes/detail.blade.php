<!-- resources/views/teacher/classes/detail.blade.php -->
@extends('layouts.teacher')

@section('title', 'Class Details')

@section('breadcrumb')
<nav class="flex items-center space-x-2 text-sm">
    <a href="{{ route('teacher.dashboard') }}" class="text-gray-600 hover:text-gray-900">Dashboard</a>
    <i class="fas fa-chevron-right text-xs text-gray-400"></i>
    <a href="{{ route('teacher.classes.index') }}" class="text-gray-600 hover:text-gray-900">Class</a>
    <i class="fas fa-chevron-right text-xs text-gray-400"></i>
    <span class="text-gray-900 font-medium">Attendance</span>
</nav>
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Class Info Card -->
    <div class="border-l-4 border-red-500 bg-white rounded-lg p-6 shadow-sm">
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Vocabulary</h2>
                <div class="space-y-1 text-sm text-gray-600">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-calendar text-gray-400 w-4"></i>
                        <span>March 20, 2021</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-clock text-gray-400 w-4"></i>
                        <span>08.00 - 09.00 AM</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-door-open text-gray-400 w-4"></i>
                        <span>E-101</span>
                    </div>
                </div>
            </div>
            <div class="flex flex-col space-y-2">
                <span class="bg-green-100 text-green-600 px-4 py-1 rounded-full text-sm font-medium text-center">Present</span>
                <span class="bg-blue-100 text-blue-600 px-4 py-1 rounded-full text-sm font-medium text-center">Pre-Level</span>
            </div>
        </div>
    </div>

    <!-- Filter and Add Button -->
    <div class="flex items-center justify-between">
        <div class="relative">
            <select class="px-4 py-2 border border-gray-300 rounded-lg pr-10 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option>All</option>
                <option>Active</option>
                <option>Completed</option>
            </select>
        </div>
        <button @click="openModal = true" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition flex items-center space-x-2">
            <i class="fas fa-plus"></i>
            <span>Add New Session</span>
        </button>
    </div>

    <!-- Sessions Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">No</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Session No.</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Topic</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm text-gray-800">1</td>
                        <td class="px-6 py-4 text-sm text-gray-800">1</td>
                        <td class="px-6 py-4 text-sm text-gray-600">2025-10-12</td>
                        <td class="px-6 py-4 text-sm text-gray-800">Exercise</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3">
                                <a href="{{ route('teacher.classes.session.detail', [1, 1]) }}" class="text-gray-600 hover:text-blue-600">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button class="text-gray-600 hover:text-green-600">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm text-gray-800">2</td>
                        <td class="px-6 py-4 text-sm text-gray-800">2</td>
                        <td class="px-6 py-4 text-sm text-gray-600">2025-10-11</td>
                        <td class="px-6 py-4 text-sm text-gray-800">Verb</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3">
                                <a href="{{ route('teacher.classes.session.detail', [1, 2]) }}" class="text-gray-600 hover:text-blue-600">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button class="text-gray-600 hover:text-green-600">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
            <button class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50">
                Previous
            </button>
            <span class="text-sm text-gray-600">Page 1 of 1</span>
            <button class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50">
                Next
            </button>
        </div>
    </div>
</div>

<!-- Modal Add New Session -->
<div x-data="{ openModal: false }" @keydown.escape="openModal = false">
    <div x-show="openModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black opacity-50" @click="openModal = false"></div>
            
            <div class="relative bg-white rounded-xl shadow-xl max-w-2xl w-full p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl font-bold text-gray-800">Add New Session</h3>
                    <button @click="openModal = false" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Class Name</label>
                            <input type="text" value="Vocabulary" readonly class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                            <input type="text" value="19 November 2025" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Start Time</label>
                            <input type="time" value="17:00" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">End Time</label>
                            <input type="time" value="18:00" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Session No.</label>
                            <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option>12</option>
                                <option>13</option>
                                <option>14</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Topic</label>
                        <input type="text" placeholder="Topic Sentence" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection