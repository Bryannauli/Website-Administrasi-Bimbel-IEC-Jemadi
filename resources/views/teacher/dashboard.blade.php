<!-- resources/views/teacher/dashboard.blade.php -->
@extends('layouts.teacher')

@section('title', 'Teacher Dashboard')

@section('breadcrumb')
<nav class="flex items-center space-x-2 text-sm">
    <a href="{{ route('teacher.dashboard') }}" class="text-gray-600 hover:text-gray-900">Home</a>
    <i class="fas fa-chevron-right text-xs text-gray-400"></i>
    <span class="text-gray-900 font-medium">Teachers</span>
    <i class="fas fa-chevron-right text-xs text-gray-400"></i>
    <span class="text-gray-500">Teacher's Details</span>
</nav>
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Welcome Card -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Profile Card -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-start justify-between">
                <div class="flex items-center space-x-4">
                    <img src="https://ui-avatars.com/api/?name=Kim+Geonwoo&background=8B5CF6&color=fff" class="w-24 h-24 rounded-full">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Kim Geonwoo S.Si</h2>
                        <p class="text-blue-600 font-medium">127893683</p>
                    </div>
                </div>
                <span class="bg-blue-100 text-blue-600 px-4 py-1 rounded-full text-sm font-medium">Active</span>
            </div>
        </div>

        <!-- Welcome Message -->
        <div class="bg-gradient-to-r from-purple-50 to-blue-50 rounded-xl shadow-sm p-6 flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">
                    Hello, <span class="text-purple-600">Geonwoo!</span>
                </h2>
                <p class="text-gray-600">Welcome back! We're here to support you on your learning journey. Dive into your classes and keep progressing towards your goals</p>
            </div>
            <div class="hidden lg:block">
                <img src="/images/teacher-illustration.png" alt="Teacher" class="w-48">
            </div>
        </div>
    </div>

    <!-- Today's Schedule -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="mb-4">
            <h3 class="text-xl font-bold text-gray-800">Today' Schedule</h3>
            <p class="text-gray-500 text-sm">Thursday, 10th April, 2021</p>
        </div>

        <div class="space-y-4">
            <!-- Schedule Item 1 -->
            <div class="border-l-4 border-blue-500 bg-white rounded-lg p-4 shadow-sm hover:shadow-md transition">
                <h4 class="text-lg font-bold text-gray-800 mb-2">Speaking</h4>
                <div class="grid grid-cols-3 gap-4 text-sm text-gray-600">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-calendar text-gray-400"></i>
                        <span>March 20, 2021</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-clock text-gray-400"></i>
                        <span>09.00 - 10.00 AM</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-door-open text-gray-400"></i>
                        <span>E-101</span>
                    </div>
                </div>
            </div>

            <!-- Schedule Item 2 -->
            <div class="border-l-4 border-red-500 bg-white rounded-lg p-4 shadow-sm hover:shadow-md transition">
                <h4 class="text-lg font-bold text-gray-800 mb-2">Vocabulary</h4>
                <div class="grid grid-cols-3 gap-4 text-sm text-gray-600">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-calendar text-gray-400"></i>
                        <span>March 20, 2021</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-clock text-gray-400"></i>
                        <span>08.00 - 09.00 AM</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-door-open text-gray-400"></i>
                        <span>E-101</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection