<div x-show="showAddModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        
        {{-- Overlay --}}
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="showAddModal = false"></div>
        
        {{-- Modal Panel --}}
        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl w-full">
            
            {{-- Header --}}
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900">Add New Student</h3>
                <button @click="showAddModal = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-6 max-h-[80vh] overflow-y-auto custom-scrollbar">
                
                {{-- FORM ADD --}}
                <form action="{{ route('admin.student.store') }}" method="POST">
                    @csrf 

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        {{-- 1. Student Number --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Student Number <span class="text-red-500">*</span></label>
                            <input type="text" name="student_number" 
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm @error('student_number') border-red-500 @enderror" 
                                   placeholder="e.g. 2025001" 
                                   value="{{ old('student_number') }}" required>
                            @error('student_number')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- 2. Full Name --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" 
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm @error('name') border-red-500 @enderror" 
                                   placeholder="e.g. John Doe" 
                                   value="{{ old('name') }}" required>
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- 3. Gender --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Gender <span class="text-red-500">*</span></label>
                            <select name="gender" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm @error('gender') border-red-500 @enderror" required>
                                <option value="" disabled selected>Select Gender</option>
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                            @error('gender')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- 4. Phone --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                            <input type="text" name="phone" 
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm" 
                                   placeholder="e.g. 08123456789"
                                   value="{{ old('phone') }}">
                            @error('phone')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- 5. Address --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <textarea name="address" rows="2" 
                                      class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm" 
                                      placeholder="Enter full address...">{{ old('address') }}</textarea>
                        </div>

                        {{-- 6. Class Assignment (Alpine Local Scope untuk Filter) --}}
                        <div class="md:col-span-2 bg-blue-50/50 p-4 rounded-lg border border-blue-100" x-data="{ addCategory: 'all' }">
                            <label class="block text-sm font-bold text-gray-700 mb-3">Class Assignment (Optional)</label>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                {{-- Filter Category --}}
                                <div>
                                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 block">Filter Category</label>
                                    <select x-model="addCategory" class="w-full rounded-lg border-gray-300 text-sm">
                                        <option value="all">Show All Categories</option>
                                        @foreach ($categories as $cat) 
                                            <option value="{{ $cat }}">{{ ucwords(str_replace('_', ' ', $cat)) }}</option> 
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Select Class --}}
                                <div>
                                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 block">Select Class</label>
                                    <select name="class_id" class="w-full rounded-lg border-gray-300 text-sm @error('class_id') border-red-500 @enderror">
                                        <option value="">Select Class (Unassigned)</option>
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->id }}" 
                                                    {{ old('class_id') == $class->id ? 'selected' : '' }}
                                                    x-show="addCategory === 'all' || addCategory === '{{ $class->category }}'">
                                                {{ $class->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('class_id')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">* Filter by category first to find the class easier.</p>
                        </div>
                    </div>

                    {{-- Footer Action --}}
                    <div class="mt-8 flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
                        <button type="button" 
                            @click="{{ $errors->any() ? "window.location.href='".url()->current()."'" : "showAddModal = false" }}"
                            class="px-5 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-lg bg-blue-600 text-white font-medium hover:bg-blue-700 shadow-sm transition-colors">Save Student</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>