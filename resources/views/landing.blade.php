<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AIMS - IEC Jemadi</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased overflow-x-hidden">

    <!-- =================================== -->
    <!-- == BAGIAN ATAS (GRADIENT HERO) == -->
    <!-- =================================== -->
    <div class="relative bg-gradient-to-br pb-10 from-red-100 to-blue-100 overflow-hidden">
        
        {{-- Dekorasi blur --}}
        <div class="absolute top-1/4 left-1/2 -translate-x-1/2 w-72 h-72 bg-blue-200 rounded-full opacity-30 blur-3xl"></div>
        <div class="absolute bottom-10 right-10 w-64 h-64 bg-red-200 rounded-full opacity-30 blur-3xl"></div>
        <div class="absolute bottom-1/4 left-1/4 w-48 h-48 bg-purple-200 rounded-full opacity-30 blur-3xl"></div>

        {{-- Dekorasi titik-titik --}}
        <div class="absolute bottom-1/4 right-1/4 w-20 h-20 bg-[radial-gradient(#d1d5db_1px,transparent_1px)] [background-size:10px_10px] opacity-50"></div>
        <div class="absolute top-1/3 left-1/4 w-20 h-20 bg-[radial-gradient(#d1d5db_1px,transparent_1px)] [background-size:10px_10px] opacity-50"></div>


        {{-- Konten utama dengan lebar terbatas --}}
        <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            
         <header class="sticky top-3 z-50  ">
      <div class="w-full">
        <nav x-data="{ open: false }" class="flex justify-between items-center h-16">

            <!-- Logo -->
            <div class="flex items-center space-x-2">
                <img src="{{ asset('images/aims.png') }}" class="h-9 w-9">
                <img src="{{ asset('images/logo.png') }}" class="h-9">
            </div>

            <!-- MENU DESKTOP -->
            <div class="hidden md:flex items-center space-x-10">
                <a href="#" class="font-medium text-gray-700 hover:text-brand-blue">Home</a>
                <a href="#" class="font-medium text-gray-700 hover:text-brand-blue">About Us</a>
                <a href="#" class="font-medium text-gray-700 hover:text-brand-blue">Services</a>

                @auth
                    <a href="{{ url('/dashboard') }}"
                       class="bg-brand-blue text-white px-6 py-2 rounded-lg font-bold">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="bg-brand-blue text-white px-6 py-2 rounded-lg font-bold">
                        Login
                    </a>
                    <!-- <a href="{{ route('register') }}"
                       class="text-brand-blue font-bold">
                        Register
                    </a> -->
                @endauth
            </div>

            <!-- HAMBURGER -->
            <button @click="open = !open" class="md:hidden">
                <svg class="w-7 h-7 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <!-- MENU MOBILE -->
            <div x-show="open"
                 x-transition
                 @click.outside="open = false"
                 class="absolute top-16 left-0 w-full bg-gradient-to-br from-red-100 to-blue-100 shadow-lg md:hidden">
                <div class="flex flex-col p-6 space-y-4">
                    <a href="#" class="font-medium text-gray-700">Home</a>
                    <a href="#" class="font-medium text-gray-700">About Us</a>
                    <a href="#" class="font-medium text-gray-700">Services</a>

                    @auth
                        <a href="{{ url('/dashboard') }}"
                           class="bg-brand-blue text-white text-center py-2 rounded-lg font-bold">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="bg-brand-blue text-white text-center py-2 rounded-lg font-bold">
                            Login
                        </a>
                        <a href="{{ route('register') }}"
                           class="border border-brand-blue text-brand-blue text-center py-2 rounded-lg font-bold">
                            Register
                        </a>
                    @endauth
                </div>
            </div>

        </nav>
    </div>
</header>

            <main class="py-10 md:py-16">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-16 items-start">
                    
                    <div class="space-y-6 text-center md:text-left">
                        <span class="text-sm font-semibold text-gray-600 uppercase tracking-wider">AIMS</span>
                        <h1 class="text-3xl lg:text-4xl font-extrabold text-gray-900">
                            Smart Administration System
                        </h1>
                        <h2 class="text-2xl lg:text-3xl font-bold text-brand-blue">
                            IEC Jemadi
                        </h2>
                        <p class="text-base text-gray-700 max-w-md mx-auto md:mx-0">
                            Manage students, classes, and attendance efficiently with a secure and centralized web-based system.
                        </p>
                        <div>
                            <a href="#" class="inline-block bg-brand-blue hover:bg-opacity-90 text-white font-bold py-3 px-10 rounded-lg shadow-lg text-lg transition duration-300">
                                Get Started
                            </a>
                        </div>
                    </div>

                    <div class="relative space-y-4"> 
                        
                        <div class="absolute -top-4 -left-4 w-12 h-12 bg-brand-red rounded-full opacity-50"></div>
                        <div class="absolute top-1/2 -right-4 w-8 h-8 bg-brand-blue rounded-full opacity-50"></div>
                        
                        <div class="relative max-w-xs ml-auto"> 
                            <div class="absolute top-0 right-0 w-[90%] h-[90%] bg-brand-red rounded-3xl -translate-y-3 translate-x-3"></div>
                            
                            <div x-data="{ activeSlide: 1, slides: 3 }" class="relative bg-white p-2 rounded-3xl shadow-xl w-full">
                                <div class="relative w-full h-48 overflow-hidden rounded-xl"> 
                                    <img src="{{ asset('images/image1.png') }}" alt="Class 1" 
                                         class="absolute w-full h-full object-cover transition-opacity duration-500" x-show="activeSlide === 1">
                                    <img src="{{ asset('images/image2.png') }}" alt="Class 2" 
                                         class="absolute w-full h-full object-cover transition-opacity duration-500" x-show="activeSlide === 2">
                                    <img src="{{ asset('images/image3.png') }}" alt="Class 3" 
                                         class="absolute w-full h-full object-cover transition-opacity duration-500" x-show="activeSlide === 3">
                                </div>
                                <div class="absolute inset-0 flex justify-between items-center px-4">
                                    <button @click="activeSlide = (activeSlide === 1) ? slides : activeSlide - 1" class="bg-white/50 hover:bg-white rounded-full p-1 text-gray-800 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                                    </button>
                                    <button @click="activeSlide = (activeSlide === slides) ? 1 : activeSlide + 1" class="bg-white/50 hover:bg-white rounded-full p-1 text-gray-800 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="relative max-w-xs mr-auto"> 
                            <div class="absolute bottom-0 left-0 w-[90%] h-[90%] bg-brand-blue rounded-3xl translate-y-3 -translate-x-3"></div>

                            <div x-data="{ activeSlide: 1, slides: 3 }" class="relative bg-white p-2 rounded-3xl shadow-xl w-full">
                                <div class="relative w-full h-48 overflow-hidden rounded-xl"> 
                                    <img src="{{ asset('images/image4.png') }}" alt="Teacher 1" 
                                         class="absolute w-full h-full object-cover transition-opacity duration-500" x-show="activeSlide === 1">
                                    <img src="{{ asset('images/image5.png') }}" alt="Teacher 2" 
                                         class="absolute w-full h-full object-cover transition-opacity duration-500" x-show="activeSlide === 2">
                                    <img src="{{ asset('images/image2.png') }}" alt="Teacher 3" 
                                         class="absolute w-full h-full object-cover transition-opacity duration-500" x-show="activeSlide === 3">
                                </div>
                                <div class="absolute inset-0 flex justify-between items-center px-4">
                                    <button @click="activeSlide = (activeSlide === 1) ? slides : activeSlide - 1" class="bg-white/50 hover:bg-white rounded-full p-1 text-gray-800 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                                    </button>
                                    <button @click="activeSlide = (activeSlide === slides) ? 1 : activeSlide + 1" class="bg-white/50 hover:bg-white rounded-full p-1 text-gray-800 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </main>
        </div> 
        {{-- Akhir dari div max-w-6xl dan wrapper gradien --}}
    </div> 
    {{-- Akhir dari div bg-gradient-to-br --}}


    <!-- =================================== -->
    <!-- == BAGIAN TENGAH (KONTEN PUTIH) == -->
    <!-- =================================== -->
    <div class="bg-white">
        <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <section class="py-16 md:py-24">
                
                <!-- Grid 3 Kolom (Hands-On, etc.) -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    
                    <!-- Card 1 -->
                    <div class="relative h-64 rounded-2xl shadow-lg overflow-hidden group">
                        <!-- Gambar Latar -->
                        <img src="{{ asset('images/image2.png') }}" alt="Hands-On Learning" class="absolute w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                        <!-- Overlay Gelap -->
                        <div class="absolute inset-0 bg-black/20"></div>
                        <!-- Konten Teks -->
                        <div class="relative z-10 flex flex-col justify-end h-full p-6 text-white">
                            <h3 class="text-2xl font-bold mb-2">Hands-On Learning</h3>
                            <p class="text-sm text-gray-200">"Exploration and discovery through play-based activities."</p>
                        </div>
                    </div>
                    
                    <!-- Card 2 -->
                    <div class="relative h-64 rounded-2xl shadow-lg overflow-hidden group">
                        <!-- Gambar Latar -->
                        <img src="{{ asset('images/image3.png') }}" alt="Collaborative Classroom" class="absolute w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                        <!-- Overlay Gelap -->
                        <div class="absolute inset-0 bg-black/20"></div>
                        <!-- Konten Teks -->
                        <div class="relative z-10 flex flex-col justify-end h-full p-6 text-white">
                            <h3 class="text-2xl font-bold mb-2">Collaborative Classroom</h3>
                            <p class="text-sm text-gray-200">"Encouraging teamwork, problem-solving, and communication."</p>
                        </div>
                    </div>
                    
                    <!-- Card 3 -->
                    <div class="relative h-64 rounded-2xl shadow-lg overflow-hidden group">
                        <!-- Gambar Latar -->
                        <img src="{{ asset('images/image5.png') }}" alt="Active & Healthy Kids" class="absolute w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                        <!-- Overlay Gelap -->
                        <div class="absolute inset-0 bg-black/20"></div>
                        <!-- Konten Teks -->
                        <div class="relative z-10 flex flex-col justify-end h-full p-6 text-white">
                            <h3 class="text-2xl font-bold mb-2">Active & Healthy Kids</h3>
                            <p class="text-sm text-gray-200">"Sports, movement, and fun for every learner!"</p>
                        </div>
                    </div>
                </div>

                <!-- Judul "Our Values" -->
                <div class="text-center mt-24 mb-16">
                    <h2 class="text-4xl font-extrabold text-brand-blue" style="color: #4a4a4a;">Our Values</h2>
                </div>

                <!-- Grid 4 Kolom (Values) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-x-8 gap-y-12">
                    
                    <!-- Value 1: Compassion -->
                    <div class="text-center">
                        <div class="flex justify-center mb-4">
                            <div class="flex items-center justify-center w-20 h-20 rounded-full bg-green-100">
                                {{-- Ikon SVG Daun (Compassion) --}}
                                <svg class="w-10 h-10 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-6.364-.386l1.591-1.591M3 12H.75m.386-6.364l1.591 1.591M9 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 15c-2.652 0-5.06.9-7.016 2.37A18.754 18.754 0 0012 21c2.75 0 5.356-.688 7.625-1.932A18.75 18.75 0 0015 15.011" />
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.75 0 5.356.688 7.625 1.932A18.75 18.75 0 0115 6.011M12 3a18.754 18.754 0 00-7.625 1.932A18.75 18.75 0 019 6.011" />
                                </svg>
                            </div>
                        </div>
                        <h4 class="text-xl font-bold text-gray-800 mb-2">Compassion</h4>
                        <p class="text-gray-600">We treat others with kindness, respect, and understanding.</p>
                    </div>
                    
                    <!-- Value 2: Growth -->
                    <div class="text-center">
                        <div class="flex justify-center mb-4">
                            <div class="flex items-center justify-center w-20 h-20 rounded-full bg-red-100">
                                {{-- Ikon SVG Target (Growth) --}}
                                <svg class="w-10 h-10 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.362-1.434 1.062 1.062 0 01.962.725 1.062 1.062 0 01-.725 1.286A7.47 7.47 0 0012 10.5a7.47 7.47 0 00-2.612-.443 1.062 1.062 0 01-.962-.725 1.062 1.062 0 01.725-1.286A8.983 8.983 0 0112 6.9a8.983 8.983 0 013.362 1.434 1.062 1.062 0 01.725 1.286 1.062 1.062 0 01-.962.725A7.47 7.47 0 0012 10.5a7.47 7.47 0 00-2.612-.443 1.062 1.062 0 01-.962-.725 1.062 1.062 0 01.725-1.286A8.983 8.983 0 0112 6.9c.43 0 .849.03 1.255.082A8.287 8.287 0 0015.362 5.214z" />
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.9A8.983 8.983 0 0115.362 5.214" />
                                </svg>
                            </div>
                        </div>
                        <h4 class="text-xl font-bold text-gray-800 mb-2">Growth</h4>
                        <p class="text-gray-600">We believe in growing every day — in knowledge, skills, and character.</p>
                    </div>

                    <!-- Value 3: Excellence -->
                    <div class="text-center">
                        <div class="flex justify-center mb-4">
                            <div class="flex items-center justify-center w-20 h-20 rounded-full bg-yellow-100">
                                {{-- Ikon SVG Tangan (Excellence) --}}
                                <svg class="w-10 h-10 text-yellow-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M15.042 21.672L13.684 16.6m0 0l-2.5-2.5m2.5 2.5l-2.5 2.5M13.684 16.6l-2.5-2.5m2.5 2.5l2.5 2.5m0-12.5l-2.5 2.5m2.5-2.5l2.5-2.5M3.375 7.5c0-3.142 3.58-5.625 8.125-5.625 4.545 0 8.125 2.483 8.125 5.625 0 3.142-3.58 5.625-8.125 5.625S3.375 10.642 3.375 7.5z" />
                                </svg>
                            </div>
                        </div>
                        <h4 class="text-xl font-bold text-gray-800 mb-2">Excellence</h4>
                        <p class="text-gray-600">Students are encouraged to be curious, creative, and committed to learning.</p>
                    </div>

                    <!-- Value 4: Community -->
                    <div class="text-center">
                        <div class="flex justify-center mb-4">
                            <div class="flex items-center justify-center w-20 h-20 rounded-full bg-blue-100">
                                {{-- Ikon SVG Grup (Community) --}}
                                <svg class="w-10 h-10 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m-4.682 2.72a3 3 0 01-4.682-2.72m4.682 2.72a9.094 9.094 0 01-3.741-.479m0 0a3 3 0 01-4.682 2.72M12 18.72A3 3 0 017.318 16m4.682 2.72a3 3 0 004.682-2.72m-4.682 2.72A9.094 9.094 0 0012 21.056m-4.682-2.336a3 3 0 010-4.682M12 5.28c-1.872 0-3.6.45-5.04 1.236A3 3 0 0012 10.5m0-5.22a3 3 0 013.741 2.336M12 5.28a3 3 0 00-3.741 2.336m7.482 0a3 3 0 010 4.682M3.259 10.704a3 3 0 010-4.682m17.482 4.682a3 3 0 010-4.682M12 10.5a3 3 0 01-3-3m3 3a3 3 0 00-3-3m3 3a3 3 0 013 3m-3 3a3 3 0 003 3m0-3a3 3 0 01-3 3" />
                                </svg>
                            </div>
                        </div>
                        <h4 class="text-xl font-bold text-gray-800 mb-2">Community</h4>
                        <p class="text-gray-600">Bright Futures believes in strong connections between students, teachers, families, and the community.</p>
                    </div>

                </div>
            </section>
        </div> 
        {{-- Akhir dari div max-w-6xl dan wrapper putih --}}
    </div>


    <!-- =================================== -->
    <!-- == BAGIAN BAWAH (FOOTER BIRU MUDA) == -->
    <!-- =================================== -->
    <footer class="relative z-10 bg-blue-100 pt-16 pb-10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                
                <!-- Kolom 1: Logo & About -->
                <div class="md:col-span-2">
                    <div class="flex items-center space-x-2 mb-4">
                        <img src="{{ asset('images/aims.png') }}" alt="AIMS Logo" class="h-10 w-10">
                        <img src="{{ asset('images/logo.png') }}" alt="IEC Logo" class="h-10">
                    </div>
                    <p class="text-gray-600 max-w-md">
                        AIMS IEC Jemadi provides a smart administration system to support modern education, fostering compassion, growth, excellence, and community.
                    </p>
                </div>

                <!-- Kolom 2: Tautan Cepat -->
                <div>
                    <h5 class="font-bold text-gray-800 mb-4">About IEC</h5>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-600 hover:text-brand-blue">Services</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-brand-blue">Contact Us</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-brand-blue">Our Values</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-brand-blue">Register</a></li>
                    </ul>
                </div>

                <!-- Kolom 3: Sosial Media -->
                <div>
                    <h5 class="font-bold text-gray-800 mb-4">Follow Us</h5>
                    <div class="flex space-x-3">
                        {{-- Ikon Instagram --}}
                        <a href="#" class="w-8 h-8 flex items-center justify-center bg-white rounded-full text-blue-600 hover:bg-blue-200 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-instagram" viewBox="0 0 16 16">
  <path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.9 3.9 0 0 0-1.417.923A3.9 3.9 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.9 3.9 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.9 3.9 0 0 0-.923-1.417A3.9 3.9 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599s.453.546.598.92c.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.5 2.5 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.5 2.5 0 0 1-.92-.598 2.5 2.5 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233s.008-2.388.046-3.231c.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92s.546-.453.92-.598c.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92m-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217m0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334"/>
</svg>
                        </a>
                        {{-- Ikon Email --}}
                        <a href="#" class="w-8 h-8 flex items-center justify-center bg-white rounded-full text-blue-600 hover:bg-blue-200 transition-colors">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                            </svg>
                        </a>
                        {{-- Ikon Telepon --}}
                        <a href="#" class="w-8 h-8 flex items-center justify-center bg-white rounded-full text-blue-600 hover:bg-blue-200 transition-colors">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-2.836-.184-5.253-2.5-5.438-5.334l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Garis Pemisah & Copyright -->
            <div class="mt-12 border-t border-blue-200 pt-8 text-center">
                <p class="text-gray-600 text-sm">
                    Copyright &copy; 2025 International Education Centre. All rights reserved.
                </p>
            </div>
        </div>
    </footer>
    
</body>
</html>