<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'AIMS IEC') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">

    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
{{-- Tambahkan x-data di sini untuk kontrol sidebar global --}}

<body class="font-sans antialiased" x-data="{ sidebarOpen: false }">

    <div class="min-h-screen flex bg-blue-50">

        {{-- 1. Mobile Backdrop (Layar gelap saat menu buka di HP) --}}
        <div x-show="sidebarOpen"
            @click="sidebarOpen = false"
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-900/50 z-20 lg:hidden">
        </div>

        @include('layouts.navigation')

        {{-- 2. Konten Utama: ml-64 hanya di layar besar (lg:ml-64) --}}
        <div class="flex-1 flex flex-col h-screen overflow-y-auto transition-all duration-300 lg:ml-64">

            <header class="sticky top-0 bg-white shadow-sm w-full z-30">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center">

                    <div class="flex items-center space-x-3">
                        {{-- 3. Tombol Hamburger (Hanya muncul di HP/lg:hidden) --}}
                        <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-gray-700 focus:outline-none lg:hidden mr-2">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>

                        <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                            <img src="{{ asset('images/aims.png') }}" alt="AIMS Logo" class="h-10">
                            <img src="{{ asset('images/logo.png') }}" alt="IEC Logo" class="h-10">
                        </a>

                        @if (isset($header))
                        <div class="text-lg font-semibold text-gray-800 ml-4 hidden md:block">
                            {{ $header }}
                        </div>
                        @endif
                    </div>

                    <div class="flex items-center space-x-4">
                        {{-- Tombol Notifikasi --}}
                        <button class="text-gray-500 hover:text-gray-700 relative">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341A6.002 6.002 0 006 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span class="absolute top-0 right-0 flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-pink-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-pink-500"></span>
                            </span>
                        </button>

                        <div class="flex items-center">

                            {{-- Avatar (Mobile & Desktop) --}}
                            <x-dropdown align="right" width="48" contentClasses="py-1 bg-white">
                                <x-slot name="trigger">
                                    <button class="flex items-center focus:outline-none">
                                        <img
                                            src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&color=E91E63&background=F8BBD0"
                                            alt="Avatar"
                                            class="h-8 w-8 rounded-full shadow cursor-pointer">
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    {{-- Nama + role tampil di dalam dropdown saja --}}
                                    <div class="px-4 py-2 border-b">
                                        <div class="font-semibold text-gray-800">{{ Auth::user()->name }}</div>
                                        <div class="text-xs text-gray-500">
                                            {{ ucfirst(Auth::user()->role) }}
                                        </div>
                                    </div>

                                    {{-- [UPDATED] HANYA PAKAI SATU ROUTE --}}
                                    <x-dropdown-link :href="route('profile.edit')"
                                        class="bg-white text-gray-800 hover:!bg-blue-50 hover:text-blue-600 dark:bg-white dark:text-gray-800 dark:hover:bg-blue-50 dark:hover:text-blue-600">
                                        {{ __('Profile') }}
                                    </x-dropdown-link>

                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <x-dropdown-link :href="route('logout')"
                                            onclick="event.preventDefault(); this.closest('form').submit();" 
                                            class="bg-white text-gray-800 hover:!bg-blue-50 hover:text-blue-600 dark:bg-white dark:text-gray-800 dark:hover:bg-blue-50 dark:hover:text-blue-600">
                                            {{ __('Log Out') }}
                                        </x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>

                        </div>

                    </div>
                </div>
            </header>

            <main>
                {{ $slot }}
            </main>

            <footer class="bg-white py-4 border-t mt-auto">
                <div class="max-w-7xl mx-auto px-4 flex justify-between items-center text-sm text-gray-500">
                    <span>Copyright ©2025 International Education Centre Jemadi</span>
                    <span>©2025 AIMS. All right reserved.</span>
                </div>
            </footer>

        </div>
    </div>
</body>

</html>