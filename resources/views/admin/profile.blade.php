<x-app-layout>
    <x-slot name="header"></x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- 1. BREADCRUMB --}}
            <nav class="flex mb-6" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-blue-600">
                            {{-- ICON HOME --}}
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                            Dashboard
                        </a>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            <span class="ml-1 text-sm font-medium text-gray-900 md:ml-2">Profile</span>
                        </div>
                    </li>
                </ol>
            </nav>

            {{-- 2. HEADER SIMPLE --}}
            <div class="mb-8">
                <div class="flex items-center gap-4">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=ec4899&color=fff&size=80&bold=true" 
                         alt="{{ $user->name }}" 
                         class="w-16 h-16 rounded-full border-4 border-white shadow-sm">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800">Admin Settings</h2>
                        <p class="text-sm text-gray-500">Manage account details and system privileges.</p>
                    </div>
                </div>
            </div>

            {{-- 3. GRID LAYOUT --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- COLUMN 1: PERSONAL DETAILS --}}
                <div class="lg:col-span-1 space-y-6">
                    {{-- HAPUS class 'h-full' disini --}}
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            Personal Details
                        </h3>
                        
                        <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
                            @csrf
                            @method('patch')

                            {{-- Nama --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" required 
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm transition-all">
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>

                            {{-- Email --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-gray-400 font-normal">(Optional)</span></label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm transition-all">
                                <x-input-error class="mt-2" :messages="$errors->get('email')" />
                            </div>

                            {{-- Phone --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number <span class="text-gray-400 font-normal">(Optional)</span></label>
                                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="e.g. 08123456789"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm transition-all">
                                <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                            </div>

                            {{-- Address --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Address <span class="text-gray-400 font-normal">(Optional)</span></label>
                                <textarea name="address" rows="3" placeholder="Street name, City, etc."
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm transition-all">{{ old('address', $user->address) }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('address')" />
                            </div>

                            {{-- Checkbox Status Guru (Admin Only) --}}
                            <div class="pt-2">
                                <label class="flex items-start p-3 rounded-lg border border-gray-200 bg-gray-50 cursor-pointer hover:bg-gray-100 transition">
                                    <div class="flex items-center h-5">
                                        <input id="is_teacher" name="is_teacher" type="checkbox" value="1" 
                                            {{ old('is_teacher', $user->is_teacher) ? 'checked' : '' }}
                                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    </div>
                                    <div class="ml-3 text-xs">
                                        <span class="font-bold text-gray-700">Enable Teaching Status</span>
                                        <p class="text-gray-500 mt-0.5">Allows this admin to be assigned as a teacher in classes.</p>
                                    </div>
                                </label>
                            </div>

                            <div class="pt-4">
                                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-lg transition shadow-sm">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- COLUMN 2: UPDATE PASSWORD --}}
                <div class="lg:col-span-2">
                    {{-- HAPUS class 'h-full' disini juga --}}
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            Update Password
                        </h3>
                        <p class="text-sm text-gray-500 mb-6">Ensure your account is using a long, random password to stay secure.</p>

                        <form method="post" action="{{ route('password.update') }}" class="space-y-6">
                            @csrf
                            @method('put')

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Current Password <span class="text-red-500">*</span></label>
                                    <input type="password" name="current_password" 
                                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm transition-all">
                                    <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">New Password <span class="text-red-500">*</span></label>
                                    <input type="password" name="password" autocomplete="new-password" 
                                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm transition-all">
                                    <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password <span class="text-red-500">*</span></label>
                                    <input type="password" name="password_confirmation" autocomplete="new-password" 
                                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm transition-all">
                                    <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
                                </div>
                            </div>

                            <div class="flex justify-end pt-4">
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-6 rounded-lg transition shadow-sm">
                                    Update Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SWEETALERT --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const status = "{{ session('status') }}";
            if (status === 'profile-updated') {
                Swal.fire({icon: 'success', title: 'Success!', text: 'Profile updated.', timer: 2000, showConfirmButton: false});
            } else if (status === 'password-updated') {
                Swal.fire({icon: 'success', title: 'Success!', text: 'Password updated.', timer: 2000, showConfirmButton: false});
            }
        });
    </script>
</x-app-layout>