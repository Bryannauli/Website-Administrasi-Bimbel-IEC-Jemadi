<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AIMS - IEC Jemadi</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased overflow-x-hidden">

    <div class="relative min-h-screen bg-gradient-to-br from-red-100 to-blue-100">
        
        <div class="absolute top-1/4 left-1/2 -translate-x-1/2 w-72 h-72 bg-blue-200 rounded-full opacity-30 blur-3xl"></div>
        <div class="absolute bottom-10 right-10 w-64 h-64 bg-red-200 rounded-full opacity-30 blur-3xl"></div>
        <div class="absolute bottom-1/4 left-1/4 w-48 h-48 bg-purple-200 rounded-full opacity-30 blur-3xl"></div>

        <div class="absolute bottom-1/4 right-1/4 w-20 h-20 bg-[radial-gradient(#d1d5db_1px,transparent_1px)] [background-size:10px_10px] opacity-50"></div>
        <div class="absolute top-1/3 left-1/4 w-20 h-20 bg-[radial-gradient(#d1d5db_1px,transparent_1px)] [background-size:10px_10px] opacity-50"></div>


        <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            
    <header class="py-6">
        <nav class="flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <img src="{{ asset('images/aims.png') }}" alt="AIMS Logo" class="h-10 w-10">
                <img src="{{ asset('images/logo.png') }}" alt="IEC Logo" class="h-10">
            </div>

            <div class="hidden md:flex items-center space-x-10">
                <div class="flex items-center space-x-10">
                    <a href="#" class="text-gray-700 hover:text-brand-blue font-medium">Home</a>
                    <a href="#" class="text-gray-700 hover:text-brand-blue font-medium">About Us</a>
                    <a href="#" class="text-gray-700 hover:text-brand-blue font-medium">Services</a>
                </div>

                <div class="flex items-center space-x-6"> 
                    @if (Route::has('login')) @auth 
                        <a href="{{ url('/dashboard') }}" class="bg-brand-blue hover:bg-opacity-90 text-white font-bold py-2 px-8 rounded-lg shadow-md transition duration-300"> Dashboard </a>
                    @else 
                        <a href="{{ route('login') }}" class="bg-brand-blue hover:bg-opacity-90 text-white font-bold py-2 px-8 rounded-lg shadow-md transition duration-300"> Login </a>
                
                        @if (Route::has('register')) <a href="{{ route('register') }}" class="text-gray-700 hover:text-brand-blue font-medium"> Register </a>
                        @endif @endauth @endif 
                </div>
            </div>
        </nav>
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
    </div>

</body>
</html>