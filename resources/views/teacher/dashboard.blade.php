@extends('layouts.teacher')

@section('title', 'Teacher Dashboard')

@section('breadcrumb')
<nav class="flex items-center space-x-2 text-sm">
    <a href="{{ route('teacher.dashboard') }}" class="text-gray-600 hover:text-gray-900">Home</a>
    <i class="fas fa-chevron-right text-xs text-gray-400"></i>
    <span class="text-gray-900 font-medium">Teachers</span>
    <i class="fas fa-chevron-right text-xs text-gray-400"></i>
    <span class="text-gray-500">Dashboard</span>
</nav>
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-start justify-between">
                <div class="flex items-center space-x-4">
                    <img src="{{ $user->photo ? asset('storage/'.$user->photo) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=8B5CF6&color=fff' }}" 
                         class="w-24 h-24 rounded-full object-cover border-2 border-gray-100">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">{{ $user->name }}</h2>
                        <p class="text-gray-500 text-sm">{{ $user->email }}</p>
                    </div>
                </div>
                <span class="bg-blue-100 text-blue-600 px-4 py-1 rounded-full text-sm font-medium">
                    Active
                </span>
            </div>
        </div>

        <div class="bg-gradient-to-r from-purple-50 to-blue-50 rounded-xl shadow-sm p-6 flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">
                    Hello, <span class="text-purple-600">{{ explode(' ', $user->name)[0] }}!</span>
                </h2>
                <p class="text-gray-600 text-sm">Welcome back! Here is your schedule for today.</p>
            </div>
            <div class="hidden lg:block">
                <img src="{{ asset('images/teacher.png') }}" alt="Teacher" class="w-32 opacity-90">
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="mb-4 flex justify-between items-end">
            <div>
                <h3 class="text-xl font-bold text-gray-800">Today's Schedule</h3>
                <p class="text-gray-500 text-sm">{{ $today->format('l, d F Y') }}</p>
            </div>
        </div>

        <div class="space-y-4">
            @forelse($todaysClasses as $class)
                @php
                    $colors = ['blue', 'red', 'green', 'purple', 'orange'];
                    $color = $colors[$loop->index % count($colors)];
                @endphp

                <div class="border-l-4 border-{{ $color }}-500 bg-white rounded-lg p-4 shadow-sm hover:shadow-md transition border border-gray-100">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="text-lg font-bold text-gray-800 mb-2">
                                {{ $class->name }}
                            </h4>
                            
                            <div class="grid grid-cols-2 gap-6 text-sm text-gray-600">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-clock text-{{ $color }}-400 w-5"></i>
                                    <span>
                                        {{ \Carbon\Carbon::parse($class->start_time)->format('H:i') }} - 
                                        {{ \Carbon\Carbon::parse($class->end_time)->format('H:i') }}
                                    </span>
                                </div>
                                
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-door-open text-{{ $color }}-400 w-5"></i>
                                    <span>{{ $class->classroom }}</span>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('teacher.classes.detail', $class->id) }}" 
                           class="px-3 py-1 bg-gray-50 hover:bg-{{ $color }}-50 text-gray-400 hover:text-{{ $color }}-600 rounded-md transition">
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

            @empty
                <div class="text-center py-10">
                    <div class="bg-gray-100 rounded-full h-16 w-16 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-calendar-day text-gray-400 text-2xl"></i>
                    </div>
                    <p class="text-gray-800 font-medium">No classes today</p>
                    <p class="text-gray-500 text-sm">You have no scheduled classes for {{ $today->format('l') }}.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection