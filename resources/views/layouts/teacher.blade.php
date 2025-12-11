<!-- resources/views/layouts/teacher.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'AIMS - Teacher Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .gradient-pink-purple {
            background: linear-gradient(180deg, #FDE1F5 0%, #E9D5FF 100%);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-purple-50">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 gradient-pink-purple shadow-lg fixed h-full overflow-y-auto">
            <!-- Logo -->
            <div class="p-6">
                <div class="flex items-center space-x-3">
                    <img src="/images/aims.png" alt="AIMS" class="h-10">
                    <img src="/images/IEC.png" alt="IEC" class="h-10">
                </div>
            </div>

            <!-- Navigation -->
            <nav class="px-4 space-y-1">
                <!-- Home Dropdown -->
                <div x-data="{ open: true }">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-gray-700 hover:bg-white/50 rounded-lg transition">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-home"></i>
                            <span>Home</span>
                        </div>
                        <i class="fas fa-chevron-down text-xs" :class="{ 'rotate-180': open }"></i>
                    </button>
                    <div x-show="open" class="ml-4 mt-1 space-y-1">
                        <a href="{{ route('teacher.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-white/50 rounded-lg {{ request()->routeIs('teacher.dashboard') ? 'bg-white text-blue-600' : '' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('teacher.analytics') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-white/50 rounded-lg {{ request()->routeIs('teacher.analytics') ? 'bg-white text-blue-600' : '' }}">
                            Analytics
                        </a>
                    </div>
                </div>

                <!-- Students Dropdown -->
                <!-- <div x-data="{ open: false }">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-gray-700 hover:bg-white/50 rounded-lg transition">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-user-graduate"></i>
                            <span>Students</span>
                        </div>
                        <i class="fas fa-chevron-down text-xs" :class="{ 'rotate-180': open }"></i>
                    </button>
                    <div x-show="open" class="ml-4 mt-1 space-y-1">
                        <a href="{{ route('teacher.students.marks') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-white/50 rounded-lg">
                            Students Marks
                        </a>
                        <a href="{{ route('teacher.students.attendance') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-white/50 rounded-lg">
                            Attendance
                        </a>
                    </div>
                </div> -->

                <!-- Class Schedule -->
                <a href="{{ route('teacher.classes.index') }}" 
                class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg transition
                {{ request()->routeIs('teacher.classes.*') ? 'bg-white text-gray-700 shadow-sm' : 'text-gray-700 hover:bg-white/50' }}">
                    <i class="fas fa-calendar-alt w-5 text-center"></i> 
                    <span>Class Schedule</span>
                </a>

                <!-- Teachers Dropdown -->
                <!-- <div x-data="{ open: {{ request()->routeIs('teacher.classes.*') || request()->routeIs('teacher.teachers.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-gray-700 hover:bg-white/50 rounded-lg transition {{ request()->routeIs('teacher.classes.*') || request()->routeIs('teacher.teachers.*') ? 'bg-blue-100' : '' }}">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-chalkboard-teacher"></i>
                            <span>Teachers</span>
                        </div>
                        <i class="fas fa-chevron-down text-xs" :class="{ 'rotate-180': open }"></i>
                    </button>
                    <div x-show="open" class="ml-4 mt-1 space-y-1">
                        <a href="{{ route('teacher.classes.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-white/50 rounded-lg {{ request()->routeIs('teacher.classes.*') ? 'bg-white text-blue-600' : '' }}">
                            Classes
                        </a>
                        <a href="{{ route('teacher.teachers.attendance') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-white/50 rounded-lg {{ request()->routeIs('teacher.teachers.attendance') ? 'bg-white text-blue-600' : '' }}">
                            Attendance
                        </a>
                    </div>
                </div> -->

                <!-- Authentication -->
                <div x-data="{ open: false }">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-gray-700 hover:bg-white/50 rounded-lg transition">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-shield-alt"></i>
                            <span>Authentication</span>
                        </div>
                        <i class="fas fa-chevron-down text-xs" :class="{ 'rotate-180': open }"></i>
                    </button>
                </div>
            </nav>

            <!-- User Profile at Bottom -->
            <div class="absolute bottom-0 left-0 right-0 p-4">
                <div class="bg-blue-600 rounded-lg p-4 text-white">
                    <div class="flex items-center space-x-3">
                        <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name ?? 'Teacher' }}&background=8B5CF6&color=fff" class="w-10 h-10 rounded-full">
                        <div class="flex-1">
                            <p class="font-semibold text-sm">{{ Auth::user()->name ?? 'Raa' }}</p>
                            <p class="text-xs opacity-90">{{ Auth::user()->email ?? 'slaysgirl.gmail.com' }}</p>
                        </div>
                        <button class="text-white">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Logout Button -->
            <div class="px-4 pb-24">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-white/50 rounded-lg transition">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Log Out</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 ml-64">
            <!-- Header -->
            <header class="bg-white shadow-sm sticky top-0 z-10">
                <div class="px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <!-- Breadcrumb -->
                        @yield('breadcrumb')
                    </div>

                    <div class="flex items-center space-x-6">
                        <!-- Notification -->
                        <button class="relative">
                            <i class="fas fa-bell text-gray-600 text-xl"></i>
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">3</span>
                        </button>

                        <!-- User Profile -->
                        <div class="flex items-center space-x-3">
                            <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name ?? 'Teacher' }}&background=8B5CF6&color=fff" class="w-10 h-10 rounded-full">
                            <div>
                                <p class="font-semibold text-sm">{{ Auth::user()->name ?? 'Geonwoo' }}</p>
                                <p class="text-xs text-gray-500">Teacher</p>
                            </div>
                            <i class="fas fa-chevron-down text-xs text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="p-6">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @stack('scripts')
</body>
</html>