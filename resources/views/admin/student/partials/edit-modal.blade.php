<div x-show="showEditModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        
        {{-- Overlay --}}
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="showEditModal = false"></div>
        
        {{-- Modal Panel --}}
        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl w-full">
            
            {{-- Header --}}
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900">Edit Student</h3>
                <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>

            <div class="p-6 max-h-[80vh] overflow-y-auto custom-scrollbar">
                
                {{-- FORM UPDATE --}}
                <form :action="updateUrl" method="POST">
                    @csrf @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        {{-- 1. Student Number --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Student Number <span class="text-red-500">*</span></label>
                            <input type="text" name="student_number" x-model="editForm.student_number" 
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm @error('student_number') border-red-500 @enderror" required>
                            @error('student_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- 2. Full Name --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" x-model="editForm.name" 
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm @error('name') border-red-500 @enderror" required>
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- 3. Gender --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Gender <span class="text-red-500">*</span></label>
                            <select name="gender" x-model="editForm.gender" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>

                        {{-- 4. Phone --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                            <input type="text" name="phone" x-model="editForm.phone" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                        </div>

                        {{-- 5. Address --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <textarea name="address" x-model="editForm.address" rows="2" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm"></textarea>
                        </div>

                        {{-- 6. Status Student (Toggle) --}}
                        <div class="md:col-span-2 bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Student Status</label>
                            <div class="flex items-center">
                                <input type="hidden" name="is_active" :value="editForm.is_active ? 1 : 0">
                                <div @click="editForm.is_active = !editForm.is_active" class="relative inline-flex items-center cursor-pointer">
                                    <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-blue-300 transition-colors" :class="editForm.is_active ? 'bg-green-600' : 'bg-gray-200'"></div>
                                    <div class="absolute left-[2px] top-[2px] bg-white border-gray-300 border rounded-full h-5 w-5 transition-transform" :class="editForm.is_active ? 'translate-x-full border-white' : 'translate-x-0'"></div>
                                </div>
                                <span class="ml-3 text-sm font-medium" :class="editForm.is_active ? 'text-green-700 font-bold' : 'text-gray-500'" x-text="editForm.is_active ? 'Active (Studying)' : 'Inactive (Graduated/Out)'"></span>
                            </div>
                        </div>

                        {{-- 7. Class Assignment (Optional Display) --}}
                        @if($showClassAssignment ?? true)
                            <div class="md:col-span-2 bg-blue-50/50 p-4 rounded-lg border border-blue-100">
                                <label class="block text-sm font-bold text-gray-700 mb-3">Class Assignment</label>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 block">Filter Category</label>
                                        <select x-model="editCategory" class="w-full rounded-lg border-gray-300 text-sm">
                                            <option value="all">Show All Categories</option>
                                            @foreach ($categories as $cat) 
                                                <option value="{{ $cat }}">{{ ucwords(str_replace('_', ' ', $cat)) }}</option> 
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 block">Select Class</label>
                                        <select name="class_id" x-model="editForm.class_id" class="w-full rounded-lg border-gray-300 text-sm">
                                            <option value="">Select Class (Unassigned)</option>
                                            @foreach ($classes as $class)
                                                <option value="{{ $class->id }}" 
                                                        x-show="editCategory === 'all' || editCategory === '{{ $class->category }}'">
                                                    {{ $class->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>

                    <div class="mt-8 flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
                        <button type="button" 
                                @click="{{ $errors->any() ? "window.location.href='".url()->current()."'" : "showEditModal = false" }}"
                                class="px-5 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-700 font-medium hover:bg-gray-50">
                                Cancel
                            </button>
                        <button type="submit" class="px-5 py-2.5 rounded-lg bg-blue-600 text-white font-medium hover:bg-blue-700 shadow-sm">Update Changes</button>
                    </div>
                </form>

                {{-- DANGER ZONE --}}
                <div class="bg-red-50 rounded-xl shadow-sm border border-red-200 overflow-hidden mt-8">
                    <div class="px-6 py-4 flex flex-col md:flex-row items-center justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-bold text-red-800">Danger Zone</h3>
                            <p class="text-sm text-red-600 mt-1">Deleting this student will move data to trash.</p>
                        </div>
                        <button type="button" @click="confirmDelete()" class="whitespace-nowrap px-4 py-2 bg-red-600 text-white hover:bg-red-700 rounded-lg font-medium text-sm transition-all">Delete Student</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- FORM DELETE HIDDEN --}}
<form id="delete-student-form" :action="deleteUrl" method="POST" style="display: none;">
    @csrf @method('DELETE')
</form>