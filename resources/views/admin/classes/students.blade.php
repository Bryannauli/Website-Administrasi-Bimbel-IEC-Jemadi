<x-app-layout>
    <x-slot name="header"></x-slot>

    {{-- 
        x-data: Mengontrol state modal 'Add Student'
    --}}
    <div class="py-6" x-data="{ showAddModal: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Breadcrumb --}}
            <div class="mb-6 flex items-center gap-2 text-sm font-medium text-gray-500">
                <a href="{{ route('dashboard') }}" class="hover:text-gray-900 border-gray-800 text-gray-900">Home</a>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                <a href="{{ route('admin.classes.index') }}" class="hover:text-gray-900  border-gray-800 text-gray-900">Class</a>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                <span class="text-gray-500">Students List</span>
            </div>

              <div class="lg:col-span-2 space-y-6">
                    
                    {{-- Kartu Info Kelas --}}
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex justify-between items-start relative overflow-hidden">
                        <div class="absolute left-0 top-0 bottom-0 w-3 bg-red-600 rounded-l-2xl"></div>
                        
                        <div class="pl-4">
                            <h1 class="text-2xl font-bold text-gray-900">{{ $class->name }}</h1>
                            <p class="text-gray-400 text-sm mt-1">{{ $class->date }}</p>
                            
                            <div class="mt-4 flex items-center gap-2 text-gray-500 text-sm">
                                <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>{{ $class->time }}</span>
                            </div>
                        </div>

                        <div class="flex flex-col items-end gap-2">
                            <span class="px-4 py-1.5 bg-purple-100 text-purple-600 rounded-lg text-sm font-semibold">
                                {{ $class->status }}
                            </span>
                            <span class="px-4 py-1.5 bg-blue-50 text-blue-600 rounded-lg text-sm font-semibold">
                                {{ $class->level }}
                            </span>
                        </div>
                    </div>

            {{-- Action Bar --}}
            <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
                <div class="flex items-center gap-3 w-full md:w-auto">
                    
                    {{-- ADD STUDENT BUTTON (Memicu Modal) --}}
                    <button @click="showAddModal = true" 
                            class="inline-flex items-center px-4 py-2.5 bg-blue-700 border border-transparent rounded-xl font-medium text-sm text-white hover:bg-blue-800 focus:outline-none shadow-sm transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        Add Student to this Class
                    </button>
                </div>

                {{-- Search Bar --}}
                <div class="relative w-full md:w-72">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>
                    <input type="text" class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl leading-5 bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm shadow-sm" placeholder="Search student...">
                </div>
            </div>

            {{-- Tabel Siswa --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50 text-xs text-gray-400 font-medium uppercase border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4 w-16 font-normal">No</th>
                                <th class="px-6 py-4 font-normal">Student Number</th>
                                <th class="px-6 py-4 font-normal">Name</th>
                                <th class="px-6 py-4 font-normal">Gender</th>
                                <th class="px-6 py-4 font-normal">Status</th>
                                <th class="px-6 py-4 font-normal text-center w-32">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                            @forelse($students as $index => $student)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-5 text-gray-500">{{ $students->firstItem() + $index }}</td>
                                    <td class="px-6 py-5 font-medium text-gray-900">{{ $student->number }}</td>
                                    <td class="px-6 py-5 font-medium text-gray-900">{{ $student->name }}</td>
                                    <td class="px-6 py-5 text-gray-600">{{ $student->gender }}</td>
                                    <td class="px-6 py-5">
                                        <span class="px-3 py-1 rounded-md text-xs font-medium capitalize inline-block 
                                            {{ $student->status == 'Active' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600' }}">
                                            {{ $student->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex items-center justify-center gap-4">
                                            <a href="#" class="text-gray-400 hover:text-blue-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg></a>
                                            <button class="text-gray-400 hover:text-red-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button>
                                            <button class="text-gray-400 hover:text-green-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg></button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-6 py-10 text-center text-gray-500 bg-gray-50">No students found in this class.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ========================================== --}}
            {{-- MODAL ADD STUDENT (OVERLAY)                --}}
            {{-- ========================================== --}}
            <div x-show="showAddModal" 
                style="display: none;"
                class="fixed inset-0 z-50 overflow-y-auto" 
                aria-labelledby="modal-title" role="dialog" aria-modal="true">
                
                {{-- Backdrop --}}
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="showAddModal" 
                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
                         class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true" @click="showAddModal = false"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    {{-- Konten Modal --}}
                    <div x-show="showAddModal" 
                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                         class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl w-full">
                        
                        {{-- Header Modal --}}
                        <div class="px-8 py-6 flex justify-between items-center">
                            <h2 class="text-3xl font-bold bg-gradient-to-r from-blue-500 to-red-500 bg-clip-text text-transparent">
                                Add New Student
                            </h2>
                            <button @click="showAddModal = false" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        {{-- Body Modal --}}
                        <div class="px-8 pb-8">
                            <form action="{{ route('admin.student.store') }}" method="POST">
                                @csrf
                                
                                {{-- INPUT TERSEMBUNYI: ID KELAS --}}
                                {{-- Ini triknya: Kita kunci ID kelasnya sesuai halaman ini --}}
                                <input type="hidden" name="class_id" value="{{ $class->id }}">
                                
                                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                    
                                    {{-- Kolom Kiri: Info Dasar --}}
                                    <div class="lg:col-span-2 space-y-6">
                                        <h3 class="text-lg font-bold text-gray-800 border-b pb-2">Basic Information</h3>
                                        
                                        <div class="grid grid-cols-2 gap-6">
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">First Name</label>
                                                <input type="text" name="first_name" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 outline-none" required>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-2">Last Name</label>
                                                <input type="text" name="last_name" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 outline-none">
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Gender</label>
                                            <div class="flex gap-6">
                                                <label class="flex items-center gap-2 cursor-pointer">
                                                    <input type="radio" name="gender" value="male" checked class="text-blue-600 focus:ring-blue-500">
                                                    <span class="text-gray-700">Male</span>
                                                </label>
                                                <label class="flex items-center gap-2 cursor-pointer">
                                                    <input type="radio" name="gender" value="female" class="text-blue-600 focus:ring-blue-500">
                                                    <span class="text-gray-700">Female</span>
                                                </label>
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Date of Birth</label>
                                            <input type="date" name="dob" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 outline-none">
                                        </div>
                                        
                                        {{-- Info Kelas (Read Only / Disabled karena otomatis) --}}
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Assigning to Class</label>
                                            <input type="text" value="{{ $class->name }}" disabled class="w-full px-4 py-2.5 border border-gray-200 bg-gray-100 text-gray-500 rounded-lg cursor-not-allowed">
                                            <p class="text-xs text-gray-500 mt-1">*Student will be automatically added to this class.</p>
                                        </div>
                                    </div>

                                    {{-- Kolom Kanan: Kontak & Status --}}
                                    <div class="space-y-6">
                                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                                            <h3 class="text-lg font-bold text-gray-800 mb-4">Contact</h3>
                                            <div class="space-y-4">
                                                <div>
                                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Phone</label>
                                                    <input type="tel" name="phone" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Email</label>
                                                    <input type="email" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Address</label>
                                                    <textarea name="address" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                                            <h3 class="text-lg font-bold text-gray-800 mb-3">Status</h3>
                                            <div class="space-y-2">
                                                <label class="flex items-center gap-2"><input type="radio" name="status" value="active" checked class="text-blue-600"> Active</label>
                                                <label class="flex items-center gap-2"><input type="radio" name="status" value="inactive" class="text-blue-600"> Inactive</label>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                {{-- Footer Buttons --}}
                                <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-gray-100">
                                    <button type="button" @click="showAddModal = false" class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                                        Cancel
                                    </button>
                                    <button type="submit" class="px-8 py-2.5 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors shadow-sm">
                                        Save Student
                                    </button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
            {{-- END MODAL --}}

        </div>
    </div>
</x-app-layout>