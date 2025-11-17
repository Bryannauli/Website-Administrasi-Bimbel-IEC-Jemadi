@php
// Definisikan kelas untuk link aktif dan tidak aktif
$activeClasses = 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200';
$inactiveClasses = 'text-gray-600 hover:text-gray-900 hover:bg-pink-100 dark:text-gray-400 dark:hover:text-gray-100 dark:hover:bg-gray-700';
$activeClassesIcon = 'text-blue-600 dark:text-blue-300';
$inactiveClassesIcon = 'text-gray-400 group-hover:text-gray-500 dark:text-gray-500 dark:group-hover:text-gray-300';

// Definisikan kelas untuk link sub-menu aktif
$subActiveClasses = 'bg-blue-600 text-white';
$subInactiveClasses = 'text-gray-700 hover:bg-pink-100 dark:text-gray-300 dark:hover:bg-gray-700';
@endphp

{{-- Sidebar Penuh --}}
{{-- PERUBAHAN DI SINI: Ditambahkan "fixed top-0 left-0 z-20" --}}
<div class="fixed top-0 left-0 z-20 flex flex-col w-64 h-screen bg-gradient-to-br from-red-100 to-blue-100 shadow-lg">
    
    <!-- Logo -->
    <div class="flex items-center justify-center h-20 shadow-sm">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
            <img src="{{ asset('images/aims.png') }}" alt="AIMS Logo" class="h-10 w-10">
            <img src="{{ asset('images/logo.png') }}" alt="IEC Logo" class="h-10">
        </a>
    </div>

    <!-- Menu Navigasi -->
    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
        
        <!-- Home (dengan Sub-menu) -->
        <div x-data="{ open: request()->routeIs('dashboard') || request()->routeIs('analytics') }">
            <button @click="open = !open" 
                    class="group flex items-center justify-between w-full px-3 py-2 text-sm font-medium rounded-md {{ $inactiveClasses }}">
                <span class="flex items-center">
                    <svg class="mr-3 flex-shrink-0 h-6 w-6 {{ $inactiveClassesIcon }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6-4v-4a1 1 0 011-1h2a1 1 0 011 1v4" />
                    </svg>
                    {{ __('Home') }}
                </span>
                <svg :class="{'rotate-180': open}" class="ml-auto h-5 w-5 transform transition-transform" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
            
            {{-- Sub-menu --}}
            <div x-show="open" x-transition class="mt-2 space-y-1">
                <a href="{{ route('dashboard') }}" 
                   class="group flex items-center px-4 py-2 text-sm font-medium rounded-md ml-4 {{ request()->routeIs('dashboard') ? $subActiveClasses : $subInactiveClasses }}">
                    {{ __('Dashboard') }}
                </a>
                <a href="#" {{-- Ganti # dengan route analytics Anda --}}
                   class="group flex items-center px-4 py-2 text-sm font-medium rounded-md ml-4 {{ request()->routeIs('analytics') ? $subActiveClasses : $subInactiveClasses }}">
                    {{ __('Analytics') }}
                </a>
            </div>
        </div>

        <!-- Students -->
        <x-nav-link href="#" {{-- Ganti # dengan route --}} :active="request()->routeIs('students.*')" 
                    class="group {{ request()->routeIs('students.*') ? $activeClasses : $inactiveClasses }}">
            <svg class="mr-3 flex-shrink-0 h-6 w-6 {{ request()->routeIs('students.*') ? $activeClassesIcon : $inactiveClassesIcon }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            {{ __('Students') }}
            <svg class="ml-auto h-5 w-5 transform transition-colors" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </x-nav-link>

        <!-- Class Schedule -->
        <x-nav-link href="#" {{-- Ganti # dengan route --}} :active="request()->routeIs('classes.*')" 
                    class="group {{ request()->routeIs('classes.*') ? $activeClasses : $inactiveClasses }}">
            <svg class="mr-3 flex-shrink-0 h-6 w-6 {{ request()->routeIs('classes.*') ? $activeClassesIcon : $inactiveClassesIcon }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            {{ __('Class Schedule') }}
            <svg class="ml-auto h-5 w-5 transform transition-colors" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </x-nav-link>

        <!-- Teachers -->
        <x-nav-link href="#" {{-- Ganti # dengan route --}} :active="request()->routeIs('teachers.*')" 
                    class="group {{ request()->routeIs('teachers.*') ? $activeClasses : $inactiveClasses }}">
            <svg class="mr-3 flex-shrink-0 h-6 w-6 {{ request()->routeIs('teachers.*') ? $activeClassesIcon : $inactiveClassesIcon }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            {{ __('Teachers') }}
            <svg class="ml-auto h-5 w-5 transform transition-colors" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </x-nav-link>

        <!-- Authentication -->
        <x-nav-link href="#" {{-- Ganti # dengan route --}} :active="request()->routeIs('auth.*')" 
                    class="group {{ request()->routeIs('auth.*') ? $activeClasses : $inactiveClasses }}">
            <svg class="mr-3 flex-shrink-0 h-6 w-6 {{ request()->routeIs('auth.*') ? $activeClassesIcon : $inactiveClassesIcon }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6.036 1.916A9.006 9.006 0 0112 3c5.523 0 10 4.477 10 10 0 5.523-4.477 10-10 10-3.72 0-6.957-2.03-8.571-5M12 15V9" />
            </svg>
            {{ __('Authentication') }}
            <svg class="ml-auto h-5 w-5 transform transition-colors" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </x-nav-link>

    </nav>

    <!-- Bagian Bawah Sidebar (User & Logout) -->
    <div class="mt-auto p-4 border-t border-gray-200 dark:border-gray-700">
        <!-- User Info -->
        <div class="flex items-center mb-4">
            {{-- Avatar & Nama --}}
            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&color=0D9488&background=CCFBF1" alt="Avatar" class="h-10 w-10 rounded-full mr-3">
            <div>
                <div class="font-semibold text-sm text-gray-800 dark:text-gray-100">{{ Auth::user()->name }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</div>
            </div>
            {{-- Tombol Titik Tiga --}}
            <button class="ml-auto text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                  <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                </svg>
            </button>
        </div>
        
        <!-- Logout Button -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <a href="{{ route('logout') }}"
                    onclick="event.preventDefault(); this.closest('form').submit();"
                    class="group flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-blue-700 bg-blue-100 rounded-lg hover:bg-blue-200 dark:bg-blue-900 dark:text-blue-200 dark:hover:bg-blue-800">
                <svg class="mr-3 h-5 w-5 text-blue-600 dark:text-blue-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                {{ __('Log Out') }}
            </a>
        </form>
    </div>
</div>