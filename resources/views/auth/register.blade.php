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
        /* Custom styles for gradient background and shadow effects */
        .custom-bg-gradient {
            background: linear-gradient(135deg, #fce0e0, #e0e6f6); /* Matches login page gradient */
        }
        .form-card {
            background-color: white;
            border-radius: 1rem; /* 16px */
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1); /* shadow-2xl */
            overflow: hidden;
        }
        .input-underline {
            border: none;
            border-bottom: 2px solid #D1D5DB; /* gray-300 */
            border-radius: 0;
            padding-left: 0;
            padding-right: 0;
        }
        .input-underline:focus {
            border-color: #3B82F6; /* blue-600 */
            box-shadow: none;
            ring: none;
        }
    </style>
</head>
<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex items-center justify-center p-4 custom-bg-gradient">

        <div class="w-full max-w-4xl form-card grid grid-cols-1 md:grid-cols-2">

            <div class="hidden md:block">
                {{-- Pastikan path gambar ini benar --}}
                <img src="{{ asset('images/image1.png') }}" alt="Students in class" class="w-full h-full object-cover">
            </div>

            <div class="p-8 md:p-12 w-full">

                <div class="flex justify-end gap-2 mb-10">
                    {{-- Pastikan path gambar logo ini benar --}}
                    <img src="{{ asset('images/aims.png') }}" alt="AIMS Logo" class="h-10">
                    <img src="{{ asset('images/iec.png') }}" alt="IEC Logo" class="h-10">
                </div>

                <h1 class="text-3xl font-bold text-gray-800">Create Your Account</h1>
                <p class="text-gray-600 mt-2 mb-6">Register to get access to AIMS dashboard</p>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="mb-5">
                        <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                        <input
                            id="username"
                            type="text"
                            name="username"
                            :value="old('username')"
                            required
                            autofocus
                            autocomplete="username"
                            class="block mt-1 w-full input-underline focus:ring-0"
                        />
                        <x-input-error :messages="$errors->get('username')" class="mt-2" />
                    </div>

                    <div class="mb-5">
                        <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                        <input
                            id="name"
                            type="text"
                            name="name"
                            :value="old('name')"
                            required
                            autofocus
                            autocomplete="name"
                            class="block mt-1 w-full input-underline focus:ring-0"
                        />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="mb-5">
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            :value="old('email')"
                            required
                            autocomplete="username"
                            class="block mt-1 w-full input-underline focus:ring-0"
                        />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div class="mb-5">
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="new-password"
                            class="block mt-1 w-full input-underline focus:ring-0"
                        />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="mb-5">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            required
                            autocomplete="new-password"
                            class="block mt-1 w-full input-underline focus:ring-0"
                        />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <div class="flex items-center mt-6 text-sm">
                        <input id="terms" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" name="terms" required>
                        <label for="terms" class="ms-2 text-gray-600">I agree to the <a href="#" class="text-blue-600 hover:underline">Privacy Policy</a> & <a href="#" class="text-blue-600 hover:underline">Terms of Use</a></label>
                    </div>

                    <div class="mt-8">
                        <button type="submit" class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-300">
                            {{ __('Register') }}
                        </button>
                    </div>

                    <p class="text-center text-sm text-gray-600 mt-6">
                        Already have an account?
                        <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:underline">
                            Login
                        </a>
                    </p>

                </form>
            </div>
        </div>

    </div>
</body>
</html>