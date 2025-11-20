@php
$inactiveClasses = 'text-gray-700 hover:bg-blue-100 hover:text-blue-700';
$activeClasses = 'bg-blue-200 text-blue-700';

$inactiveClassesIcon = 'text-gray-700 group-hover:text-blue-700';
$activeClassesIcon = 'text-blue-700';


$subActiveClasses = 'bg-blue-600 text-blue-700';
$subInactiveClasses = 'text-gray-700 hover:bg-blue-100 hover:text-blue-700';
@endphp

<div class="fixed top-20 left-0 z-20 flex flex-col w-64 h-screen bg-gradient-to-br from-red-100 to-blue-100 shadow-lg">

    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto mt-5">

        {{-- DASHBOARD DROPDOWN --}}
        <div x-data="{ open: {{ request()->routeIs('dashboard') || request()->routeIs('analytics') ? 'true' : 'false' }} }">
            <button @click="open = !open"
                class="group flex items-center justify-between w-full px-3 py-2 text-sm font-medium rounded-md {{ $inactiveClasses }}">

                <span class="flex items-center">
                    <svg class="mr-3 h-6 w-6 {{ $inactiveClassesIcon }}" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6-4v-4a1 1 0 011-1h2a1 1 0 011 1v4" />
                    </svg>
                    Dashboard
                </span>

                <svg :class="{'rotate-180': open}"
                    class="ml-auto h-5 w-5 transform transition-transform"
                    xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                        clip-rule="evenodd" />
                </svg>
            </button>

            {{-- SUBMENU --}}
            <div x-show="open" x-transition class="mt-2 space-y-1">
                <a href="{{ route('dashboard') }}"
                    class="flex items-center px-4 py-2 text-sm font-medium rounded-md ml-4
                          {{ request()->routeIs('dashboard') ? $subActiveClasses : $subInactiveClasses }}">
                    Dashboard
                </a>


            </div>
        </div>

        {{-- STUDENTS DROPDOWN --}}
        <div x-data="{ open: {{ request()->routeIs('students.*') ? 'true' : 'false' }} }">
            <button @click="open = !open"
                class="group flex items-center justify-between w-full px-3 py-2 text-sm font-medium rounded-md !no-underline
               {{ request()->routeIs('students.*') ? $activeClasses : $inactiveClasses }}">

                <span class="flex items-center">
                    <svg class="mr-3  h-6 w-6 
                       {{ request()->routeIs('students.*') ? $activeClassesIcon : $inactiveClassesIcon }}"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Students
                </span>

                <svg :class="{'rotate-180': open}"
                    class="h-5 w-5 transition-transform" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M5.23 7.21a1 1 0 011.41 0L10 10.56l3.36-3.35a1 1 0 111.41 1.41l-4.06 4a1 1 0 01-1.41 0l-4.06-4a1 1 0 010-1.41z" />
                </svg>
            </button>

            {{-- SUBMENU --}}
            <div x-show="open" x-transition class="mt-2 space-y-1">
                <a href="{{ route('admin.student.index') }}"
                    class="block px-4 py-2 ml-4 rounded-md text-sm font-medium
                  {{ request()->routeIs('students.index') ? $subActiveClasses : $subInactiveClasses }}">
                    All Students
                </a>
                <a href=""
                    class="block px-4 py-2 ml-4 rounded-md text-sm font-medium
                  {{ request()->routeIs('students.index') ? $subActiveClasses : $subInactiveClasses }}">
                    Assesment
                </a>

                <a href=""
                    class="block px-4 py-2 ml-4 rounded-md text-sm font-medium
                  {{ request()->routeIs('students.create') ? $subActiveClasses : $subInactiveClasses }}">
                    Attendance
                </a>
            </div>
        </div>


        {{-- CLASS DROPDOWN --}}
        <div x-data="{ open: {{ request()->routeIs('classes.*') ? 'true' : 'false' }} }">
            <button @click="open = !open"
                class="group flex items-center justify-between w-full px-3 py-2 text-sm font-medium rounded-md !no-underline
               {{ request()->routeIs('classes.*') ? $activeClasses : $inactiveClasses }}">

                <span class="flex items-center">
                    <svg class="mr-3  h-6 w-6 
                       {{ request()->routeIs('classes.*') ? $activeClassesIcon : $inactiveClassesIcon }}"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Classes
                </span>

                <svg :class="{'rotate-180': open}"
                    class="h-5 w-5 transition-transform" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M5.23 7.21a1 1 0 011.41 0L10 10.56l3.36-3.35a1 1 0 111.41 1.41l-4.06 4a1 1 0 01-1.41 0l-4.06-4a1 1 0 010-1.41z" />
                    </path>
                </svg>
            </button>

            <div x-show="open" x-transition class="mt-2 space-y-1">
                <a href="}"
                    class="block px-4 py-2 ml-4 rounded-md text-sm font-medium
                  {{ request()->routeIs('classes.index') ? $subActiveClasses : $subInactiveClasses }}">
                    Class Schedule
                </a>


            </div>
        </div>

        {{-- TEACHERS DROPDOWN --}}
        <div x-data="{ open: {{ request()->routeIs('teachers.*') ? 'true' : 'false' }} }">
            <button @click="open = !open"
                class="group flex items-center justify-between w-full px-3 py-2 text-sm font-medium rounded-md !no-underline
               {{ request()->routeIs('teachers.*') ? $activeClasses : $inactiveClasses }}">

                <span class="flex items-center">
                    <svg class="mr-3  h-6 w-6 
                       {{ request()->routeIs('teachers.*') ? $activeClassesIcon : $inactiveClassesIcon }}"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Teachers
                </span>

                <svg :class="{'rotate-180': open}"
                    class="h-5 w-5 transition-transform" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M5.23 7.21a1 1 0 011.41 0L10 10.56l3.36-3.35a1 1 0 111.41 1.41l-4.06 4a1 1 0 01-1.41 0l-4.06-4a1 1 0 010-1.41z" />
                </svg>
            </button>

            <div x-show="open" x-transition class="mt-2 space-y-1">
                <a href=""
                    class="block px-4 py-2 ml-4 rounded-md text-sm font-medium
                  {{ request()->routeIs('teachers.index') ? $subActiveClasses : $subInactiveClasses }}">
                    Attendance
                </a>


            </div>
        </div>




    </nav>

</div>