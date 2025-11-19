@extends('layouts.app')

@section('title', 'Add New Student - AIMS')

@section('breadcrumb')
    <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900">Home</a>
    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
    </svg>
    <a href="#" class="text-gray-600 hover:text-gray-900">Student</a>
    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
    </svg>
    <span class="text-gray-900 font-medium">Add New Student</span>
@endsection

@section('content')
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold">
            <span class="text-red-500">Add New </span>
            <span class="text-purple-600">Student</span>
        </h1>
        
        <div class="flex items-center gap-3">
            <button class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                Cancel
            </button>
            <button class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                Reset
            </button>
            <button class="px-6 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors">
                Save
            </button>
        </div>
    </div>

    <form class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Basic Information -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg p-8">
            <h2 class="text-xl font-bold text-gray-800 mb-6">Basic Information</h2>
            
            <div class="space-y-6">
                <div class="grid grid-cols-2 gap-6">
                    <!-- First Name -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            First Name
                        </label>
                        <input type="text" placeholder="First Name" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all">
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Last Name
                        </label>
                        <input type="text" placeholder="Last Name" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all">
                    </div>
                </div>

                <!-- Gender -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        Gender
                    </label>
                    <div class="flex items-center gap-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="gender" value="male" checked class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                            <span class="text-gray-700">Male</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="gender" value="female" class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                            <span class="text-gray-700">female</span>
                        </label>
                    </div>
                </div>

                <!-- Date of Birth -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Date of Birth
                    </label>
                    <input type="text" placeholder="dd/mm/yyyy" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all">
                </div>

                <!-- Class and Section -->
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Class
                        </label>
                        <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all">
                            <option>Select Class</option>
                            <option>English 1</option>
                            <option>English 2</option>
                            <option>Math 1</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Section
                        </label>
                        <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all">
                            <option>Select Section</option>
                            <option>A</option>
                            <option>B</option>
                            <option>C</option>
                        </select>
                    </div>
                </div>

                <!-- Upload Photo -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        Upload Photo
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-12 text-center hover:border-blue-400 transition-colors cursor-pointer">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <p class="text-gray-600 mb-2">Drop your files to upload</p>
                            <button type="button" class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                Select files
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Information & Status -->
        <div class="space-y-6">
            <!-- Contact Information -->
            <div class="bg-white rounded-2xl shadow-lg p-8">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Contact Information</h2>
                
                <div class="space-y-6">
                    <!-- Phone -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Phone
                        </label>
                        <input type="tel" placeholder="Contact number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all">
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Email
                        </label>
                        <input type="email" placeholder="exampe@gmail.com" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all">
                    </div>

                    <!-- Address -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Address
                        </label>
                        <input type="text" placeholder="Street" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all">
                    </div>
                </div>
            </div>

            <!-- Status -->
            <div class="bg-white rounded-2xl shadow-lg p-8">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Status</h2>
                
                <div class="space-y-3">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="radio" name="status" value="active" checked class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                        <span class="text-gray-700 font-medium">Active</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="radio" name="status" value="inactive" class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                        <span class="text-gray-700 font-medium">Inactive</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="radio" name="status" value="graduated" class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                        <span class="text-gray-700 font-medium">Graduated</span>
                    </label>
                </div>
            </div>
        </div>
    </form>
@endsection