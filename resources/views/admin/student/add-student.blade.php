<x-app-layout>

    <x-slot name="header"></x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- BREADCRUMB --}}
            <nav class="flex mb-5" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-blue-600">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            <a href="{{ route('admin.student.index') }}" class="ml-1 text-sm font-medium text-gray-500 hover:text-blue-600 md:ml-2">Students</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            <span class="ml-1 text-sm font-medium text-gray-900 md:ml-2">Add New</span>
                        </div>
                    </li>
                </ol>
            </nav>

            {{-- FORM CARD --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                
                {{-- Card Header --}}
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h1 class="text-xl font-bold text-gray-900">Add New Student</h1>
                    <p class="text-sm text-gray-500 mt-1">Please fill in the student data correctly.</p>
                </div>

                {{-- Form Content --}}
                <div class="p-6">
                    <form action="{{ route('admin.student.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- 1. Student Number --}}
                            <div>
                                <label for="student_number" class="block text-sm font-medium text-gray-700 mb-1">Student Number <span class="text-red-500">*</span></label>
                                <input type="text" name="student_number" id="student_number"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-colors"
                                    placeholder="e.g. 2025001"
                                    value="{{ old('student_number') }}">
                                @error('student_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- 2. Full Name --}}
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="name"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-colors"
                                    placeholder="e.g. John Doe"
                                    value="{{ old('name') }}">
                                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- 3. Gender --}}
                            <div>
                                <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Gender <span class="text-red-500">*</span></label>
                                <select name="gender" id="gender" 
                                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm bg-white cursor-pointer">
                                    <option value="" disabled selected class="text-gray-400">Select Gender</option>
                                    <option value="male" {{ old('gender')=='male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender')=='female' ? 'selected' : '' }}>Female</option>
                                </select>
                                @error('gender') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- 4. Phone --}}
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                <input type="text" name="phone" id="phone"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-colors"
                                    placeholder="e.g. 08123456789"
                                    value="{{ old('phone') }}">
                            </div>

                            {{-- 5. Address (Posisi Dinaikkan) --}}
                            <div class="md:col-span-2">
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                <textarea name="address" id="address" rows="3"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm transition-colors"
                                    placeholder="Enter full address...">{{ old('address') }}</textarea>
                            </div>

                            {{-- 6. Class Assignment (FILTERED) --}}
                            <div class="md:col-span-2 bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <label class="block text-sm font-bold text-gray-700 mb-3">Assign Class (Optional)</label>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    
                                    {{-- A. FILTER KATEGORI (Hanya visual, name dikosongkan agar tidak terkirim) --}}
                                    <div>
                                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 block">Filter by Category</label>
                                        <div class="relative">
                                            <select id="category_filter" 
                                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm bg-white cursor-pointer text-sm">
                                                <option value="all">Show All Categories</option>
                                                @foreach ($categories as $cat)
                                                    <option value="{{ $cat }}">{{ ucwords(str_replace('_', ' ', $cat)) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    {{-- B. PILIH KELAS (Target Asli) --}}
                                    <div>
                                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 block">Select Class</label>
                                        <div class="relative">
                                            <select name="class_id" id="class_selector" 
                                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm bg-white cursor-pointer text-sm">
                                                
                                                <option value="" selected>Select Class (Unassigned)</option>
                                                
                                                @foreach ($classes as $class)
                                                    {{-- Kita sisipkan data-category di sini untuk dibaca JS --}}
                                                    <option value="{{ $class->id }}" 
                                                            data-category="{{ $class->category }}"
                                                            {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                                        {{ $class->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            
                                            {{-- Pesan Error --}}
                                            @error('class_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                        </div>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 mt-2">* Tip: Select a category on the left to narrow down the class list on the right.</p>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="mt-8 flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
                            
                            {{-- Cancel Button --}}
                            <a href="{{ route('admin.student.index') }}" 
                               class="px-5 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-700 font-medium hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 transition-colors">
                                Cancel
                            </a>

                            {{-- Save Button --}}
                            <button type="submit" 
                                    class="px-5 py-2.5 rounded-lg bg-blue-600 text-white font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-sm transition-colors">
                                Save Student
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>

    {{-- Script Filter Kategori Kelas --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categoryFilter = document.getElementById('category_filter');
            const classSelector = document.getElementById('class_selector');
            
            // Simpan semua opsi kelas asli ke dalam array saat halaman dimuat
            // Kita skip opsi pertama (index 0) karena itu adalah placeholder "Select Class (Unassigned)"
            const originalOptions = Array.from(classSelector.options).slice(1);

            categoryFilter.addEventListener('change', function() {
                const selectedCategory = this.value;

                // 1. Reset pilihan kelas ke "Unassigned" dulu biar aman
                classSelector.value = "";

                // 2. Kosongkan dropdown kelas (sisakan placeholder paling atas)
                classSelector.innerHTML = '<option value="" selected>Select Class (Unassigned)</option>';

                // 3. Filter dan Masukkan kembali opsi yang cocok
                originalOptions.forEach(option => {
                    const optionCategory = option.getAttribute('data-category');

                    // Jika "all" atau kategori cocok, masukkan opsi ke dropdown
                    if (selectedCategory === 'all' || optionCategory === selectedCategory) {
                        classSelector.appendChild(option);
                    }
                });
            });
        });
    </script>

</x-app-layout>