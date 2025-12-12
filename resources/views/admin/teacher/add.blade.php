<x-app-layout>
    <x-slot name="header"></x-slot>

    {{-- Main Container dengan background warna senada dengan referensi --}}
    <div class="p-6 bg-[#F3F4FF] min-h-screen font-sans">
        <div class="max-w-5xl mx-auto">

            {{-- Breadcrumb --}}
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
                <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition">Home</a>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <a href="{{ route('admin.teacher.index') }}" class="hover:text-blue-600 transition">Teachers</a>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="font-medium text-gray-900">Add New Teacher</span>
            </div>

            {{-- Page Title --}}
           <div class="mb-8">
                <h1 class="text-3xl font-bold bg-gradient-to-b from-blue-500 to-red-500 bg-clip-text text-transparent">
                    Add New Teacher
                </h1>
            </div>
            {{-- Form Card --}}
            <div class="bg-white rounded-[20px] shadow-sm border border-gray-100 p-8">
                
                {{-- Form Start --}}
                {{-- Penting: enctype="multipart/form-data" wajib ada untuk upload foto --}}
                <form action="{{ route('admin.teacher.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <h2 class="text-xl font-bold text-gray-800 mb-6 pb-4 border-b border-gray-100">Teacher Information</h2>

                    {{-- Grid Layout untuk Field --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

                        {{-- 1. Name & Username --}}
                        <div class="space-y-2">
                            <label for="name" class="text-sm font-semibold text-gray-700">Full Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" required placeholder="e.g. Richard Lim"
                                class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-200 transition duration-200 outline-none text-gray-700 placeholder-gray-400">
                        </div>

                        <div class="space-y-2">
                            <label for="username" class="text-sm font-semibold text-gray-700">Username <span class="text-red-500">*</span></label>
                            <input type="text" name="username" id="username" required placeholder="e.g. Richardlim7"
                                class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-200 transition duration-200 outline-none text-gray-700 placeholder-gray-400">
                        </div>

                        {{-- 2. Email & Phone --}}
                        <div class="space-y-2">
                            <label for="email" class="text-sm font-semibold text-gray-700">Email Address <span class="text-red-500">*</span></label>
                            <input type="email" name="email" id="email" required placeholder="e.g. teacher@school.com"
                                class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-200 transition duration-200 outline-none text-gray-700 placeholder-gray-400">
                        </div>

                        <div class="space-y-2">
                            <label for="phone" class="text-sm font-semibold text-gray-700">Phone Number <span class="text-red-500">*</span></label>
                            <input type="tel" name="phone" id="phone" required placeholder="e.g. 08123456789"
                                class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-200 transition duration-200 outline-none text-gray-700 placeholder-gray-400">
                        </div>

                        {{-- 3. Password & Classroom --}}
<div class="space-y-2">
    <label for="password" class="text-sm font-semibold text-gray-700">Password <span class="text-red-500">*</span></label>
    
    {{-- Wrapper Relative untuk posisi icon --}}
    <div class="relative" x-data="{ show: false }">
        <input 
            :type="show ? 'text' : 'password'" 
            name="password" 
            id="password" 
            required 
            placeholder="••••••••"
            class="w-full px-4 py-3 pr-12 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-200 transition duration-200 outline-none text-gray-700 placeholder-gray-400"
        >
        
        {{-- Tombol Toggle Icon --}}
        <button type="button" @click="show = !show" 
            class="absolute inset-y-0 right-0 px-4 flex items-center text-gray-400 hover:text-blue-600 focus:outline-none transition-colors"
            title="Toggle Password Visibility">
            
            {{-- Icon Mata Terbuka (Show) --}}
            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>

            {{-- Icon Mata Coret (Hide) --}}
            <svg x-show="show" style="display: none;" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.05 10.05 0 011.574-2.59M5.38 5.38a10.056 10.056 0 016.62-2.38c4.478 0 8.268 2.943 9.542 7a10.05 10.05 0 01-2.033 3.51M15 12a3 3 0 00-3-3m-1.5 7.5a3 3 0 01-3-3 3 3 0 013-3m6.75-4.5L5.25 19.5" />
            </svg>
        </button>
    </div>
</div>

                        {{-- 4. Type & Status Dropdowns --}}
                        <div class="space-y-2">
                            <label for="type" class="text-sm font-semibold text-gray-700">Teacher Type <span class="text-red-500">*</span></label>
                            <select name="type" id="type" required
                                class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-200 transition duration-200 outline-none text-gray-700 cursor-pointer appearance-none">
                                <option value="">Select Type</option>
                                <option value="Form Teacher">Form Teacher</option>
                                <option value="Local Teacher">Local Teacher</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label for="status" class="text-sm font-semibold text-gray-700">Status <span class="text-red-500">*</span></label>
                            <select name="status" id="status" required
                                class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-200 transition duration-200 outline-none text-gray-700 cursor-pointer appearance-none">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                        {{-- 5. Address (Full Width) --}}
                        <div class="space-y-2 md:col-span-2">
                            <label for="address" class="text-sm font-semibold text-gray-700"> Address</label>
                            <textarea name="address" id="address" rows="3" placeholder="Enter full address here..."
                                class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-200 transition duration-200 outline-none text-gray-700 placeholder-gray-400 resize-none"></textarea>
                        </div>

                         {{-- 6. Photo Upload (Drag & Drop Style) --}}
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
                                        PNG, JPG, GIF up to 2MB
                                    </p>
                                </div>
                            </div>
                        </div>

                    </div>
                    {{-- End Grid Layout --}}

                    {{-- Action Buttons --}}
                    <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-100">
                        <a href="{{ route('admin.teacher.index') }}"
                            class="px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition focus:outline-none focus:ring-2 focus:ring-gray-300">
                            Cancel
                        </a>
                        <button type="submit"
                            class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-blue-800 transition shadow-lg shadow-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Save Teacher
                        </button>
                    </div>

                </form>
                {{-- Form End --}}

            </div>
        </div>
    </div>
</x-app-layout>