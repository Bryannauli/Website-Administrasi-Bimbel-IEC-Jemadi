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
                
                {{-- Form Start --}}
                <form :action="updateUrl" method="POST" x-data="{ showResetPassword: false }">
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
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm bg-white @error('username') border-red-500 @enderror">
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

                        {{-- 5. Address (Dipindah ke atas Status) --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <textarea name="address" rows="2" x-model="editForm.address"
                                      class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm"></textarea>
                        </div>

                        {{-- 6. Status (Sekarang di bawah Address) --}}
                        <div class="md:col-span-2 bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Teacher Status</label>
                            <div class="flex items-center">
                                <input type="hidden" name="status" :value="editForm.status ? 1 : 0">
                                <div @click="editForm.status = !editForm.status" class="relative inline-flex items-center cursor-pointer">
                                    <div class="w-11 h-6 bg-gray-200 rounded-full peer transition-colors" :class="editForm.status ? 'bg-green-600' : 'bg-gray-200'"></div>
                                    <div class="absolute left-[2px] top-[2px] bg-white border-gray-300 border rounded-full h-5 w-5 transition-transform" :class="editForm.status ? 'translate-x-full border-white' : 'translate-x-0'"></div>
                                </div>
                                <span class="ml-3 text-sm font-medium" :class="editForm.status ? 'text-green-700 font-bold' : 'text-gray-500'" x-text="editForm.status ? 'Active (Can Login)' : 'Inactive (Suspended)'"></span>
                            </div>
                        </div>

                        {{-- 7. SECURITY ZONE (RESET PASSWORD) --}}
                        <div class="md:col-span-2 border-t border-gray-100 pt-4 mt-2">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-sm font-bold text-gray-800">Security</h4>
                                    <p class="text-xs text-gray-500">Only change password if the teacher forgot it.</p>
                                </div>
                                <button type="button" 
                                    @click="showResetPassword = !showResetPassword"
                                    class="text-sm font-medium text-blue-600 hover:text-blue-800 underline focus:outline-none">
                                    <span x-text="showResetPassword ? 'Cancel Reset' : 'Reset Password'"></span>
                                </button>
                            </div>

                            {{-- Input Password Muncul hanya jika diklik --}}
                            <div x-show="showResetPassword" x-transition class="mt-3 bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                                <label class="block text-sm font-bold text-gray-700 mb-1">New Password</label>
                                <div class="relative" x-data="{ showParams: false }">
                                    <input :type="showParams ? 'text' : 'password'" name="password" placeholder="Enter new password (min 8 chars)"
                                           class="w-full rounded-lg border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 shadow-sm pr-10">
                                    <button type="button" @click="showParams = !showParams" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                                        <svg x-show="!showParams" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                        <svg x-show="showParams" style="display: none;" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.05 10.05 0 011.574-2.59M5.38 5.38a10.056 10.056 0 016.62-2.38c4.478 0 8.268 2.943 9.542 7a10.05 10.05 0 01-2.033 3.51M15 12a3 3 0 00-3-3m-1.5 7.5a3 3 0 01-3-3 3 3 0 013-3m6.75-4.5L5.25 19.5" /></svg>
                                    </button>
                                </div>
                                <p class="text-xs text-yellow-700 mt-1">⚠️ Leave empty if you don't want to change the password.</p>
                            </div>
                        </div>

                    </div>

                    {{-- Action Buttons --}}
                    <div class="mt-8 flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
                        <button type="button" @click="closeModal('showEditModal')" class="px-5 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-700 font-medium hover:bg-gray-50 transition-colors">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 rounded-lg bg-blue-600 text-white font-medium hover:bg-blue-700 shadow-sm transition-colors">Update Changes</button>
                    </div>
                </form>

                {{-- DANGER ZONE (SOFT DELETE) --}}
                <div class="bg-red-50 rounded-xl shadow-sm border border-red-200 overflow-hidden mt-8">
                    <div class="px-6 py-4 flex flex-col md:flex-row items-center justify-between gap-4">
                        <div>
                            <h3 class="text-sm font-bold text-red-800 uppercase tracking-wider">Danger Zone</h3>
                            <p class="text-xs text-red-600 mt-1">Deleting this teacher will move data to trash (Soft Delete).</p>
                        </div>
                        
                        <button type="button" @click="confirmDelete()" class="whitespace-nowrap px-4 py-2 bg-red-600 text-white hover:bg-red-700 rounded-lg font-medium text-sm transition-all shadow-sm">
                            Delete Teacher
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>