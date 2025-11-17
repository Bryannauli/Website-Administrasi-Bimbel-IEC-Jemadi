{{-- resources/views/layouts/app.blade.php --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin IEC Jemadi')</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        /* Menggunakan warna background body dari desain Figma */
        body { background-color: #F8F9FA; }
    </style>
</head>
<body class="text-gray-800 font-sans"> {{-- Tambah font-sans untuk font default yang bersih --}}

    <div class="flex">
        {{-- Sidebar --}}
        <aside class="w-[280px] h-screen fixed top-0 left-0 bg-[#4F35A1] text-white flex flex-col shadow-xl"> {{-- Lebar dan warna disesuaikan, tambah shadow --}}
            <div class="flex items-center justify-center p-6 h-[80px] border-b border-white/[0.08]"> {{-- Tinggi dan warna border disesuaikan --}}
                <img src="https://i.imgur.com/g82yBfv.png" alt="IEC Logo" class="h-10"> {{-- Pastikan logo pas --}}
            </div>
            
            <nav class="flex-1 p-6 space-y-2 overflow-y-auto"> {{-- Padding dan spacing disesuaikan --}}
                
                {{-- Home --}}
                <a href="#" class="flex items-center space-x-4 p-3 rounded-lg text-sm font-semibold transition-all text-[#C5B3E6] hover:bg-white/10"> {{-- Warna teks, hover, dan spacing ikon disesuaikan --}}
                    <i class="fa-solid fa-house fa-fw w-5 text-center text-[18px]"></i> {{-- Ukuran ikon disesuaikan --}}
                    <span>Home</span>
                </a>
                
                {{-- Dashboard (Aktif) --}}
                <a href="{{ url('/admin/dashboard') }}" 
                   class="flex items-center space-x-4 p-3 rounded-lg text-sm font-semibold transition-all bg-[#E9EBF9] text-[#4F35A1] shadow-sm"> {{-- Warna latar aktif dan teks disesuaikan --}}
                    <i class="fa-solid fa-chart-pie fa-fw w-5 text-center text-[18px]"></i>
                    <span>Dashboard</span>
                </a>
                
                {{-- Students --}}
                <a href="#" class="flex items-center space-x-4 p-3 rounded-lg text-sm font-semibold transition-all text-[#C5B3E6] hover:bg-white/10">
                    <i class="fa-solid fa-users fa-fw w-5 text-center text-[18px]"></i>
                    <span>Students</span>
                </a>
                
                {{-- Class Schedule --}}
                <a href="#" class="flex items-center space-x-4 p-3 rounded-lg text-sm font-semibold transition-all text-[#C5B3E6] hover:bg-white/10">
                    <i class="fa-solid fa-calendar-days fa-fw w-5 text-center text-[18px]"></i>
                    <span>Class Schedule</span>
                </a>

                {{-- Teachers --}}
                <a href="#" class="flex items-center space-x-4 p-3 rounded-lg text-sm font-semibold transition-all text-[#C5B3E6] hover:bg-white/10">
                    <i class="fa-solid fa-chalkboard-user fa-fw w-5 text-center text-[18px]"></i>
                    <span>Teachers</span>
                </a>

                {{-- Authentication --}}
                <a href="#" class="flex items-center space-x-4 p-3 rounded-lg text-sm font-semibold transition-all text-[#C5B3E6] hover:bg-white/10">
                    <i class="fa-solid fa-shield-halved fa-fw w-5 text-center text-[18px]"></i>
                    <span>Authentication</span>
                </a>
            </nav>
            
            {{-- Logout Button --}}
            <div class="p-6 border-t border-white/[0.08]"> {{-- Padding dan border disesuaikan --}}
                <a href="#" class="flex items-center justify-center space-x-3 p-3 rounded-lg bg-[#5377F9] hover:bg-[#4364e6] transition-all text-sm font-semibold"> {{-- Warna dan hover disesuaikan --}}
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Log Out</span>
                </a>
            </div>
        </aside>

        {{-- Main Content Area --}}
        <main class="flex-1 ml-[280px]"> {{-- Margin kiri disesuaikan dengan lebar sidebar --}}
        
            {{-- Header (Navbar Top) --}}
            <header class="bg-white h-[80px] flex justify-between items-center px-8 border-b border-gray-200 sticky top-0 z-10 shadow-sm"> {{-- Tinggi, padding, dan shadow disesuaikan --}}
                <div class="text-sm text-gray-500 font-medium"> {{-- Font size dan weight disesuaikan --}}
                    <span class="text-gray-900 font-bold">Home</span> / 
                    @yield('breadcrumb', 'Page') 
                </div>
                
                <div class="flex items-center space-x-6">
                    {{-- Notification Bell --}}
                    <button class="text-gray-500 hover:text-gray-800 relative">
                        <i class="fa-solid fa-bell fa-xl"></i> {{-- Ukuran ikon --}}
                        <span class="absolute top-0 right-0 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"></span>
                    </button>
                    
                    {{-- User Profile --}}
                    <a href="{{ url('/admin/profile') }}" class="flex items-center space-x-3 cursor-pointer">
                        <img src="https://i.imgur.com/E8Wb2yr.png" alt="Admin Photo" class="w-10 h-10 rounded-full border-2 border-pink-300"> {{-- Border warna disesuaikan --}}
                        <div class="text-sm">
                            <strong class="block text-gray-800 font-medium">Moni Roy</strong> {{-- Font weight disesuaikan --}}
                            <span class="text-gray-500">Admin</span>
                        </div>
                    </a>
                </div>
            </header>

            {{-- Page Content Slot --}}
            <div class="p-8"> {{-- Global padding untuk konten --}}
                @yield('content')
            </div>
            
            {{-- Footer --}}
            <footer class="text-center p-6 text-sm text-gray-500 mt-auto">
                Copyright ©2025 International Education Centre Jemadi. All rights reserved.
            </footer>
        </main>
    </div>

    @stack('scripts')
</body>
</html>