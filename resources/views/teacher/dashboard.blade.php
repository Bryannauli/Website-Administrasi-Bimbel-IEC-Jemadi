{{-- resources/views/teacher/dashboard.blade.php --}}

<x-app-layout>

    {{-- Wrapper Konten Utama (Ganti py-8 menjadi py-6 untuk konsistensi) --}}
    <div class="py-6"> 
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            {{-- 1. BREADCRUMB (DISINGKAT AGAR KONSISTEN DENGAN DASHBOARD ADMIN) --}}
            <nav class="flex mb-8" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        {{-- Sesuaikan dengan rute guru --}}
                        <a href="{{ route('teacher.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-blue-600 cursor-default">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                            Dashboard
                        </a>
                    </li>
                </ol>
            </nav>

            {{-- 2. WELCOME SECTION (Grid 2 Kolom) --}}
            {{-- Gunakan Div yang sama dengan dashboard admin agar konsisten --}}
            <div class="w-full bg-white p-6 rounded-xl shadow-sm flex flex-col md:flex-row justify-between items-center border border-gray-100">
                <div class="text-gray-800 mb-4 md:mb-0 text-center md:text-left">
                    <h2 class="text-2xl font-bold bg-gradient-to-r from-blue-500 to-red-500 bg-clip-text text-transparent">
                        Welcome back, {{ explode(' ', $user->name)[0] }}!
                    </h2>
                    <p class="mt-2 text-gray-600 max-w-md text-sm leading-relaxed">
                        You have <strong class="text-indigo-600">{{ $todaysClasses->count() }} classes</strong> scheduled for today. Have a great day!
                    </p>
                </div>
                <div class="hidden sm:block">
                    {{-- Ganti dengan asset yang sesuai atau hapus jika tidak ada --}}
                    <img src="{{ asset('images/dashboard.png') }}" alt="Dashboard" class="rounded-lg object-contain h-32 w-auto"> 
                </div>
            </div>

            {{-- 3. SCHEDULE SECTION (Ganti space-y-8 menjadi space-y-6) --}}
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