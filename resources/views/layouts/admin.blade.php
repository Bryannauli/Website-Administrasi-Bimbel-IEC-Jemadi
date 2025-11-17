<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin IEC Jemadi')</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        body { background-color: #F8F9FA; }
    </style>
</head>
<body class="text-gray-800">

    <div class="flex">
        <aside class="w-72 h-screen fixed top-0 left-0 bg-[#4F35A1] text-white flex flex-col">
            <div class="flex items-center justify-center p-6 h-20 border-b border-white/10">
                <img src="https://i.imgur.com/g82yBfv.png" alt="IEC Logo" class="h-10">
            </div>
            
            <nav class="flex-1 p-6 space-y-3 overflow-y-auto">
               
                
                <a href="#" class="flex items-center space-x-3 p-3 rounded-lg text-sm font-medium transition-all text-white bg-white/20">
                    <i class="fa-solid fa-house fa-fw w-5 text-center"></i>
                    <span>Home</span>
                </a>
                
                <a href="{{ url('/admin/dashboard') }}" 
                   class="flex items-center space-x-3 p-3 rounded-lg text-sm font-medium transition-all bg-[#E9EBF9] text-gray-900 shadow-sm">
                    <i class="fa-solid fa-chart-pie fa-fw w-5 text-center"></i>
                    <span>Dashboard</span>
                </a>
                
                <a href="#" class="flex items-center space-x-3 p-3 rounded-lg text-sm font-medium transition-all text-gray-300 hover:bg-white/10 hover:text-white">
                    <i class="fa-solid fa-users fa-fw w-5 text-center"></i>
                    <span>Students</span>
                </a>
                
                <a href="#" class="flex items-center space-x-3 p-3 rounded-lg text-sm font-medium transition-all text-gray-300 hover:bg-white/10 hover:text-white">
                    <i class="fa-solid fa-calendar-days fa-fw w-5 text-center"></i>
                    <span>Class Schedule</span>
                </a>

                <a href="#" class="flex items-center space-x-3 p-3 rounded-lg text-sm font-medium transition-all text-gray-300 hover:bg-white/10 hover:text-white">
                    <i class="fa-solid fa-chalkboard-user fa-fw w-5 text-center"></i>
                    <span>Teachers</span>
                </a>

                <a href="#" class="flex items-center space-x-3 p-3 rounded-lg text-sm font-medium transition-all text-gray-300 hover:bg-white/10 hover:text-white">
                    <i class="fa-solid fa-shield-halved fa-fw w-5 text-center"></i>
                    <span>Authentication</span>
                </a>
            </nav>
            
            <div class="p-6 border-t border-white/10">
                <a href="#" class="flex items-center justify-center space-x-3 p-3 rounded-lg bg-blue-600 hover:bg-blue-700 transition-all text-sm font-medium">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Log Out</span>
                </a>
            </div>
        </aside>

      
        <main class="flex-1 ml-72">
        
            <header class="bg-white h-20 flex justify-between items-center px-8 border-b border-gray-200 sticky top-0 z-10">
                <div class="text-sm text-gray-500">
                    <span class="text-gray-900 font-semibold">Home</span> / 
                    {{-- Ini akan diisi oleh halaman 'anak' --}}
                    @yield('breadcrumb', 'Page') 
                </div>
                
                <div class="flex items-center space-x-6">
                    <button class="text-gray-500 hover:text-gray-800 relative">
                        <i class="fa-solid fa-bell fa-lg"></i>
                        <span class="absolute top-0 right-0 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"></span>
                    </button>
                    
                    <a href="{{ url('/admin/profile') }}" class="flex items-center space-x-3 cursor-pointer">
                        <img src="https://i.imgur.com/E8Wb2yr.png" alt="Admin Photo" class="w-10 h-10 rounded-full border-2 border-pink-300">
                        <div class="text-sm">
                            <strong class="block text-gray-800">Moni Roy</strong>
                            <span class="text-gray-500">Admin</span>
                        </div>
                    </a>
                </div>
            </header>

            <div class="p-8">
                
                @yield('content')
                
            </div>
            
            <footer class="text-center p-6 text-sm text-gray-500 mt-auto">
                Copyright ©2025 International Education Centre Jemadi. All rights reserved.
            </footer>
        </main>
    </div>

    @stack('scripts')
</body>
</html>