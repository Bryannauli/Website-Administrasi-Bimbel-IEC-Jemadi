{{-- File: resources/views/admin/classes/partials/add-class-modal.blade.php --}}

<div x-show="showAddModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        
        {{-- Overlay --}}
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="closeModal('showAddModal')"></div>
        
        {{-- Modal Panel --}}
        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl w-full border border-gray-100">
            
            {{-- Header --}}
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900">Add New Class</h3>
                <button @click="closeModal('showAddModal')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-6">
                {{-- Form Create --}}
                <form action="{{ route('admin.classes.store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
                        
                        {{-- Bagian 1: Identitas Kelas --}}
                        <div class="space-y-4">
                            {{-- Class Name --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Class Name <span class="text-red-500">*</span></label>
                                <input type="text" name="name" value="{{ old('name') }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 @enderror" required placeholder="e.g. Level 1A">
                                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            {{-- Category --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Category <span class="text-red-500">*</span></label>
                                <select name="category" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('category') border-red-500 @enderror" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $cat) <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ ucwords(str_replace('_', ' ', $cat)) }}</option> @endforeach
                                </select>
                                @error('category') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            {{-- Academic Year --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Academic Year</label>
                                <select name="academic_year" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('academic_year') border-red-500 @enderror">
                                    <option value="2025" {{ old('academic_year', '2025') == '2025' ? 'selected' : '' }}>2025</option>
                                    <option value="2026" {{ old('academic_year') == '2026' ? 'selected' : '' }}>2026</option>
                                </select>
                                @error('academic_year') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        
                        {{-- Bagian 2: Waktu & Lokasi --}}
                        <div class="space-y-4">
                            {{-- Classroom --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Classroom <span class="text-red-500">*</span></label>
                                <input type="text" name="classroom" value="{{ old('classroom') }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('classroom') border-red-500 @enderror" required placeholder="e.g. Room 101">
                                @error('classroom') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            {{-- Period --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Period (Start - End)</label>
                                <div class="flex gap-2">
                                    <select name="start_month" class="w-1/2 border-gray-300 rounded-lg shadow-sm text-sm @error('start_month') border-red-500 @enderror">@foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $m)<option value="{{$m}}" {{ old('start_month') == $m ? 'selected' : '' }}>{{$m}}</option>@endforeach</select>
                                    <select name="end_month" class="w-1/2 border-gray-300 rounded-lg shadow-sm text-sm @error('end_month') border-red-500 @enderror">@foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $m)<option value="{{$m}}" {{ old('end_month') == $m ? 'selected' : '' }}>{{$m}}</option>@endforeach</select>
                                </div>
                                @if ($errors->has('start_month') || $errors->has('end_month')) <p class="text-red-500 text-xs mt-1">Month selection required.</p> @endif
                            </div>
                        </div>

                        {{-- Bagian 3: Guru --}}
                        <div class="md:col-span-2 grid grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg border border-gray-100">
                            {{-- Form Teacher --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Form Teacher</label>
                                <select name="form_teacher_id" class="w-full border-gray-300 rounded-lg shadow-sm text-sm @error('form_teacher_id') border-red-500 @enderror">
                                    <option value="">Select Teacher (Optional)</option>
                                    @foreach($teachers as $teacher) <option value="{{ $teacher->id }}" {{ old('form_teacher_id') == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option> @endforeach
                                </select>
                            </div>
                            {{-- Local Teacher --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Local Teacher</label>
                                <select name="local_teacher_id" class="w-full border-gray-300 rounded-lg shadow-sm text-sm @error('local_teacher_id') border-red-500 @enderror">
                                    <option value="">Select Teacher (Optional)</option>
                                    @foreach($teachers as $teacher) <option value="{{ $teacher->id }}" {{ old('local_teacher_id') == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option> @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Bagian 4: Jadwal & Tipe Guru (NEW SWITCH UI) --}}
                        <div class="md:col-span-2" x-data="{ selectedDays: {{ json_encode(old('days', [])) }} }">
                            <label class="block text-sm font-bold text-gray-700 mb-3 @error('days') text-red-500 @enderror">Schedule & Teacher Assignment <span class="text-red-500">*</span></label>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                                    <div class="p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
                                         :class="selectedDays.includes('{{ $day }}') ? 'bg-blue-50 border-blue-200' : ''">
                                        
                                        <div class="flex items-center justify-between">
                                            <label class="inline-flex items-center cursor-pointer w-full">
                                                {{-- Checkbox Hari --}}
                                                <input type="checkbox" name="days[]" value="{{ $day }}" 
                                                       x-model="selectedDays"
                                                       class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                                <span class="ml-2 text-gray-700 text-sm font-bold">{{ $day }}</span>
                                            </label>
                                        </div>

                                        {{-- SWITCH UI: Form / Local (Muncul saat hari dipilih) --}}
                                        <div x-show="selectedDays.includes('{{ $day }}')" x-transition 
                                             class="mt-3" 
                                             x-data="{ tType: '{{ old("teacher_types.$day", 'form') }}' }">
                                            
                                            {{-- Hidden input menyimpan nilai sebenarnya --}}
                                            <input type="hidden" name="teacher_types[{{ $day }}]" :value="tType">

                                            {{-- Toggle Button Group --}}
                                            <div class="flex bg-gray-100 p-1 rounded-md">
                                                {{-- Button Form --}}
                                                <button type="button" 
                                                    @click="tType = 'form'"
                                                    :class="tType === 'form' ? 'bg-white text-blue-700 shadow-sm ring-1 ring-black/5 font-bold' : 'text-gray-500 hover:text-gray-700 font-medium'"
                                                    class="flex-1 py-1.5 text-xs rounded transition-all text-center">
                                                    Form
                                                </button>

                                                {{-- Button Local --}}
                                                <button type="button" 
                                                    @click="tType = 'local'"
                                                    :class="tType === 'local' ? 'bg-white text-purple-700 shadow-sm ring-1 ring-black/5 font-bold' : 'text-gray-500 hover:text-gray-700 font-medium'"
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
                        
                        {{-- Bagian 5: Jam --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Class Time <span class="text-red-500">*</span></label>
                            <div class="flex items-center gap-2">
                                <input type="time" name="start_time" value="{{ old('start_time') }}" class="border-gray-300 rounded-lg shadow-sm @error('start_time') border-red-500 @enderror" required>
                                <span class="text-gray-400 font-bold">-</span>
                                <input type="time" name="end_time" value="{{ old('end_time') }}" class="border-gray-300 rounded-lg shadow-sm @error('end_time') border-red-500 @enderror" required>
                            </div>
                            @if ($errors->has('start_time') || $errors->has('end_time')) <p class="text-red-500 text-xs mt-1">Time fields are required.</p> @endif
                        </div>
                    </div>
                    
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        {{-- CANCEL LOGIC: Close Modal --}}
                        <button type="button" @click="closeModal('showAddModal')" class="px-5 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 bg-blue-700 text-white font-medium rounded-lg hover:bg-blue-800 shadow-sm transition">Create Class</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>