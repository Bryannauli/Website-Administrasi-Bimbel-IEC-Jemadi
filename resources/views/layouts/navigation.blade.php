@php
    // Definisi Class Styling
    $inactiveClasses = 'text-gray-700 hover:bg-blue-100 hover:text-blue-700';
    $activeClasses = 'bg-blue-200 text-blue-700';

    $inactiveClassesIcon = 'text-gray-700 group-hover:text-blue-700';
    $activeClassesIcon = 'text-blue-700';

    $subActiveClasses = 'bg-blue-100 text-blue-700';
    $subInactiveClasses = 'text-gray-700 hover:bg-blue-100 hover:text-blue-700';
    
    // Cek Role Helper
    $isAdmin = Auth::user()->role === 'admin';
    $isTeacher = Auth::user()->is_teacher;
@endphp

<div :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
     class="fixed inset-y-0 left-0 z-30 flex flex-col w-64 h-screen bg-gradient-to-br from-red-100 to-blue-100 shadow-lg transform transition-transform duration-300 ease-in-out">

    {{-- Tombol Close (Mobile) --}}
    <div class="lg:hidden flex justify-end px-4 pt-4">
        <button @click="sidebarOpen = false" class="text-gray-600 hover:text-gray-900">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    {{-- Logo / Judul --}}
    <div class="flex items-center justify-center h-16 bg-white/50 border-b border-white/50 px-4">
        <span class="text-xl font-extrabold text-blue-700 uppercase tracking-wider">
            AIMS <span class="text-gray-800">IEC</span>
        </span>
    </div>

    {{-- Navigasi Utama --}}
    <nav class="flex-1 overflow-y-auto p-4 space-y-2">

        {{-- ================================================= --}}
        {{-- DASHBOARD (Semua Role)                            --}}
        {{-- ================================================= --}}
        <a href="{{ route('dashboard') }}"
           class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-150 ease-in-out
           {{ request()->routeIs('dashboard') || request()->routeIs('admin.dashboard') || request()->routeIs('teacher.dashboard') ? $activeClasses : $inactiveClasses }}">
            
            <svg class="mr-3 h-6 w-6 flex-shrink-0
                {{ request()->routeIs('dashboard') || request()->routeIs('admin.dashboard') || request()->routeIs('teacher.dashboard') ? $activeClassesIcon : $inactiveClassesIcon }}"
                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            Dashboard
        </a>


        {{-- ================================================= --}}
        {{-- ADMIN ACCESS                                      --}}
        {{-- ================================================= --}}
        @if($isAdmin)
            
            <h3 class="mt-4 pt-2 text-xs font-semibold uppercase text-gray-500 tracking-wider border-t border-gray-200">Management</h3>

            {{-- 1. CLASSES --}}
            <a href="{{ route('admin.classes.index') }}"
               class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-150 ease-in-out
               {{ request()->routeIs('admin.classes.*') ? $activeClasses : $inactiveClasses }}">
                
                <svg class="mr-3 h-6 w-6 flex-shrink-0
                    {{ request()->routeIs('admin.classes.*') ? $activeClassesIcon : $inactiveClassesIcon }}"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                Classes
            </a>

            {{-- 2. STUDENTS --}}
            <a href="{{ route('admin.student.index') }}"
               class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-150 ease-in-out
               {{ request()->routeIs('admin.student.*') ? $activeClasses : $inactiveClasses }}">
                
                <svg class="mr-3 h-6 w-6 flex-shrink-0
                    {{ request()->routeIs('admin.student.*') ? $activeClassesIcon : $inactiveClassesIcon }}"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20v-2a3 3 0 015.356-1.857M7 20h4m-4 0a1 1 0 01-1-1v-2.75a2 2 0 012-2h4a2 2 0 012 2v2.75a1 1 0 01-1 1H7zm11.5-9.409v2.218a3 3 0 010 6.182M10 16a2 2 0 100-4 2 2 0 000 4z" />
                </svg>
                Students
            </a>

            {{-- 3. TEACHERS --}}
            <a href="{{ route('admin.teacher.index') }}"
               class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-150 ease-in-out
               {{ request()->routeIs('admin.teacher.*') ? $activeClasses : $inactiveClasses }}">
                
                <svg class="mr-3 h-6 w-6 flex-shrink-0
                    {{ request()->routeIs('admin.teacher.*') ? $activeClassesIcon : $inactiveClassesIcon }}"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Teachers
            </a>

            {{-- 4. ACTIVITY LOG --}}
            <a href="{{ route('admin.activity-log.index') }}"
               class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-150 ease-in-out
               {{ request()->routeIs('admin.activity-log.*') ? $activeClasses : $inactiveClasses }}">
                
                <svg class="mr-3 h-6 w-6 flex-shrink-0
                    {{ request()->routeIs('admin.activity-log.*') ? $activeClassesIcon : $inactiveClassesIcon }}"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Activity Log
            </a>

            {{-- 5. SYSTEM TRASH --}}
            <a href="{{ route('admin.trash.trash') }}"
                   class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-150 ease-in-out
                   {{ request()->routeIs('admin.trash.trash') ? 'bg-red-200 text-red-700' : 'text-gray-700 hover:bg-red-100 hover:text-red-700' }}">
                    
                    <svg class="mr-3 h-6 w-6 flex-shrink-0
                        {{ request()->routeIs('admin.trash.trash') ? 'text-red-700' : 'text-gray-700 group-hover:text-red-700' }}"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    System Trash
                </a>

        @endif {{-- End Admin Check --}}


        {{-- ================================================= --}}
        {{-- TEACHER ACCESS (Class Schedule)                   --}}
        {{-- ================================================= --}}
        
        @if($isTeacher)
            <a href="{{ route('teacher.classes.index') }}"
               class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-150 ease-in-out
               {{ request()->routeIs('teacher.classes.*') ? $activeClasses : $inactiveClasses }}">
                
                <svg class="mr-3 h-6 w-6 flex-shrink-0
                    {{ request()->routeIs('teacher.classes.*') ? $activeClassesIcon : $inactiveClassesIcon }}"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8..." />
                </svg>
                Classes
            </a>
            
            {{-- Tambahkan menu Teacher lainnya di sini --}}

        @endif {{-- End Teacher Check --}}

    </nav>

</div>