{{-- 
    ===========================================
    PARTIAL: MODAL EDIT TEACHER
    Dibuat agar bisa digunakan di index.blade.php dan show.blade.php
    
    Persyaratan Alpine.js di elemen parent:
    1. showEditModal: State boolean untuk menampilkan/menyembunyikan modal.
    2. editForm: Objek data form (name, username, email, phone, type, status, address).
    3. updateUrl: URL untuk aksi form PUT/UPDATE.
    4. closeModal(modalVar): Fungsi untuk menutup modal (terutama untuk menghilangkan query string error).
    
    Catatan: Gunakan x-bind/x-model/action binding langsung ke variabel Alpine.js.
    ===========================================
--}}
<div x-show="showEditModal" 
    x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 backdrop-blur-sm"
    style="display: none;">
    
    <div class="flex items-center justify-center min-h-screen p-4" @click.outside="closeModal('showEditModal')">
        <div x-show="showEditModal"
            x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="bg-white rounded-[20px] shadow-2xl w-full max-w-4xl p-8 transform transition-all relative">
            
            <button type="button" @click="closeModal('showEditModal')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 focus:outline-none"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
            
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Edit Teacher</h2>
            
            {{-- Action URL diambil dari Alpine Variable --}}
            <form :action="updateUrl" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    {{-- Name --}}
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" x-model="editForm.name" class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:ring-2 focus:ring-blue-200 focus:bg-white transition" required>
                    </div>
                    {{-- Username --}}
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700">Username <span class="text-red-500">*</span></label>
                        <input type="text" name="username" x-model="editForm.username" class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:ring-2 focus:ring-blue-200 focus:bg-white transition" required>
                    </div>
                    {{-- Email --}}
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700">Email Address <span class="text-red-500">*</span></label>
                        <input type="email" name="email" x-model="editForm.email" class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:ring-2 focus:ring-blue-200 focus:bg-white transition" required>
                    </div>
                    {{-- Phone --}}
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700">Phone Number</label>
                        <input type="tel" name="phone" x-model="editForm.phone" class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:ring-2 focus:ring-blue-200 focus:bg-white transition">
                    </div>
                    {{-- Password (Optional) --}}
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700">New Password <span class="text-gray-400 font-normal">(Optional)</span></label>
                        <input type="password" name="password" placeholder="Leave blank to keep current" class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:ring-2 focus:ring-blue-200 focus:bg-white transition">
                    </div>
                    {{-- Type --}}
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700">Teacher Type <span class="text-red-500">*</span></label>
                        <select name="type" x-model="editForm.type" class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:ring-2 focus:ring-blue-200 focus:bg-white transition">
                            <option value="Form Teacher">Form Teacher</option>
                            <option value="Local Teacher">Local Teacher</option>
                        </select>
                    </div>
                    {{-- Status --}}
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700">Status <span class="text-red-500">*</span></label>
                        <select name="status" x-model="editForm.status" class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:ring-2 focus:ring-blue-200 focus:bg-white transition">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    {{-- Address --}}
                    <div class="space-y-2 md:col-span-2">
                        <label class="text-sm font-semibold text-gray-700">Address</label>
                        <textarea name="address" x-model="editForm.address" rows="3" class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:ring-2 focus:ring-blue-200 focus:bg-white transition resize-none"></textarea>
                    </div>
                </div>

                {{-- Footer: HANYA TOMBOL CANCEL & UPDATE --}}
                <div class="flex items-center justify-end pt-6 border-t border-gray-100">
                    <div class="flex gap-4">
                        <button type="button" @click="closeModal('showEditModal')" class="px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition">Cancel</button>
                        <button type="submit" class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition shadow-lg shadow-blue-200">Update Teacher</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>