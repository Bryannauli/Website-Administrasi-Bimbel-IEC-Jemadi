<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Login</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex items-center justify-center p-4" style="background: linear-gradient(135deg, #fce0e0, #e0e6f6);">

        <div class="w-full max-w-4xl bg-white rounded-2xl shadow-2xl overflow-hidden grid grid-cols-1 md:grid-cols-2">

            <div class="hidden md:block">
                <img src="{{ asset('images/image1.png') }}" alt="Students in class" class="w-full h-full object-cover">
            </div>

            <div class="p-8 md:p-12 w-full">

                <div class="flex justify-end gap-2 mb-10">
                    <img src="{{ asset('images/aims.png') }}" alt="AIMS Logo" class="h-10">
                    <img src="{{ asset('images/iec.png') }}" alt="IEC Logo" class="h-10">
                </div>

                <h1 class="text-3xl font-bold text-gray-800">Hello, Welcome back</h1>
                <p class="text-gray-600 mt-2 mb-6">Login to access your AIMS account</p>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}">
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
                            class="block mt-1 w-full border-0 border-b-2 border-gray-300 focus:border-blue-600 focus:ring-0"
                        />
                        <x-input-error :messages="$errors->get('username')" class="mt-2" />
                    </div>

                    <div class="mb-5">
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            class="block mt-1 w-full border-0 border-b-2 border-gray-300 focus:border-blue-600 focus:ring-0"
                        />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-between mt-4 text-sm">
                        <label for="remember_me" class="inline-flex items-center">
                            <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" name="remember">
                            <span class="ms-2 text-gray-600">{{ __('Remember me') }}</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="underline text-blue-600 hover:text-blue-800" href="{{ route('password.request') }}">
                                {{ __('Forgot password?') }}
                            </a>
                        @endif
                    </div>

                    <div class="mt-8">
                        <button type="submit" class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-300">
                            {{ __('Login') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</body>
</html>