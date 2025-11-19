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
    <body class="font-sans antialiased">
        {{-- PERUBAHAN: Latar belakang utama diubah ke bg-blue-50 --}}
        <div class="min-h-screen flex bg-blue-50 ">
            
            @include('layouts.navigation')

            <div class="flex-1 flex flex-col ml-64 h-screen overflow-y-auto">
                
                <header class="sticky top-0 bg-white shadow-sm w-full z-10">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                        
                        {{-- Judul Halaman (dari slot header) --}}
                        @if (isset($header))
                            <div class="flex-1">
                                {{ $header }}
                            </div>
                        @else
                            {{-- Placeholder agar header tidak bergeser --}}
                            <div class="flex-1"></div>
                        @endif

                        <div class="flex items-center space-x-4">
                            
                            {{-- Tombol Notifikasi --}}
                            <button class="text-gray-500 hover:text-gray-700 relative">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341A6.002 6.002 0 006 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                {{-- Titik Notifikasi --}}
                                <span class="absolute top-0 right-0 flex h-3 w-3">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-pink-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-pink-500"></span>
                                </span>
                            </button>

                            <div class="hidden sm:flex sm:items-center">
                                <x-dropdown align="right" width="48">
                                    <x-slot name="trigger">
                                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                            {{-- Ganti dengan gambar profil jika ada, atau gunakan inisial --}}
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&color=E91E63&background=F8BBD0" alt="Avatar" class="h-8 w-8 rounded-full mr-2">
                                            <div class="text-left">
                                                <div class="font-medium">{{ Auth::user()->name }}</div>
                                                {{-- Ganti dengan Role jika Anda menyimpannya --}}
                                                <div class="text-xs text-gray-400">Admin</div> 
                                            </div>
                                            <div class="ms-1">
                                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </button>
                                    </x-slot>

                                    <x-slot name="content">
                                        <x-dropdown-link :href="route('profile.edit')">
                                            {{ __('Profile') }}
                                        </x-dropdown-link>

                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <x-dropdown-link :href="route('logout')"
                                                    onclick="event.preventDefault();
                                                                this.closest('form').submit();">
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
            </div>
        </div>
    </body>
</html>