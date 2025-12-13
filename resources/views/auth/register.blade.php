<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Register</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .custom-bg-gradient {
            background: linear-gradient(135deg, #fce0e0, #e0e6f6);
        }
        .form-card {
            background-color: white;
            border-radius: 1rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .input-underline {
            border: none;
            border-bottom: 2px solid #D1D5DB;
            border-radius: 0;
            padding-left: 0;
            /* padding-right diatur inline untuk password */
            background: transparent;
        }
        .input-underline:focus {
            border-color: #3B82F6;
            box-shadow: none;
            ring: none;
        }
    </style>
</head>
<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex items-center justify-center p-4 custom-bg-gradient">

        <div class="w-full max-w-5xl form-card grid grid-cols-1 md:grid-cols-2 min-h-[500px]">

            {{-- Bagian Gambar (Kiri) --}}
            <div class="hidden md:block relative bg-gray-100">
                <img src="{{ asset('images/image1.png') }}" alt="Students" class="absolute inset-0 w-full h-full object-cover">
            </div>

            {{-- Bagian Form (Kanan) --}}
            <div class="p-8 md:p-10 w-full flex flex-col justify-center">

                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Create Account</h1>
                        <p class="text-sm text-gray-500 mt-1">Register to access AIMS dashboard</p>
                    </div>
                    <div class="flex gap-2">
                        <img src="{{ asset('images/aims.png') }}" alt="AIMS" class="h-8">
                        <img src="{{ asset('images/iec.png') }}" alt="IEC" class="h-8">
                    </div>
                </div>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    {{-- Baris 1: Username & Name --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 mb-4">
                        <div>
                            <label for="username" class="block text-xs font-bold text-gray-500 uppercase tracking-wide">Username</label>
                            <input id="username" type="text" name="username" :value="old('username')" required autofocus autocomplete="username"
                                class="block mt-1 w-full input-underline focus:ring-0 sm:text-sm py-2" placeholder="">
                            <x-input-error :messages="$errors->get('username')" class="mt-1" />
                        </div>

                        <div>
                            <label for="name" class="block text-xs font-bold text-gray-500 uppercase tracking-wide">Full Name</label>
                            <input id="name" type="text" name="name" :value="old('name')" required autocomplete="name"
                                class="block mt-1 w-full input-underline focus:ring-0 sm:text-sm py-2" placeholder="">
                            <x-input-error :messages="$errors->get('name')" class="mt-1" />
                        </div>
                    </div>

                    {{-- Baris 2: Email --}}
                    <div class="mb-4">
                        <label for="email" class="block text-xs font-bold text-gray-500 uppercase tracking-wide">Email</label>
                        <input id="email" type="email" name="email" :value="old('email')" required autocomplete="username"
                            class="block mt-1 w-full input-underline focus:ring-0 sm:text-sm py-2" placeholder="">
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>

                    {{-- Baris 3: Passwords dengan Toggle Show/Hide --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 mb-6">
                        
                        {{-- Password Field --}}
                        <div x-data="{ show: false }" class="relative">
                            <label for="password" class="block text-xs font-bold text-gray-500 uppercase tracking-wide">Password</label>
                            <div class="relative">
                                <input id="password" :type="show ? 'text' : 'password'" name="password" required autocomplete="new-password"
                                    class="block mt-1 w-full input-underline focus:ring-0 sm:text-sm py-2 pr-10"> {{-- pr-10 agar teks tidak ketutup icon --}}
                                
                                <button type="button" @click="show = !show" 
                                    class="absolute inset-y-0 right-0 flex items-center pr-2 text-gray-400 hover:text-blue-600 cursor-pointer focus:outline-none">
                                    {{-- Icon Eye (Show) --}}
                                    <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    {{-- Icon Eye Slash (Hide) --}}
                                    <svg x-show="show" style="display: none;" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.05 10.05 0 011.574-2.59M5.38 5.38a10.056 10.056 0 016.62-2.38c4.478 0 8.268 2.943 9.542 7a10.05 10.05 0 01-2.033 3.51M15 12a3 3 0 00-3-3m-1.5 7.5a3 3 0 01-3-3 3 3 0 013-3m6.75-4.5L5.25 19.5" />
                                    </svg>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-1" />
                        </div>

                        {{-- Confirm Password Field --}}
                        <div x-data="{ show: false }" class="relative">
                            <label for="password_confirmation" class="block text-xs font-bold text-gray-500 uppercase tracking-wide">Confirm</label>
                            <div class="relative">
                                <input id="password_confirmation" :type="show ? 'text' : 'password'" name="password_confirmation" required autocomplete="new-password"
                                    class="block mt-1 w-full input-underline focus:ring-0 sm:text-sm py-2 pr-10">
                                
                                <button type="button" @click="show = !show" 
                                    class="absolute inset-y-0 right-0 flex items-center pr-2 text-gray-400 hover:text-blue-600 cursor-pointer focus:outline-none">
                                    <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg x-show="show" style="display: none;" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.05 10.05 0 011.574-2.59M5.38 5.38a10.056 10.056 0 016.62-2.38c4.478 0 8.268 2.943 9.542 7a10.05 10.05 0 01-2.033 3.51M15 12a3 3 0 00-3-3m-1.5 7.5a3 3 0 01-3-3 3 3 0 013-3m6.75-4.5L5.25 19.5" />
                                    </svg>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                        </div>
                    </div>

                    {{-- Terms --}}
                    <div class="flex items-center mb-6 text-sm">
                        <input id="terms" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" name="terms" required>
                        <label for="terms" class="ms-2 text-gray-600 text-xs">I agree to the <a href="#" class="text-blue-600 hover:underline">Privacy Policy</a> & <a href="#" class="text-blue-600 hover:underline">Terms</a></label>
                    </div>

                    {{-- Actions --}}
                    <div>
                        <button type="submit" class="w-full bg-blue-600 text-white py-2.5 px-4 rounded-lg font-semibold hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-300 text-sm">
                            {{ __('Register') }}
                        </button>
                    </div>

                    <p class="text-center text-xs text-gray-600 mt-5">
                        Already have an account?
                        <a href="{{ route('login') }}" class="font-bold text-blue-600 hover:underline">
                            Login here
                        </a>
                    </p>

                </form>
            </div>
        </div>

    </div>
</body>
</html>