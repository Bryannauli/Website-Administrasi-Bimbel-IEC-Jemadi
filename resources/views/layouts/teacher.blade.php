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
    <div class="flex min-h-screen" x-data="{ sidebarOpen: true }">        
    <!-- Sidebar -->
    <aside x-show="sidebarOpen" 
       x-transition:enter="transition ease-out duration-300"
       x-transition:enter-start="-translate-x-full"
       x-transition:enter-end="translate-x-0"
       x-transition:leave="transition ease-in duration-300"
       x-transition:leave-start="translate-x-0"
       x-transition:leave-end="-translate-x-full"
       class="w-64 bg-gradient-to-br from-red-100 to-blue-100 shadow-lg fixed h-full overflow-y-auto z-20">

        <button @click="sidebarOpen = false" class="absolute top-4 right-4 text-gray-500 hover:text-red-500 transition focus:outline-none z-50">
            <i class="fas fa-times text-lg"></i>
        </button> 

        <!-- Logo -->
            <div class="p-6">
                <div class="flex items-center space-x-3">
                    <img src="/images/aims.png" alt="AIMS" class="h-10">
                    <img src="/images/IEC.png" alt="IEC" class="h-10">
                </div>
            </div>

        <!-- Navigation -->
            <nav class="px-4 space-y-1">
                
                <!-- Dashboard -->
                <a href="{{ route('teacher.dashboard') }}" 
                   class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg transition
                   {{ request()->routeIs('teacher.dashboard') ? 'bg-white text-gray-700 shadow-sm' : 'text-gray-700 hover:bg-white/50' }}">
                    <i class="fas fa-home w-5 text-center"></i> 
                    <span>Dashboard</span>
                </a>

                <!-- Class Schedule -->
                <a href="{{ route('teacher.classes.index') }}" 
                class="w-full flex items-center space-x-3 px-4 py-3 rounded-lg transition
                {{ request()->routeIs('teacher.classes.*') ? 'bg-white text-gray-700 shadow-sm' : 'text-gray-700 hover:bg-white/50' }}">
                    <i class="fas fa-calendar-alt w-5 text-center"></i> 
                    <span>Class Schedule</span>
                </a>
            </nav>
    </aside>

        <!-- Main Content -->   
            <main class="flex-1 transition-all duration-300" :class="sidebarOpen ? 'ml-64' : 'ml-0'">
                <header class="bg-white shadow-sm sticky top-0 z-30">
                <div class="px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <button x-show="!sidebarOpen" @click="sidebarOpen = true" class="text-gray-500 hover:text-blue-600 focus:outline-none transition mr-2">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        @yield('breadcrumb')
                    </div>

                    <div class="flex items-center space-x-6">
                        <button class="relative">
                            <i class="fas fa-bell text-gray-600 text-xl"></i>
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">3</span>
                        </button>

                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" @click.outside="open = false" class="flex items-center space-x-3 focus:outline-none hover:bg-gray-50 p-2 rounded-lg transition">
                                <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name ?? 'Teacher' }}&background=8B5CF6&color=fff" class="w-10 h-10 rounded-full object-cover">
                                <div class="hidden md:block text-left">
                                    <p class="font-semibold text-sm text-gray-800">{{ Auth::user()->name ?? 'Geonwoo' }}</p>
                                    <p class="text-xs text-gray-500">{{ ucfirst(Auth::user()->role ?? 'Teacher') }}</p>
                                </div>
                                <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                            </button>

                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 ring-1 ring-black ring-opacity-5 focus:outline-none" 
                                 style="display: none;">
                                
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    {{ __('Profile') }}
                                </a>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <a href="{{ route('logout') }}" 
                                       onclick="event.preventDefault(); this.closest('form').submit();" 
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-red-600">
                                        {{ __('Log Out') }}
                                    </a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

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