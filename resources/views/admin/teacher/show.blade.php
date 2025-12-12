PHP

<?php

// TeacherAdminController.php - Dummy data untuk attendance (sesuai controller sebelumnya)
$teacher->status = $teacher->is_active ?? 0;
$teacher->photo = $teacher->profile_photo_path ?? null;

// Ganti data attendance dengan format yang sesuai dengan view blade Anda
$attendance_records = collect([
    (object)['date' => now()->subDays(1)->format('Y-m-d'), 'status' => 'present'],
    (object)['date' => now()->subDays(2)->format('Y-m-d'), 'status' => 'late'],
    (object)['date' => now()->subDays(3)->format('Y-m-d'), 'status' => 'absent'],
    (object)['date' => now()->subDays(4)->format('Y-m-d'), 'status' => 'present'],
    (object)['date' => now()->subDays(5)->format('Y-m-d'), 'status' => 'sick'],
]);

?>

<x-app-layout>
    <x-slot name="header"></x-slot>

    {{-- KONTEN UTAMA (Bungkus dengan x-data untuk Alpine) --}}
    <div x-data="{ isEditModalOpen: false }" class="py-6 bg-[#F3F4FF] min-h-screen font-sans">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- 1. BREADCRUMB --}}
            <nav class="flex mb-5" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-blue-600">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                            Home
                        </a>
                    </li>
                    <li class="inline-flex items-center">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            <a href="{{ route('admin.teacher.index') }}" class="ml-1 text-sm font-medium text-gray-500 hover:text-blue-600 md:ml-2">Teachers</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            <span class="ml-1 text-sm font-medium text-gray-900 md:ml-2">{{ $teacher->name }}</span>
                        </div>
                    </li>
                </ol>
            </nav>

            {{-- 2. HEADER DETAIL & ACTION BUTTONS --}}
            <div class="flex items-center justify-between mb-8">
                <h1 class="text-3xl font-bold text-gray-800">
                    Teacher Details: <span class="text-blue-600">{{ $teacher->name }}</span>
                </h1>
                
                {{-- Edit Button (MODAL TRIGGER) --}}
                <button @click="isEditModalOpen = true" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium transition-colors shadow-sm">
                    Edit Teacher
                </button>
            </div>

            {{-- 3. CARD DETAIL --}}
            <div class="bg-white rounded-[20px] shadow-sm border border-gray-100 p-6 mb-8">
                <div class="grid grid-cols-2 gap-4 text-gray-700">
                    <p><strong>ID:</strong> {{ $teacher->id }}</p>
                    <p><strong>Username:</strong> {{ $teacher->username }}</p>
                    <p><strong>Email:</strong> {{ $teacher->email }}</p>
                    <p><strong>Phone:</strong> {{ $teacher->phone }}</p>
                    <p><strong>Type:</strong> {{ $teacher->type }}</p>
                    <p><strong>Status:</strong> 
                        <span class="font-semibold {{ $teacher->status == 1 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $teacher->status == 1 ? 'Active' : 'Inactive' }}
                        </span>
                    </p>
                    <p class="col-span-2"><strong>Address:</strong> {{ $teacher->address ?? '-' }}</p>
                    
                    {{-- Asumsi Anda memiliki path foto --}}
                    @if($teacher->photo)
                        <div class="col-span-2">
                            <strong>Photo:</strong>
                            <img src="{{ asset('storage/' . $teacher->photo) }}" alt="Profile Photo" class="w-20 h-20 rounded-full object-cover mt-2">
                        </div>
                    @endif
                </div>
            </div>
            
            {{-- 4. ATTENDANCE HISTORY --}}
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Attendance History</h2>
            <div class="bg-white rounded-[20px] shadow-sm border border-gray-100 p-6">
                @if(isset($attendance_records) && $attendance_records->count())
                    <ul class="space-y-3">
                        @foreach($attendance_records as $record)
                            <li class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <span class="font-medium text-gray-700">{{ \Carbon\Carbon::parse($record->date)->format('d M Y') }}</span>
                                <div class="flex items-center gap-2 text-sm font-semibold 
                                    @if($record->status == 'present') text-green-600 
                                    @elseif($record->status == 'late') text-yellow-600 
                                    @elseif($record->status == 'absent') text-red-600 
                                    @endif">
                                    {{ Str::ucfirst($record->status) }}
                                    
                                    @if($record->status == 'present')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    @elseif($record->status == 'late') 
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    @elseif($record->status == 'absent') 
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    @else 
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="w-full text-center py-8 text-gray-400 bg-gray-50 rounded-xl border border-dashed border-gray-200">No attendance history available.</div>
                @endif
            </div>

        </div>

        {{-- MODAL SECTION: EDIT TEACHER (SUDAH DIPERBAIKI) --}}
        <div x-show="isEditModalOpen" 
            x-transition:enter="ease-out duration-300" 
            x-transition:enter-start="opacity-0" 
            x-transition:enter-end="opacity-100" 
            x-transition:leave="ease-in duration-200" 
            x-transition:leave-start="opacity-100" 
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50"> 
            
            <div class="flex items-center justify-center min-h-screen p-4" @click.outside="isEditModalOpen = false">
                
                <div x-show="isEditModalOpen"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="bg-white rounded-[20px] shadow-2xl w-full max-w-4xl p-8 transform transition-all mx-auto relative">
                    
                    {{-- Tombol Close Modal --}}
                    <button type="button" @click="isEditModalOpen = false" 
                            class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 focus:outline-none z-10">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Edit Teacher: {{ $teacher->name }}</h2>
                    
                    {{-- Form Start --}}
                    <form action="{{ route('admin.teacher.update', $teacher->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT') {{-- PENTING: Untuk Update data --}}

                        <h3 class="text-xl font-bold text-gray-800 mb-6 pb-4 border-b border-gray-100">Teacher Information</h3>

                        {{-- Grid Layout untuk Field --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

                            {{-- 1. Name & Username (Pre-filled) --}}
                            <div class="space-y-2">
                                <label for="name" class="text-sm font-semibold text-gray-700">Full Name <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="name" required placeholder="e.g. Richard Lim"
                                    value="{{ old('name', $teacher->name) }}"
                                    class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-200 transition duration-200 outline-none text-gray-700 placeholder-gray-400">
                            </div>

                            <div class="space-y-2">
                                <label for="username" class="text-sm font-semibold text-gray-700">Username <span class="text-red-500">*</span></label>
                                <input type="text" name="username" id="username" required placeholder="e.g. Richardlim7"
                                    value="{{ old('username', $teacher->username) }}"
                                    class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-200 transition duration-200 outline-none text-gray-700 placeholder-gray-400">
                            </div>

                            {{-- 2. Email & Phone (Pre-filled) --}}
                            <div class="space-y-2">
                                <label for="email" class="text-sm font-semibold text-gray-700">Email Address <span class="text-red-500">*</span></label>
                                <input type="email" name="email" id="email" required placeholder="e.g. teacher@school.com"
                                    value="{{ old('email', $teacher->email) }}"
                                    class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-200 transition duration-200 outline-none text-gray-700 placeholder-gray-400">
                            </div>

                            <div class="space-y-2">
                                <label for="phone" class="text-sm font-semibold text-gray-700">Phone Number <span class="text-red-500">*</span></label>
                                <input type="tel" name="phone" id="phone" required placeholder="e.g. 08123456789"
                                    value="{{ old('phone', $teacher->phone) }}"
                                    class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-200 transition duration-200 outline-none text-gray-700 placeholder-gray-400">
                            </div>

                            {{-- 3. Password (Optional) --}}
                            <div class="space-y-2">
                                <label for="password" class="text-sm font-semibold text-gray-700">New Password</label>
                                
                                <div class="relative" x-data="{ show: false }">
                                    <input 
                                        :type="show ? 'text' : 'password'" 
                                        name="password" 
                                        id="password" 
                                        placeholder="••••••••"
                                        class="w-full px-4 py-3 pr-12 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-200 transition duration-200 outline-none text-gray-700 placeholder-gray-400"
                                    >
                                    
                                    <button type="button" @click="show = !show" 
                                        class="absolute inset-y-0 right-0 px-4 flex items-center text-gray-400 hover:text-blue-600 focus:outline-none transition-colors"
                                        title="Toggle Password Visibility">
                                        
                                        <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>

                                        <svg x-show="show" style="display: none;" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.05 10.05 0 011.574-2.59M5.38 5.38a10.056 10.056 0 016.62-2.38c4.478 0 8.268 2.943 9.542 7a10.05 10.05 0 01-2.033 3.51M15 12a3 3 0 00-3-3m-1.5 7.5a3 3 0 01-3-3 3 3 0 013-3m6.75-4.5L5.25 19.5" />
                                        </svg>
                                    </button>
                                </div>
                                <p class="text-xs text-gray-500 italic">Kosongkan jika tidak ingin mengganti password.</p>
                            </div>

                            {{-- 4. Type & Status Dropdowns (Selected) --}}
                            <div class="space-y-2">
                                <label for="type" class="text-sm font-semibold text-gray-700">Teacher Type <span class="text-red-500">*</span></label>
                                <select name="type" id="type" required
                                    class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-200 transition duration-200 outline-none text-gray-700 cursor-pointer appearance-none">
                                    <option value="">Select Type</option>
                                    <option value="Form Teacher" {{ old('type', $teacher->type) == 'Form Teacher' ? 'selected' : '' }}>Form Teacher</option>
                                    <option value="Local Teacher" {{ old('type', $teacher->type) == 'Local Teacher' ? 'selected' : '' }}>Local Teacher</option>
                                </select>
                            </div>

                            <div class="space-y-2">
                                <label for="status" class="text-sm font-semibold text-gray-700">Status <span class="text-red-500">*</span></label>
                                {{-- Field status ini mengacu ke $teacher->status dari model Anda --}}
                                <select name="status" id="status" required
                                    class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-200 transition duration-200 outline-none text-gray-700 cursor-pointer appearance-none">
                                    <option value="1" {{ old('status', $teacher->status) == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status', $teacher->status) == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>

                            {{-- 5. Address (Pre-filled) --}}
                            <div class="space-y-2 md:col-span-2">
                                <label for="address" class="text-sm font-semibold text-gray-700"> Address</label>
                                <textarea name="address" id="address" rows="3" placeholder="Enter full address here..."
                                    class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-200 transition duration-200 outline-none text-gray-700 placeholder-gray-400 resize-none">{{ old('address', $teacher->address) }}</textarea>
                            </div>

                            {{-- 6. Photo Upload --}}
                            <div class="space-y-2 md:col-span-2">
                                <span class="text-sm font-semibold text-gray-700">Profile Photo</span>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl bg-gray-50 hover:bg-blue-50 hover:border-blue-300 transition group cursor-pointer relative">
                                    <div class="space-y-2 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400 group-hover:text-blue-500 transition" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600 justify-center">
                                            <label for="photo" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500 px-2">
                                                <span>Upload a file</span>
                                                <input id="photo" name="photo" type="file" class="sr-only" accept="image/*">
                                            </label>
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">
                                            PNG, JPG, GIF up to 2MB. Kosongkan jika tidak ingin mengubah.
                                        </p>
                                    </div>
                                </div>
                            </div>

                        </div>
                        {{-- End Grid Layout --}}

                        {{-- Action Buttons --}}
                        <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-100">
                            <button type="button" @click="isEditModalOpen = false"
                                class="px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition focus:outline-none focus:ring-2 focus:ring-gray-300">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white font-semibold rounded-xl hover:from-green-700 hover:to-green-800 transition shadow-lg shadow-green-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                Update Teacher
                            </button>
                        </div>

                    </form>
                    {{-- Form End --}}

            </div>
        </div>
    </div>
</x-app-layout>