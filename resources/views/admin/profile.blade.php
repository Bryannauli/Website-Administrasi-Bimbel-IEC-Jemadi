@extends('layouts.admin')
@section('title', 'Admin Profile - Admin IEC')
@section('breadcrumb', 'Profile')
@section('content')

    <div class="bg-white rounded-xl shadow-lg p-8 max-w-4xl mx-auto">
        
        <h2 class="text-2xl font-semibold text-gray-900 mb-6">Personal Details</h2>
        <form action="#" method="POST">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                    <input type="text" id="first_name" name="first_name" value="Moni" 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                    <input type="text" id="last_name" name="last_name" value="Roy"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <input type="email" id="email" name="email" value="moniroy@gmail.com"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone *</label>
                    <input type="text" id="phone" name="phone" value="0897534679653"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>
            <div class="mb-6">
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username *</label>
                <input type="text" id="username" name="username" value="moniroy" readonly
                       class="w-full rounded-md border-gray-300 shadow-sm bg-gray-100 text-gray-500 cursor-not-allowed">
            </div>
            
            {{-- Tombol simpan (jika ada, atau dipisah) --}}
            {{-- 
            <div class="text-right">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-blue-700">
                    Save Details
                </button>
            </div> 
            --}}
        </form>

        <hr class="my-8">

        <h2 class="text-2xl font-semibold text-gray-900 mb-6">Change Password</h2>
        <form action="#" method="POST">

            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Current Password *</label>
                    <input type="password" id="current_password" name="current_password"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password *</label>
                    <input type="password" id="confirm_password" name="confirm_password_confirmation"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>
            <div class="mb-6">
                <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">New Password *</label>
                <input type="password" id="new_password" name="new_password"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="text-right">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Update Password
                </button>
            </div>
        </form>

    </div>

@endsection