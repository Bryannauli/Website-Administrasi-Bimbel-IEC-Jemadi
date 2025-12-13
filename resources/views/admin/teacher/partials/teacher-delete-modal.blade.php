{{-- 
    ===========================================
    PARTIAL: MODAL DELETE/DEACTIVATION TEACHER (Soft Delete: is_active = 0)
    
    Persyaratan Alpine.js di elemen parent:
    1. showDeleteModal: State boolean untuk menampilkan/menyembunyikan modal.
    2. deleteUrl: URL untuk aksi form DELETE.
    3. deleteName: Nama guru yang akan dihapus (untuk konfirmasi).
    4. closeModal(modalVar): Fungsi untuk menutup modal.
    ===========================================
--}}
<div x-show="showDeleteModal" 
    x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 backdrop-blur-sm"
    style="display: none;">
    
    <div class="flex items-center justify-center min-h-screen p-4" @click.outside="closeModal('showDeleteModal')">
        <div x-show="showDeleteModal"
            x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="bg-white rounded-[20px] shadow-2xl w-full max-w-lg p-8 transform transition-all relative">
            
            <button type="button" @click="closeModal('showDeleteModal')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 focus:outline-none"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
            
            <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                <h3 class="text-xl font-bold text-gray-900 mt-3">Deactivate Teacher</h3>
                <p class="text-gray-500 mt-2">Are you sure you want to deactivate teacher <strong x-text="deleteName" class="font-bold text-red-600"></strong>? This action will set their status to **Inactive (0)** and they will no longer be able to log in.</p>
            </div>
            
            {{-- Form Delete (Deaktivasi) --}}
            <form :action="deleteUrl" method="POST" class="mt-6">
                @csrf
                @method('DELETE')
                
                {{-- Kita bisa menambahkan hidden field untuk penanda soft delete jika controller membutuhkannya --}}
                {{-- <input type="hidden" name="soft_delete" value="1"> --}}

                <div class="flex items-center justify-center gap-4">
                    <button type="button" @click="closeModal('showDeleteModal')" class="px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition">Cancel</button>
                    <button type="submit" class="px-6 py-3 bg-red-600 text-white font-semibold rounded-xl hover:bg-red-700 transition shadow-lg shadow-red-200">Yes, Deactivate</button>
                </div>
            </form>

        </div>
    </div>
</div>