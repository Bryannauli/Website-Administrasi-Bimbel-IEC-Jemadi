<div x-show="showEditModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        
        {{-- Overlay --}}
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="closeModal('showEditModal')"></div>
        
        {{-- Modal Panel --}}
        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl w-full">
            
            {{-- Header --}}
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900">Edit Teacher</h3>
                <button @click="closeModal('showEditModal')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-6 max-h-[80vh] overflow-y-auto custom-scrollbar">
                
                {{-- FORM EDIT --}}
                <form :action="updateUrl" method="POST">
                    @csrf 
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        {{-- 1. Name --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" x-model="editForm.name" required
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm @error('name') border-red-500 @enderror">
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- 2. Username --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Username <span class="text-red-500">*</span></label>
                            <input type="text" name="username" x-model="editForm.username" required
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm @error('username') border-red-500 @enderror">
                            @error('username') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- 3. Email --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" x-model="editForm.email"
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm @error('email') border-red-500 @enderror">
                            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- 4. Phone --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                            <input type="text" name="phone" x-model="editForm.phone"
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm @error('phone') border-red-500 @enderror">
                            @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- 5. Status (Active/Inactive Toggle) --}}
                        <div class="md:col-span-2 bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Teacher Status</label>
                            <div class="flex items-center">
                                <input type="hidden" name="status" :value="editForm.status ? 1 : 0">
                                
                                <div @click="editForm.status = !editForm.status" class="relative inline-flex items-center cursor-pointer">
                                    <div class="w-11 h-6 bg-gray-200 rounded-full peer transition-colors" 
                                         :class="editForm.status ? 'bg-green-600' : 'bg-gray-200'"></div>
                                    <div class="absolute left-[2px] top-[2px] bg-white border-gray-300 border rounded-full h-5 w-5 transition-transform" 
                                         :class="editForm.status ? 'translate-x-full border-white' : 'translate-x-0'"></div>
                                </div>
                                <span class="ml-3 text-sm font-medium" 
                                      :class="editForm.status ? 'text-green-700 font-bold' : 'text-gray-500'" 
                                      x-text="editForm.status ? 'Active (Can Login)' : 'Inactive (Suspended)'"></span>
                            </div>
                        </div>

                        {{-- 6. Address --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <textarea name="address" rows="2" x-model="editForm.address"
                                      class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm"></textarea>
                        </div>

                    </div>

                    {{-- Footer Action --}}
                    <div class="mt-8 flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
                        <button type="button" @click="closeModal('showEditModal')" class="px-5 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-lg bg-blue-600 text-white font-medium hover:bg-blue-700 shadow-sm transition-colors">
                            Update Changes
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>