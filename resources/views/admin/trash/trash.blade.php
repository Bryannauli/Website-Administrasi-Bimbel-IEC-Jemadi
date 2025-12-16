<x-app-layout>
    <x-slot name="header"></x-slot>

    {{-- Wrapper utama disamakan dengan Teacher (Background, Font, Min-Height) --}}
    <div class="py-6 bg-[#F3F4FF] min-h-screen font-sans">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- 1. BREADCRUMB (Style Konsisten dengan Teacher) --}}
            <nav class="flex mb-5" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    {{-- Dashboard Link --}}
                    <li class="inline-flex items-center">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-blue-600 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                            </svg>
                            Dashboard
                        </a>
                    </li>
                    {{-- Active Page (System Trash) - Warna Merah --}}
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-1 text-sm font-medium text-red-600 md:ml-2">System Trash</span>
                        </div>
                    </li>
                </ol>
            </nav>

            {{-- 2. TITLE & DESC (Style Konsisten, Gradient Merah) --}}
            <div class="mb-8">
                <h1 class="text-3xl font-bold bg-gradient-to-r from-red-600 to-rose-600 bg-clip-text text-transparent inline-block">
                    System Trash
                </h1>
                <p class="text-gray-500 text-sm mt-1">
                    Manage deleted items. 
                    <span class="font-bold text-red-600">({{ $totalCount }} items in trash)</span>
                </p>
            </div>

            {{-- 3. TABEL UNIFIED TRASH --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-max">
                        <thead class="bg-gray-50 text-xs text-gray-500 font-bold uppercase border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 whitespace-nowrap">Type</th>
                                <th class="px-6 py-4 whitespace-nowrap">Name</th>
                                <th class="px-6 py-4 whitespace-nowrap">Deleted At</th>
                                <th class="px-6 py-4 whitespace-nowrap text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                            @forelse ($logs as $item)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    {{-- Type --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full 
                                            @if($item['type'] == 'teacher') bg-indigo-100 text-indigo-700
                                            @elseif($item['type'] == 'student') bg-blue-100 text-blue-700
                                            @elseif($item['type'] == 'class') bg-yellow-100 text-yellow-700
                                            @else bg-gray-100 text-gray-700
                                            @endif">
                                            {{ ucfirst($item['type']) }}
                                        </span>
                                    </td>
                                    
                                    {{-- Name --}}
                                    <td class="px-6 py-4 font-semibold text-gray-800 whitespace-nowrap">
                                        {{ $item['name'] }}
                                    </td>
                                    
                                    {{-- Deleted At --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-red-500 font-medium">
                                        {{ \Carbon\Carbon::parse($item['deleted_at'])->format('d M Y, H:i') }}
                                    </td>
                                    
                                    {{-- Actions --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            {{-- Tombol Restore --}}
                                            <button onclick="confirmRestore('{{ $item['type'] }}', {{ $item['id'] }}, '{{ $item['name'] }}')" 
                                                    class="inline-flex items-center px-3 py-1.5 border border-green-600 text-green-600 rounded-lg text-xs font-bold hover:bg-green-50 transition-colors">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                                Restore
                                            </button>

                                            {{-- Tombol Delete Permanent --}}
                                            <button onclick="confirmForceDelete('{{ $item['type'] }}', {{ $item['id'] }}, '{{ $item['name'] }}')" 
                                                    class="inline-flex items-center px-3 py-1.5 border border-red-600 text-red-600 rounded-lg text-xs font-bold hover:bg-red-50 transition-colors">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-10 text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            <p>No items currently in the trash.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- 4. Paginasi (Style Konsisten) --}}
                <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between bg-white">
                    @if ($logs->onFirstPage())
                        <button class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-400 bg-gray-50 cursor-not-allowed" disabled>Previous</button>
                    @else
                        <a href="{{ $logs->previousPageUrl() }}" class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-600 bg-white hover:bg-gray-50 hover:text-gray-800 transition-colors">Previous</a>
                    @endif
                    
                    <span class="text-sm text-gray-500 font-medium">
                        Page {{ $logs->currentPage() }} of {{ $logs->lastPage() }}
                    </span>

                    @if ($logs->hasMorePages())
                        <a href="{{ $logs->nextPageUrl() }}" class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-600 bg-white hover:bg-gray-50 hover:text-gray-800 transition-colors">Next</a>
                    @else
                        <button class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-400 bg-gray-50 cursor-not-allowed" disabled>Next</button>
                    @endif
                </div>
            </div>

        </div>
    </div>

    {{-- SweetAlert & Logic JS (Tetap sama, hanya styling SweetAlert sedikit disesuaikan jika perlu) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Form & Logic tetap sama seperti sebelumnya
        const restoreForm = document.createElement('form');
        restoreForm.method = 'POST'; 
        restoreForm.style.display = 'none';
        restoreForm.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="PUT">'; 
        document.body.appendChild(restoreForm);
        
        const forceDeleteForm = document.createElement('form');
        forceDeleteForm.method = 'POST';
        forceDeleteForm.style.display = 'none';
        forceDeleteForm.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="DELETE">'; 
        document.body.appendChild(forceDeleteForm);

        const restoreUrlTemplate = "{{ route('admin.trash.restore', ['type' => ':type', 'id' => ':id']) }}";
        const forceDeleteUrlTemplate = "{{ route('admin.trash.forceDelete', ['type' => ':type', 'id' => ':id']) }}";

        function confirmRestore(type, id, name) {
            Swal.fire({
                title: 'Confirm Restore',
                html: `Are you sure you want to restore <b>${type.toUpperCase()}</b>: "${name}"?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10B981', 
                cancelButtonColor: '#6B7280', 
                confirmButtonText: 'Yes, Restore'
            }).then((result) => {
                if (result.isConfirmed) {
                    let url = restoreUrlTemplate.replace(':type', type).replace(':id', id);
                    restoreForm.action = url;
                    restoreForm.submit();
                }
            });
        }

        function confirmForceDelete(type, id, name) {
            Swal.fire({
                title: 'Permanent Delete',
                html: `You will <b>NOT</b> be able to recover <b>${type.toUpperCase()}</b>: "${name}"!<br>Proceed with permanent deletion?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444', 
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, Delete Permanently'
            }).then((result) => {
                if (result.isConfirmed) {
                    let url = forceDeleteUrlTemplate.replace(':type', type).replace(':id', id);
                    forceDeleteForm.action = url;
                    forceDeleteForm.submit();
                }
            });
        }
    </script>
</x-app-layout>