<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Recycle Bin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- 1. BREADCRUMB (Tambahkan Bagian Ini) --}}
            <nav class="flex mb-5" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-blue-600">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                            Dashboard
                        </a>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            <span class="ml-1 text-sm font-medium text-gray-900 md:ml-2">Trash Bin</span>
                        </div>
                    </li>
                </ol>
            </nav>

            {{-- 2. HEADER / TITLE SECTION --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                <div>
                    <h3 class="text-2xl font-bold text-gray-800">Deleted Items</h3>
                    <p class="text-sm text-gray-500 mt-1">Manage deleted teachers, students, and classes.</p>
                </div>
                <div class="mt-4 md:mt-0 px-4 py-2 bg-red-50 text-red-600 rounded-lg border border-red-100 text-sm font-semibold">
                    <span class="font-bold">{{ $totalCount }}</span> Items in trash
                </div>
            </div>

            {{-- FLASH MESSAGE --}}
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded shadow-sm">
                    <p class="font-medium">{{ session('success') }}</p>
                </div>
            @endif
            @if (session('error'))
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded shadow-sm">
                    <p class="font-medium">{{ session('error') }}</p>
                </div>
            @endif

            {{-- TABLE SECTION --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name / Identity</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deleted At</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($logs as $log)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    {{-- TYPE BADGE --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $badgeClass = match($log->type) {
                                                'teacher' => 'bg-purple-100 text-purple-800',
                                                'student' => 'bg-blue-100 text-blue-800',
                                                'class'   => 'bg-orange-100 text-orange-800',
                                                default   => 'bg-gray-100 text-gray-800',
                                            };
                                        @endphp
                                        <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full uppercase tracking-wide {{ $badgeClass }}">
                                            {{ $log->type }}
                                        </span>
                                    </td>

                                    {{-- NAME --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $log->name }}</div>
                                        <div class="text-xs text-gray-400">ID: {{ $log->id }}</div>
                                    </td>

                                    {{-- DELETED AT --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div class="flex flex-col">
                                            <span class="font-medium">{{ \Carbon\Carbon::parse($log->deleted_at)->format('d M Y') }}</span>
                                            <span class="text-xs">{{ \Carbon\Carbon::parse($log->deleted_at)->format('H:i') }}</span>
                                        </div>
                                    </td>

                                    {{-- ACTIONS --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-3">
                                            
                                            {{-- VIEW DETAIL (ONLY FOR STUDENTS) --}}
                                            @if($log->type === 'student')
                                                <a href="{{ route('admin.trash.student.detail', $log->id) }}" 
                                                    class="text-gray-500 hover:text-blue-600 transition-colors"
                                                    title="View Deleted Details">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                </a>
                                            @endif

                                            {{-- VIEW DETAIL (TEACHER) - TAMBAHKAN INI --}}
                                            @if($log->type === 'teacher')
                                                <a href="{{ route('admin.trash.teacher.detail', $log->id) }}" 
                                                    class="text-gray-500 hover:text-purple-600 transition-colors"
                                                    title="View Deleted Details">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                </a>
                                            @endif

                                            {{-- VIEW DETAIL (CLASS) --}}
                                            @if($log->type === 'class')
                                                <a href="{{ route('admin.trash.class.detail', $log->id) }}" 
                                                    class="text-gray-500 hover:text-orange-600 transition-colors"
                                                    title="View Deleted Class">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                </a>
                                            @endif

                                            {{-- RESTORE --}}
                                            <form action="{{ route('admin.trash.restore', ['type' => $log->type, 'id' => $log->id]) }}" method="POST" class="inline-block">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:text-green-900 transition-colors" title="Restore" onclick="return confirm('Are you sure you want to restore this item?')">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                                </button>
                                            </form>

                                            {{-- FORCE DELETE --}}
                                            <form action="{{ route('admin.trash.force_delete', ['type' => $log->type, 'id' => $log->id]) }}" method="POST" class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 transition-colors" title="Delete Permanently" onclick="return confirm('WARNING: This action is irreversible. Are you sure?')">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        <p class="mt-2 text-sm font-medium">Trash is empty</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- PAGINATION --}}
            <div class="mt-6">
                {{ $logs->links() }}
            </div>
            
        </div>
    </div>
</x-app-layout>