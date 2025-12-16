{{-- File: resources/views/admin/classes/partials/edit-class-modal.blade.php --}}

<div x-show="showEditModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="closeModal('showEditModal')"></div>
        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl w-full border border-gray-100">
            
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900">Edit Class</h3>
                <button @click="closeModal('showEditModal')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div class="p-6">
                {{-- FORM UPDATE --}}
                <form :action="getUpdateUrl()" method="POST"> 
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
                        
                        {{-- Bagian 1: Identitas --}}
                        <div class="space-y-4">
                            {{-- Class Name --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Class Name</label>
                                <input type="text" name="name" x-model="editForm.name" class="w-full border-gray-300 rounded-lg shadow-sm @error('name') border-red-500 @enderror">
                                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            {{-- Category --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Category</label>
                                <select name="category" x-model="editForm.category" class="w-full border-gray-300 rounded-lg shadow-sm @error('category') border-red-500 @enderror">
                                    @foreach($categories as $cat) <option value="{{ $cat }}">{{ ucwords(str_replace('_', ' ', $cat)) }}</option> @endforeach
                                </select>
                                @error('category') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            {{-- Academic Year --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Academic Year</label>
                                
                                <input type="number" 
                                    name="academic_year" 
                                    {{-- Default: Tahun saat ini --}}
                                    value="{{ old('academic_year', date('Y')) }}" 
                                    
                                    {{-- Min: 5 tahun ke belakang (Misal skrg 2025 -> Min 2020) --}}
                                    min="{{ date('Y') - 5 }}" 
                                    
                                    {{-- Max: 5 tahun ke depan (Misal skrg 2025 -> Max 2030) --}}
                                    max="{{ date('Y') + 5 }}" 
                                    
                                    step="1"
                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('academic_year') border-red-500 @enderror"
                                    placeholder="e.g. {{ date('Y') }}">
                                    
                                @error('academic_year') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        
                        {{-- Bagian 2: Lokasi & Periode --}}
                        <div class="space-y-4">
                            {{-- Classroom --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Classroom</label>
                                <input type="text" name="classroom" x-model="editForm.classroom" class="w-full border-gray-300 rounded-lg shadow-sm @error('classroom') border-red-500 @enderror">
                                @error('classroom') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            {{-- Period --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Period</label>
                                <div class="flex gap-2">
                                    <select name="start_month" x-model="editForm.start_month" class="w-1/2 border-gray-300 rounded-lg text-sm @error('start_month') border-red-500 @enderror">@foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $m)<option value="{{$m}}">{{$m}}</option>@endforeach</select>
                                    <select name="end_month" x-model="editForm.end_month" class="w-1/2 border-gray-300 rounded-lg text-sm @error('end_month') border-red-500 @enderror">@foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $m)<option value="{{$m}}">{{$m}}</option>@endforeach</select>
                                </div>
                                @if ($errors->has('start_month') || $errors->has('end_month')) <p class="text-red-500 text-xs mt-1">Month selection required.</p> @endif
                            </div>
                        </div>

                        {{-- Bagian 3: Guru --}}
                        <div class="md:col-span-2 grid grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg border border-gray-100">
                            {{-- Form Teacher --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Form Teacher</label>
                                <select name="form_teacher_id" x-model="editForm.form_teacher_id" class="w-full border-gray-300 rounded-lg shadow-sm text-sm @error('form_teacher_id') border-red-500 @enderror">
                                    <option value="">Select (Optional)</option>
                                    @foreach($teachers as $teacher) <option value="{{ $teacher->id }}">{{ $teacher->name }}</option> @endforeach
                                </select>
                            </div>
                            {{-- Local Teacher --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Local Teacher</label>
                                <select name="local_teacher_id" x-model="editForm.local_teacher_id" class="w-full border-gray-300 rounded-lg shadow-sm text-sm @error('local_teacher_id') border-red-500 @enderror">
                                    <option value="">Select (Optional)</option>
                                    @foreach($teachers as $teacher) <option value="{{ $teacher->id }}">{{ $teacher->name }}</option> @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Bagian 4: Jadwal & Tipe Guru (SWITCH UI) --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-3 @error('days') text-red-500 @enderror">Schedule & Teacher Assignment <span class="text-red-500">*</span></label>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                                    <div class="p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
                                        :class="editForm.days.includes('{{ $day }}') ? 'bg-blue-50 border-blue-200' : ''">
                                        
                                        <div class="flex items-center justify-between">
                                            <label class="inline-flex items-center cursor-pointer w-full">
                                                {{-- Checkbox Hari --}}
                                                <input type="checkbox" name="days[]" value="{{ $day }}" 
                                                    :checked="editForm.days.includes('{{ $day }}')" 
                                                    x-model="editForm.days" 
                                                    class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                                <span class="ml-2 text-gray-700 text-sm font-bold">{{ $day }}</span>
                                            </label>
                                        </div>

                                        {{-- SWITCH UI: Form / Local (Muncul saat hari dipilih) --}}
                                        <div x-show="editForm.days.includes('{{ $day }}')" x-transition class="mt-3">
                                            
                                            <input type="hidden" name="teacher_types[{{ $day }}]" 
                                                   :value="editForm.teacher_types['{{ $day }}'] || 'form'">

                                            <div class="flex bg-gray-100 p-1 rounded-md">
                                                <button type="button" 
                                                    @click="editForm.teacher_types['{{ $day }}'] = 'form'"
                                                    :class="(editForm.teacher_types['{{ $day }}'] || 'form') === 'form' ? 'bg-white text-blue-700 shadow-sm ring-1 ring-black/5 font-bold' : 'text-gray-500 hover:text-gray-700 font-medium'"
                                                    class="flex-1 py-1.5 text-xs rounded transition-all text-center">
                                                    Form
                                                </button>

                                                <button type="button" 
                                                    @click="editForm.teacher_types['{{ $day }}'] = 'local'"
                                                    :class="editForm.teacher_types['{{ $day }}'] === 'local' ? 'bg-white text-purple-700 shadow-sm ring-1 ring-black/5 font-bold' : 'text-gray-500 hover:text-gray-700 font-medium'"
                                                    class="flex-1 py-1.5 text-xs rounded transition-all text-center">
                                                    Local
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('days') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Bagian 5: Waktu & Status (LAYOUT DIPERBAIKI DENGAN TOGGLE BARU) --}}
                        <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-5 items-end">
                            {{-- Class Time --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Class Time <span class="text-red-500">*</span></label>
                                <div class="flex items-center gap-2">
                                    <input type="time" name="start_time" x-model="editForm.time_start" class="w-full border-gray-300 rounded-lg shadow-sm @error('start_time') border-red-500 @enderror">
                                    <span class="text-gray-400 font-bold">-</span>
                                    <input type="time" name="end_time" x-model="editForm.time_end" class="w-full border-gray-300 rounded-lg shadow-sm @error('end_time') border-red-500 @enderror">
                                </div>
                                @if ($errors->has('start_time') || $errors->has('end_time')) <p class="text-red-500 text-xs mt-1">Time fields are required.</p> @endif
                            </div>

                            {{-- STATUS TOGGLE (MODERN STYLE) --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Class Status</label>
                                
                                <div class="bg-gray-50 rounded-lg p-3 border border-gray-200 flex items-center h-[42px]">
                                    {{-- Hidden Input agar nilai terkirim ke backend --}}
                                    <input type="hidden" name="status" :value="editForm.status">
                                    
                                    {{-- Toggle Switch Component --}}
                                    <div @click="editForm.status = (editForm.status === 'active' ? 'inactive' : 'active')" 
                                         class="relative inline-flex items-center cursor-pointer shrink-0">
                                        
                                        {{-- Background Pill --}}
                                        <div class="w-11 h-6 bg-gray-200 rounded-full peer transition-colors duration-200 ease-in-out"
                                             :class="editForm.status === 'active' ? 'bg-green-600' : 'bg-gray-300'">
                                        </div>
                                        
                                        {{-- Sliding Circle --}}
                                        <div class="absolute left-[2px] top-[2px] bg-white border border-gray-300 rounded-full h-5 w-5 transition-transform duration-200 ease-in-out shadow-sm"
                                             :class="editForm.status === 'active' ? 'translate-x-full border-transparent' : 'translate-x-0'">
                                        </div>
                                    </div>

                                    {{-- Text Label --}}
                                    <span class="ml-3 text-sm font-medium select-none cursor-pointer" 
                                          @click="editForm.status = (editForm.status === 'active' ? 'inactive' : 'active')"
                                          :class="editForm.status === 'active' ? 'text-green-700 font-bold' : 'text-gray-500'">
                                        <span x-text="editForm.status === 'active' ? 'Active (Running)' : 'Inactive (Archived)'"></span>
                                    </span>
                                </div>
                            </div>
                        </div>

                    </div>
                    
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <button type="button" @click="closeModal('showEditModal')" class="px-5 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 bg-blue-700 text-white font-medium rounded-lg hover:bg-blue-800 shadow-sm transition">Update Class</button>
                    </div>
                </form>

                {{-- DANGER ZONE --}}
                <div class="bg-red-50 rounded-xl shadow-sm border border-red-200 overflow-hidden mt-8">
                    <div class="px-6 py-4 flex flex-col md:flex-row items-center justify-between gap-4">
                        <div>
                            <h3 class="text-sm font-bold text-red-800 uppercase tracking-wider">Danger Zone</h3>
                            <p class="text-xs text-red-600 mt-1">Deleting this class will move it to trash.</p>
                        </div>
                        
                        <button type="button" @click="confirmDelete()" class="whitespace-nowrap px-4 py-2 bg-red-600 text-white hover:bg-red-700 rounded-lg font-medium text-sm transition-all shadow-sm">
                            Delete Class
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>