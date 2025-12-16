<x-app-layout>
    <x-slot name="header"></x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- 1. BREADCRUMB --}}
            <nav class="flex mb-8" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-blue-600">
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10l-3.293-3.293a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" fill-rule="evenodd"></path></svg>
                            <span class="ml-1 text-sm font-medium text-red-500 md:ml-2">System Trash (Soft Deleted)</span>
                        </div>
                    </li>
                </ol>
            </nav>

            {{-- 2. HEADER --}}
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">
                    System Trash <span class="text-red-500">({{ $totalCount }})</span>
                </h1>
            </div>

            {{-- 3. TABEL UNIFIED TRASH --}}
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deleted At</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($logs as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        {{-- Penanda Warna Berdasarkan Tipe --}}
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($item['type'] == 'teacher') bg-indigo-100 text-indigo-800
                                            @elseif($item['type'] == 'student') bg-blue-100 text-blue-800
                                            @elseif($item['type'] == 'class') bg-yellow-100 text-yellow-800
                                            @endif">
                                            {{ ucfirst($item['type']) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item['name'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-red-500">{{ \Carbon\Carbon::parse($item['deleted_at'])->format('d M Y H:i') }}</td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                                        {{-- Tombol Restore --}}
                                        <button onclick="confirmRestore('{{ $item['type'] }}', {{ $item['id'] }}, '{{ $item['name'] }}')" 
                                                class="text-green-600 hover:text-green-900 px-3 py-1 border border-green-600 rounded-md text-xs font-medium hover:bg-green-50 transition-colors">
                                            Restore
                                        </button>

                                        {{-- Tombol Delete Permanent --}}
                                        <button onclick="confirmForceDelete('{{ $item['type'] }}', {{ $item['id'] }}, '{{ $item['name'] }}')" 
                                                class="text-red-600 hover:text-red-900 px-3 py-1 border border-red-600 rounded-md text-xs font-medium hover:bg-red-50 transition-colors">
                                            Delete Permanent
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                        No items currently in the trash.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- 4. Paginasi (Mengikuti style teacher.blade.php) --}}
                <div class="mt-8">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-gray-700">
                            Showing
                            <span class="font-medium">{{ $logs->firstItem() }}</span>
                            to
                            <span class="font-medium">{{ $logs->lastItem() }}</span>
                            of
                            <span class="font-medium">{{ $logs->total() }}</span>
                            results
                        </p>
                        <div class="flex space-x-2">
                            @if ($logs->onFirstPage())
                                <button class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-400 bg-gray-50 cursor-not-allowed" disabled>Previous</button>
                            @else
                                <a href="{{ $logs->previousPageUrl() }}" class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-600 bg-white hover:bg-gray-50 hover:text-gray-800 transition-colors">Previous</a>
                            @endif
                            @if ($logs->hasMorePages())
                                <a href="{{ $logs->nextPageUrl() }}" class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-600 bg-white hover:bg-gray-50 hover:text-gray-800 transition-colors">Next</a>
                            @else
                                <button class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-400 bg-gray-50 cursor-not-allowed" disabled>Next</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

{{-- SweetAlert & Universal Forms --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // 1. Buat Form Tersembunyi (Sekarang menggunakan output HTML murni)
        
        // Form tersembunyi untuk RESTORE (PUT)
        const restoreForm = document.createElement('form');
        restoreForm.method = 'POST'; 
        restoreForm.style.display = 'none';
        
        // FIX: Menggunakan output HTML murni dari token CSRF dan _method=PUT
        restoreForm.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="PUT">'; 
        document.body.appendChild(restoreForm);
        
        // Form tersembunyi untuk FORCE DELETE (DELETE)
        const forceDeleteForm = document.createElement('form');
        forceDeleteForm.method = 'POST';
        forceDeleteForm.style.display = 'none';

        // FIX: Menggunakan output HTML murni dari token CSRF dan _method=DELETE
        forceDeleteForm.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="DELETE">'; 
        document.body.appendChild(forceDeleteForm);

        // 2. Definisikan Template URL menggunakan route() helper
        const restoreUrlTemplate = "{{ route('admin.trash.restore', ['type' => ':type', 'id' => ':id']) }}";
        const forceDeleteUrlTemplate = "{{ route('admin.trash.forceDelete', ['type' => ':type', 'id' => ':id']) }}";


        function confirmRestore(type, id, name) {
            Swal.fire({
                title: 'Confirm Restore',
                text: `Are you sure you want to restore ${type.toUpperCase()}: "${name}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#10B981', 
                cancelButtonColor: '#EF4444', 
                confirmButtonText: 'Yes, restore it!'
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
                title: 'Confirm Permanent Deletion',
                text: `You will not be able to recover ${type.toUpperCase()}: "${name}"! Proceed with permanent deletion?`,
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#EF4444', 
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, delete permanently!'
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