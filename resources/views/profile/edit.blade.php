<x-app-layout>
    {{-- Header tidak ditampilkan sesuai layout dashboard Anda --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight hidden">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Breadcrumb --}}
            <div class="mb-6">
                <div class="text-sm text-gray-500">
                    <span class="text-gray-400">></span> Profile
                </div>
            </div>

            <div class="space-y-6">
                
                {{-- Kartu 1: Update Profile Information --}}
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-xl border border-gray-100">
                    <div class="max-w-xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                {{-- Kartu 2: Update Password --}}
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-xl border border-gray-100">
                    <div class="max-w-xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                {{-- Kartu 3: Delete User --}}
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-xl border border-gray-100">
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>