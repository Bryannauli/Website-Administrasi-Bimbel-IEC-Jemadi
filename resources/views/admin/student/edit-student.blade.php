<x-app-layout>

    <x-slot name="header"></x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- BREADCRUMB (SMART CONTEXT: Bisa dari Class atau Student List) --}}
            <nav class="flex mb-5" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    
                    {{-- 1. Dashboard (Selalu Ada) --}}
                    <li class="inline-flex items-center">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-blue-600">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                            Dashboard
                        </a>
                    </li>

                    {{-- LOGIKA PERCABANGAN --}}
                    @if(request('ref') == 'class' && $student->classModel)
                        
                        {{-- JALUR KELAS: Dashboard > Classes > Nama Kelas > Nama Siswa --}}
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                <a href="{{ route('admin.classes.index') }}" class="ml-1 text-sm font-medium text-gray-500 hover:text-blue-600 md:ml-2">Classes</a>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                <a href="{{ route('admin.classes.detailclass', $student->classModel->id) }}" class="ml-1 text-sm font-medium text-gray-500 hover:text-blue-600 md:ml-2">{{ $student->classModel->name }}</a>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                <a href="{{ route('admin.student.detail', ['id' => $student->id, 'ref' => 'class', 'class_id' => $student->classModel->id]) }}" class="ml-1 text-sm font-medium text-gray-500 hover:text-blue-600 md:ml-2">{{ $student->name }}</a>
                            </div>
                        </li>

                    @else
                        
                        {{-- JALUR STANDARD: Dashboard > Students > Nama Siswa --}}
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                <a href="{{ route('admin.student.index') }}" class="ml-1 text-sm font-medium text-gray-500 hover:text-blue-600 md:ml-2">Students</a>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                <a href="{{ route('admin.student.detail', $student->id) }}" class="ml-1 text-sm font-medium text-gray-500 hover:text-blue-600 md:ml-2">{{ $student->name }}</a>
                            </div>
                        </li>

                    @endif

                    {{-- Last Item: Edit --}}
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            <span class="ml-1 text-sm font-medium text-gray-900 md:ml-2">Edit</span>
                        </div>
                    </li>
                </ol>
            </nav>

            {{-- MAIN FORM CARD --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Edit Student</h1>
                        <p class="text-sm text-gray-500 mt-1">Update information for: <span class="font-semibold text-blue-600">{{ $student->name }}</span></p>
                    </div>
                </div>

                <div class="p-6">
                    <form action="{{ route('admin.student.update', $student->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Input Fields Sama Seperti Sebelumnya --}}
                            <div>
                                <label for="student_number" class="block text-sm font-medium text-gray-700 mb-1">Student Number <span class="text-red-500">*</span></label>
                                <input type="text" name="student_number" id="student_number" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm" value="{{ old('student_number', $student->student_number) }}">
                                @error('student_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="name" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm" value="{{ old('name', $student->name) }}">
                                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Gender <span class="text-red-500">*</span></label>
                                <select name="gender" id="gender" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm bg-white">
                                    <option value="male" {{ old('gender', $student->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender', $student->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                <input type="text" name="phone" id="phone" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm" value="{{ old('phone', $student->phone) }}">
                            </div>

                            <div class="md:col-span-2">
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                <textarea name="address" id="address" rows="3" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm">{{ old('address', $student->address) }}</textarea>
                            </div>

                            {{-- Student Status --}}
                            <div class="md:col-span-2 bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Student Status</label>
                                <div class="flex items-center">
                                    <input type="hidden" name="is_active" value="0">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="is_active" id="status_toggle" value="1" class="sr-only peer" {{ old('is_active', $student->is_active) ? 'checked' : '' }}>
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-100 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                                        <span class="ml-3 text-sm font-medium text-gray-900" id="status_label">{{ old('is_active', $student->is_active) ? 'Active (Studying)' : 'Inactive (Graduated/Out)' }}</span>
                                    </label>
                                </div>
                            </div>

                            {{-- Class Assignment --}}
                            <div class="md:col-span-2 bg-blue-50/50 p-4 rounded-lg border border-blue-100">
                                <label class="block text-sm font-bold text-gray-700 mb-3">Class Assignment</label>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 block">Filter by Category</label>
                                        <select id="category_filter" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm bg-white cursor-pointer text-sm">
                                            <option value="all">Show All Categories</option>
                                            @foreach ($categories as $cat) <option value="{{ $cat }}">{{ ucwords(str_replace('_', ' ', $cat)) }}</option> @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 block">Select Class</label>
                                        <select name="class_id" id="class_selector" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm bg-white cursor-pointer text-sm">
                                            <option value="">Select Class (Unassigned)</option>
                                            @foreach ($classes as $class)
                                                <option value="{{ $class->id }}" data-category="{{ $class->category }}" {{ old('class_id', $student->class_id) == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
                            <button type="button" onclick="history.back()" class="px-5 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-700 font-medium hover:bg-gray-50 transition-colors">Cancel</button>
                            <button type="submit" class="px-5 py-2.5 rounded-lg bg-blue-600 text-white font-medium hover:bg-blue-700 shadow-sm transition-colors">Update Changes</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- DANGER ZONE --}}
            <div class="bg-red-50 rounded-xl shadow-sm border border-red-200 overflow-hidden mb-10">
                <div class="px-6 py-4 flex flex-col md:flex-row items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-red-800">Danger Zone</h3>
                        <p class="text-sm text-red-600 mt-1">Deleting this student will permanently remove all data.</p>
                    </div>
                    <form action="{{ route('admin.student.delete', $student->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn-delete-permanent whitespace-nowrap px-4 py-2 bg-red-600 text-white hover:bg-red-700 rounded-lg font-medium text-sm transition-all">Delete Permanently</button>
                    </form>
                </div>
            </div>

        </div>
    </div>

    {{-- SCRIPTS (Sama seperti sebelumnya) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Logic 1: Status Toggle Label
            const statusToggle = document.getElementById('status_toggle');
            const statusLabel = document.getElementById('status_label');
            const updateStatusLabel = () => {
                if(statusToggle.checked) {
                    statusLabel.textContent = "Active (Studying)";
                    statusLabel.className = "ml-3 text-sm font-bold text-green-700";
                } else {
                    statusLabel.textContent = "Inactive (Graduated/Out)";
                    statusLabel.className = "ml-3 text-sm font-medium text-gray-500";
                }
            };
            statusToggle.addEventListener('change', updateStatusLabel);
            updateStatusLabel(); // Init

            // Logic 2: Filter Class Category
            const categoryFilter = document.getElementById('category_filter');
            const classSelector = document.getElementById('class_selector');
            const originalOptions = Array.from(classSelector.options).slice(1);
            
            // Set initial filter based on current selection
            let currentSelected = classSelector.options[classSelector.selectedIndex];
            if(currentSelected && currentSelected.value) {
                categoryFilter.value = currentSelected.getAttribute('data-category') || 'all';
            }

            const filterClasses = () => {
                const cat = categoryFilter.value;
                const currentVal = classSelector.value;
                classSelector.innerHTML = '<option value="">Select Class (Unassigned)</option>';
                originalOptions.forEach(opt => {
                    if(cat === 'all' || opt.getAttribute('data-category') === cat) {
                        classSelector.appendChild(opt);
                    }
                });
                classSelector.value = currentVal; // Restore selection if valid
            };
            
            filterClasses(); // Init filter
            categoryFilter.addEventListener('change', () => {
                classSelector.value = ""; // Reset on filter change
                filterClasses();
            });

            // Logic 3: Delete Confirm
            document.querySelectorAll('.btn-delete-permanent').forEach(btn => {
                btn.addEventListener('click', function(e){
                    e.preventDefault();
                    let form = this.closest('form');
                    Swal.fire({
                        title: "Are you sure?",
                        text: "This action cannot be undone.",
                        icon: "error",
                        showCancelButton: true,
                        confirmButtonColor: "#EF4444",
                        confirmButtonText: "Yes, Delete",
                    }).then((result) => {
                        if (result.isConfirmed) form.submit();
                    })
                });
            });
        });
    </script>
</x-app-layout>