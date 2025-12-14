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
                        <p class="text-gray-500 text-sm mb-3 font-medium">{{ $user->email }}</p>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                            Teacher 
                        </span>
                    </div>
                </div>

                {{-- Greeting Banner (WARNA LEBIH LEMBUT/PASTEL) --}}
                {{-- Menggunakan bg-gradient-to-r from-blue-50 to-indigo-50 (Sangat lembut) --}}
                <div class="relative bg-gradient-to-r from-blue-50 to-red-100 rounded-2xl shadow-sm p-6 flex items-center justify-between overflow-hidden border border-blue-100 hover:shadow-md transition-shadow duration-300">
                    <div class="relative z-10 max-w-md">
                        <h2 class="text-2xl font-bold mb-2 text-gray-800">
                            Hello, <span class="text-indigo-600">{{ explode(' ', $user->name)[0] }}!</span> 👋
                        </h2>
                        <p class="text-gray-600 text-sm leading-relaxed">
                            Welcome back! You have <strong class="text-indigo-600">{{ $todaysClasses->count() }} classes</strong> scheduled for today. Have a great day!
                        </p>
                    </div>
                    {{-- Ilustrasi Guru (Gunakan gambar asset Anda jika ada, atau dekorasi simple) --}}
                    <div class="hidden sm:block opacity-90">
                         {{-- Ganti src ini dengan gambar ilustrasi guru Anda --}}
                         {{-- <img src="{{ asset('images/teacher_illustration.png') }}" class="h-32 w-auto"> --}}
                         
                         {{-- Atau gunakan dekorasi icon jika gambar tidak ada --}}
                         <div class="h-24 w-24 bg-white rounded-full flex items-center justify-center shadow-sm">
                            <i class="fas fa-chalkboard-teacher text-4xl text-indigo-400"></i>
                         </div>
                    </div>
                </div>
            </div>

            {{-- 3. SCHEDULE SECTION --}}
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">Today's Schedule</h3>
                        <p class="text-gray-500 text-sm mt-1 flex items-center gap-2">
                            <i class="far fa-calendar-alt text-gray-400"></i>
                            {{ $today->format('l, d F Y') }}
                        </p>
                    </div>
                    @if($todaysClasses->isNotEmpty())
                        <span class="bg-white border border-gray-200 text-gray-600 px-4 py-1.5 rounded-lg text-xs font-bold shadow-sm">
                            {{ $todaysClasses->count() }} Sessions
                        </span>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @forelse($todaysClasses as $class)
                        {{-- Logic Warna --}}
                        @php
                            $colors = ['blue', 'purple', 'emerald', 'amber', 'rose'];
                            $baseColor = $colors[$loop->index % count($colors)];
                            
                            // Definisikan class CSS secara eksplisit
                            $borderClass = match($baseColor) {
                                'blue' => 'border-l-blue-500',
                                'purple' => 'border-l-purple-500',
                                'emerald' => 'border-l-emerald-500',
                                'amber' => 'border-l-amber-500',
                                'rose' => 'border-l-rose-500',
                                default => 'border-l-blue-500',
                            };
                            $iconColor = match($baseColor) {
                                'blue' => 'text-blue-500',
                                'purple' => 'text-purple-500',
                                'emerald' => 'text-emerald-500',
                                'amber' => 'text-amber-500',
                                'rose' => 'text-rose-500',
                                default => 'text-blue-500',
                            };
                            $btnClass = match($baseColor) {
                                'blue' => 'bg-blue-50 text-blue-600 hover:bg-blue-100',
                                'purple' => 'bg-purple-50 text-purple-600 hover:bg-purple-100',
                                'emerald' => 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100',
                                'amber' => 'bg-amber-50 text-amber-600 hover:bg-amber-100',
                                'rose' => 'bg-rose-50 text-rose-600 hover:bg-rose-100',
                                default => 'bg-blue-50 text-blue-600 hover:bg-blue-100',
                            };
                        @endphp

                        <div class="group bg-white rounded-xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 border-l-[5px] {{ $borderClass }}">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h4 class="text-lg font-bold text-gray-800 group-hover:text-gray-600 transition-colors">
                                        {{ $class->name }}
                                    </h4>
                                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                        {{ $class->category ?? 'Classroom' }}
                                    </span>
                                </div>
                                <a href="{{ route('teacher.classes.detail', $class->id) }}" 
                                   class="w-9 h-9 flex items-center justify-center rounded-lg {{ $btnClass }} transition-colors">
                                    <i class="fas fa-arrow-right text-sm"></i>
                                </a>
                            </div>

                            <div class="space-y-3 pt-2">
                                <div class="flex items-center text-sm text-gray-600">
                                    <div class="w-8 flex justify-center mr-2">
                                        <i class="far fa-clock {{ $iconColor }} text-lg"></i>
                                    </div>
                                    <span class="font-medium bg-gray-50 px-2 py-0.5 rounded text-gray-700">
                                        {{ \Carbon\Carbon::parse($class->start_time)->format('H:i') }} - 
                                        {{ \Carbon\Carbon::parse($class->end_time)->format('H:i') }}
                                    </span>
                                </div>
                                
                                <div class="flex items-center text-sm text-gray-600">
                                    <div class="w-8 flex justify-center mr-2">
                                        <i class="fas fa-door-open {{ $iconColor }} text-lg"></i>
                                    </div>
                                    <span class="font-medium">{{ $class->classroom ?? 'Room A' }}</span>
                                </div>
                            </div>
                        </div>

                    @empty
                        <div class="col-span-full bg-white rounded-2xl border border-dashed border-gray-300 p-12 text-center">
                            <div class="bg-gray-50 rounded-full h-20 w-20 flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-mug-hot text-gray-400 text-3xl"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-800">No Classes Today</h3>
                            <p class="text-gray-500 text-sm mt-2 max-w-sm mx-auto leading-relaxed">
                                You don't have any scheduled classes for {{ $today->format('l') }}. Enjoy your free time or prepare for upcoming sessions!
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>