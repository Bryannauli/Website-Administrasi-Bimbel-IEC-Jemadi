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

    {{-- Spacer --}}
    <div class="h-10 lg:h-20"></div> 

    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">

        {{-- 1. DASHBOARD --}}
        <a href="{{ $isAdmin ? route('dashboard') : route('teacher.dashboard') }}"
           class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-150 ease-in-out
           {{ request()->routeIs('dashboard') || request()->routeIs('teacher.dashboard') ? $activeClasses : $inactiveClasses }}">
            
            <svg class="mr-3 h-6 w-6 flex-shrink-0 
                {{ request()->routeIs('dashboard') || request()->routeIs('teacher.dashboard') ? $activeClassesIcon : $inactiveClassesIcon }}" 
                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6-4v-4a1 1 0 011-1h2a1 1 0 011 1v4" />
            </svg>
            Dashboard
        </a>

        {{-- ================================================= --}}
        {{-- ADMIN ONLY MENUS                                  --}}
        {{-- ================================================= --}}
        @if($isAdmin)

            {{-- 2. ACADEMICS (DROPDOWN) --}}
            {{-- Logika Active: Nyala jika buka Classes, Assessment, ATAU Daily Recap --}}
            @php
                $isAcademicsActive = request()->routeIs('admin.classes.*') || 
                                     request()->routeIs('admin.assessment.*') || 
                                     request()->routeIs('admin.classes.daily-recap');
            @endphp

            <div x-data="{ open: {{ $isAcademicsActive ? 'true' : 'false' }} }">
                <button @click="open = !open"
                    class="group flex items-center justify-between w-full px-3 py-2 text-sm font-medium rounded-md !no-underline
                   {{ $isAcademicsActive ? $activeClasses : $inactiveClasses }}">

                    <span class="flex items-center">
                        <svg class="mr-3 h-6 w-6 flex-shrink-0
                           {{ $isAcademicsActive ? $activeClassesIcon : $inactiveClassesIcon }}"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Academics
                    </span>

                    <svg :class="{'rotate-180': open}"
                        class="h-5 w-5 transition-transform transform duration-200" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M5.23 7.21a1 1 0 011.41 0L10 10.56l3.36-3.35a1 1 0 111.41 1.41l-4.06 4a1 1 0 01-1.41 0l-4.06-4a1 1 0 010-1.41z" />
                    </svg>
                </button>

                <div x-show="open" x-transition class="mt-2 space-y-1">
                    {{-- 2.a Daily Class Monitor (Pindah ke sini) --}}
                    <a href="{{ route('admin.classes.daily-recap') }}"
                        class="block px-4 py-2 ml-4 rounded-md text-sm font-medium transition-colors duration-150
                        {{ request()->routeIs('admin.classes.daily-recap') ? $subActiveClasses : $subInactiveClasses }}">
                        Daily Class Monitor
                    </a>

                    <a href="{{ route('admin.classes.index') }}"
                        class="block px-4 py-2 ml-4 rounded-md text-sm font-medium transition-colors duration-150
                      {{ request()->routeIs('admin.classes.index') ? $subActiveClasses : $subInactiveClasses }}">
                        Classes Management
                    </a>
                    
                    <a href="{{ route('admin.assessment.index') }}"
                        class="block px-4 py-2 ml-4 rounded-md text-sm font-medium transition-colors duration-150
                      {{ request()->routeIs('admin.assessment.index') ? $subActiveClasses : $subInactiveClasses }}">
                        Assessments
                    </a>
                </div>
            </div>

            {{-- 3. STUDENTS (Single Link) --}}
            <a href="{{ route('admin.student.index') }}"
               class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-150 ease-in-out
               {{ request()->routeIs('admin.student.*') ? $activeClasses : $inactiveClasses }}">
                
                <svg class="mr-3 h-6 w-6 flex-shrink-0
                    {{ request()->routeIs('admin.student.*') ? $activeClassesIcon : $inactiveClassesIcon }}"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                Students
            </a>

            {{-- 4. TEACHERS (Sekarang Single Link, bukan Dropdown) --}}
            {{-- Logika Active: Nyala jika route teacher.* TAPI BUKAN daily-recap --}}
            @php
                $isTeachersActive = request()->routeIs('admin.teacher.*') && !request()->routeIs('admin.classes.daily-recap');
            @endphp
            
            <a href="{{ route('admin.teacher.index') }}"
               class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-150 ease-in-out
               {{ $isTeachersActive ? $activeClasses : $inactiveClasses }}">
                
                <svg class="mr-3 h-6 w-6 flex-shrink-0
                    {{ $isTeachersActive ? $activeClassesIcon : $inactiveClassesIcon }}"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Teachers
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
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Class Schedule
            </a>
        @endif

    </nav>
</div>