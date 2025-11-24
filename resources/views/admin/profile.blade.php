<x-app-layout>
    {{-- 1. Mengisi Slot Header --}}
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900 font-medium">Home</a>
            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
            </svg>
            <span class="text-gray-900 font-medium">Profile</span>
        </div>
    </x-slot>

    {{-- 2. Konten Utama --}}
    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Kartu Header Profile --}}
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="relative">
                        {{-- Avatar Dinamis --}}
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=ec4899&color=fff&size=80" 
                             alt="{{ $user->name }}" 
                             class="w-20 h-20 rounded-full">
                        
                        {{-- Tombol Edit Foto (Hanya UI) --}}
                        <button class="absolute bottom-0 right-0 w-8 h-8 bg-white rounded-full flex items-center justify-center shadow-lg border-2 border-white hover:bg-gray-50">
                            <svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                            </svg>
                        </button>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">{{ $user->name }}</h1>
                        <p class="text-blue-600 font-medium">{{ $user->email }}</p>
                    </div>
                </div>
                <span class="px-4 py-2 bg-purple-100 text-purple-700 rounded-lg font-medium">{{ ucfirst($user->role ?? 'User') }}</span>
            </div>
        </div>

        {{-- Form Personal Details --}}
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-6">Personal Details</h2>
            
            <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
                @csrf
                @method('patch')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Nama Lengkap --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all">
                        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Email --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all">
                        @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    {{-- Checkbox Status Guru (Ditambahkan) --}}
                    <div class="md:col-span-2 bg-gray-50 p-4 rounded-lg border border-gray-200 mt-2">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                {{-- Menggunakan optional($user)->is_teacher untuk mencegah error jika kolom belum ada di DB --}}
                                <input id="is_teacher" name="is_teacher" type="checkbox" value="1" 
                                    {{ old('is_teacher', optional($user)->is_teacher) ? 'checked' : '' }}
                                    class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 transition-all cursor-pointer">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="is_teacher" class="font-bold text-gray-700 cursor-pointer">
                                    Enable Teaching Status
                                </label>
                                <p class="text-gray-500 mt-0.5">
                                    Check this box if this user acts as a Teacher in classes.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end mt-6">
                    <button type="submit" class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        Save Personal Details
                    </button>
                </div>
            </form>
        </div>

        {{-- Form Change Password --}}
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <h2 class="text-xl font-bold text-gray-800 mb-6">Change Password</h2>
            
            <form method="post" action="{{ route('password.update') }}" class="space-y-6">
                @csrf
                @method('put')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Current Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="current_password" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all">
                        @error('current_password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            New Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="password" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all">
                        @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Confirm Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="password_confirmation" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all">
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>