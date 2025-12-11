@php
    $total = $boys + $girls;
    $boysPercent = $total > 0 ? round($boys / $total * 100) : 0;
    $girlsPercent = $total > 0 ? round($girls / $total * 100) : 0;
@endphp

<x-app-layout>
    <x-slot name="header"></x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- 1. BREADCRUMB --}}
            <nav class="flex mb-8" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="#" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-blue-600 cursor-default">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                            Dashboard
                        </a>
                    </li>
                </ol>
            </nav>

            <div class="space-y-6">

                <div class="w-full bg-white p-6 rounded-xl shadow-sm flex flex-col md:flex-row justify-between items-center border border-gray-100">
                    <div class="text-gray-800 mb-4 md:mb-0 text-center md:text-left">
                        <h2 class="text-2xl font-bold bg-gradient-to-r from-blue-500 to-red-500 bg-clip-text text-transparent">
                            Welcome back, {{ Auth::user()->name }}!
                        </h2>
                        <p class="mt-2 text-gray-600 max-w-md text-sm leading-relaxed">
                            Manage your school operations efficiently. Here is today's overview of your academics and attendance.
                        </p>
                    </div>
                    <div class="hidden sm:block">
                        <img src="{{ asset('images/dashboard.png') }}" alt="Dashboard" class="rounded-lg object-contain h-32 w-auto">
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    {{-- A. STATS CARDS (Updated: 3 Kolom - Staff Removed) --}}
                    <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-4">
                        
                        {{-- Students --}}
                        <div class="bg-yellow-50 p-4 rounded-xl shadow-sm border border-yellow-100 flex flex-col justify-between hover:shadow-md transition-shadow">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="p-2 bg-yellow-100 rounded-lg text-yellow-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                </span>
                                <h4 class="text-xs font-bold text-yellow-700 uppercase tracking-wide">Students</h4>
                            </div>
                            <p class="text-3xl font-bold text-yellow-800 text-right">{{ $students }}</p>
                        </div>
                        
                        {{-- Teachers --}}
                        <div class="bg-purple-50 p-4 rounded-xl shadow-sm border border-purple-100 flex flex-col justify-between hover:shadow-md transition-shadow">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="p-2 bg-purple-100 rounded-lg text-purple-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                                </span>
                                <h4 class="text-xs font-bold text-purple-700 uppercase tracking-wide">Teachers</h4>
                            </div>
                            <p class="text-3xl font-bold text-purple-800 text-right">{{ $teachers }}</p>
                        </div>
                        
                        {{-- Classes --}}
                        <div class="bg-red-50 p-4 rounded-xl shadow-sm border border-red-100 flex flex-col justify-between hover:shadow-md transition-shadow">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="p-2 bg-red-100 rounded-lg text-red-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </span>
                                <h4 class="text-xs font-bold text-red-700 uppercase tracking-wide">Classes</h4>
                            </div>
                            <p class="text-3xl font-bold text-red-800 text-right">{{ $classes }}</p>
                        </div>
                    </div>

                    {{-- B. GENDER CHARTS (Col Span 1) --}}
                    <div class="lg:col-span-1 bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center justify-around">
                        {{-- Boys --}}
                        <div class="flex flex-col items-center">
                            <div class="relative w-24 h-24">
                                <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                                    <circle class="text-gray-100" stroke-width="8" stroke="currentColor" fill="transparent" r="40" cx="50" cy="50" />
                                    <circle class="text-blue-500 transition-all duration-1000 ease-out" stroke-width="8" stroke-linecap="round" stroke="currentColor" fill="transparent" r="40" cx="50" cy="50"
                                        stroke-dasharray="251.2"
                                        stroke-dashoffset="calc(251.2 - (251.2 * {{ $boysPercent }}) / 100)" />
                                </svg>
                                <div class="absolute inset-0 flex flex-col items-center justify-center">
                                    <span class="text-lg font-bold text-gray-800">{{ $boysPercent }}%</span>
                                </div>
                            </div>
                            <span class="mt-2 text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded-full">Boys</span>
                        </div>

                        {{-- Girls --}}
                        <div class="flex flex-col items-center">
                            <div class="relative w-24 h-24">
                                <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                                    <circle class="text-gray-100" stroke-width="8" stroke="currentColor" fill="transparent" r="40" cx="50" cy="50" />
                                    <circle class="text-pink-500 transition-all duration-1000 ease-out" stroke-width="8" stroke-linecap="round" stroke="currentColor" fill="transparent" r="40" cx="50" cy="50"
                                        stroke-dasharray="251.2"
                                        stroke-dashoffset="calc(251.2 - (251.2 * {{ $girlsPercent }}) / 100)" />
                                </svg>
                                <div class="absolute inset-0 flex flex-col items-center justify-center">
                                    <span class="text-lg font-bold text-gray-800">{{ $girlsPercent }}%</span>
                                </div>
                            </div>
                            <span class="mt-2 text-xs font-bold text-pink-600 bg-pink-50 px-2 py-1 rounded-full">Girls</span>
                        </div>
                    </div>

                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    {{-- C. TOTAL ATTENDANCE (Donut Chart) --}}
                    <div class="lg:col-span-1 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-bold text-gray-800">Attendance</h3>
                            <div class="flex bg-gray-100 p-1 rounded-lg">
                                <button id="btnToday" class="px-3 py-1 text-xs font-bold rounded-md bg-white text-blue-600 shadow-sm transition-all">Today</button>
                                <button id="btnAll" class="px-3 py-1 text-xs font-bold rounded-md text-gray-500 hover:text-gray-700 transition-all">All</button>
                            </div>
                        </div>

                        <div class="flex flex-col items-center">
                            <div class="relative w-40 h-40 mb-6">
                                <div class="w-40 h-40 rounded-full" id="attendanceChart">
                                    <div class="absolute inset-4 rounded-full bg-white shadow-inner"></div>
                                </div>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <span class="text-gray-400 text-xs font-semibold uppercase tracking-widest">Rate</span>
                                </div>
                            </div>
                            
                            {{-- Legend --}}
                            <div class="w-full space-y-2">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="flex items-center gap-2 text-gray-600"><span class="w-2 h-2 rounded-full bg-blue-500"></span>Present</span>
                                    <span id="presentVal" class="font-bold text-gray-800">0%</span>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="flex items-center gap-2 text-gray-600"><span class="w-2 h-2 rounded-full bg-yellow-500"></span>Late</span>
                                    <span id="lateVal" class="font-bold text-gray-800">0%</span>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="flex items-center gap-2 text-gray-600"><span class="w-2 h-2 rounded-full bg-green-500"></span>Permission</span>
                                    <span id="permissionVal" class="font-bold text-gray-800">0%</span>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="flex items-center gap-2 text-gray-600"><span class="w-2 h-2 rounded-full bg-purple-500"></span>Sick</span>
                                    <span id="sickVal" class="font-bold text-gray-800">0%</span>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="flex items-center gap-2 text-gray-600"><span class="w-2 h-2 rounded-full bg-red-500"></span>Absent</span>
                                    <span id="absentVal" class="font-bold text-gray-800">0%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- D. WEEKLY ABSENCE REPORT (Bar Chart) --}}
                    <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-bold text-gray-800">Weekly Absence Report</h3>
                        </div>
                        
                        {{-- Chart Area --}}
                        <div class="flex-1 flex items-end justify-between gap-4 px-4 pb-2 border-b border-gray-100" id="weeklyChart">
                            {{-- Bars will be injected by JS here --}}
                        </div>
                        <p class="text-center text-xs text-gray-400 mt-4">Total absence count per day (Last 7 days)</p>
                    </div>

                </div>

            </div>
        </div>
    </div>

    {{-- SCRIPTS --}}
    <script>
    document.addEventListener("DOMContentLoaded", function () {

        // --- 1. ATTENDANCE DONUT CHART ---
        function updateAttendanceChart(data) {
            let total = data.present + data.permission + data.sick + data.late + data.absent;
            if (total === 0) total = 1;

            const p  = (data.present / total) * 100;
            const pm = (data.permission / total) * 100;
            const s  = (data.sick / total) * 100;
            const l  = (data.late / total) * 100;
            const a  = (data.absent / total) * 100;

            // Update Text
            document.getElementById("presentVal").innerText = p.toFixed(1) + "%";
            document.getElementById("permissionVal").innerText = pm.toFixed(1) + "%";
            document.getElementById("sickVal").innerText = s.toFixed(1) + "%";
            document.getElementById("lateVal").innerText = l.toFixed(1) + "%";
            document.getElementById("absentVal").innerText = a.toFixed(1) + "%";

            // Update CSS Gradient
            const chart = document.getElementById("attendanceChart");
            chart.style.background = `
                conic-gradient(
                    rgb(59, 130, 246) 0% ${p}%,
                    rgb(234, 179, 8) ${p}% ${p + l}%,
                    rgb(34, 197, 94) ${p + l}% ${p + l + pm}%,
                    rgb(168, 85, 247) ${p + l + pm}% ${p + l + pm + s}%,
                    rgb(239, 68, 68) ${p + l + pm + s}% 100%
                )
            `;
        }

        function loadStats(type) {
            fetch(`/admin/attendance-stats?type=${type}`)
                .then(res => res.json())
                .then(data => updateAttendanceChart(data))
                .catch(err => console.error("Error:", err));
        }

        // Toggle Buttons Logic
        const btnToday = document.getElementById("btnToday");
        const btnAll = document.getElementById("btnAll");

        btnToday.addEventListener("click", function () {
            this.classList.add("bg-white", "text-blue-600", "shadow-sm");
            this.classList.remove("text-gray-500");
            
            btnAll.classList.remove("bg-white", "text-blue-600", "shadow-sm");
            btnAll.classList.add("text-gray-500");
            
            loadStats("today");
        });

        btnAll.addEventListener("click", function () {
            this.classList.add("bg-white", "text-blue-600", "shadow-sm");
            this.classList.remove("text-gray-500");
            
            btnToday.classList.remove("bg-white", "text-blue-600", "shadow-sm");
            btnToday.classList.add("text-gray-500");
            
            loadStats("all");
        });

        // Load Default
        loadStats("today");


        // --- 2. WEEKLY BAR CHART ---
        function loadWeeklyAbsence() {
            fetch("/admin/weekly-absence")
                .then(res => res.json())
                .then(data => renderWeeklyChart(data))
                .catch(err => console.error(err));
        }

        function renderWeeklyChart(data) {
            const container = document.getElementById("weeklyChart");
            container.innerHTML = "";

            // Cari nilai max untuk skala tinggi bar
            const maxVal = Math.max(...data.map(i => i.total), 1); 

            data.forEach(item => {
                // Hitung tinggi relatif (max height 150px misal)
                const heightPercent = (item.total / maxVal) * 100;
                
                // Warna bar: Jika 0 (bagus) abu2, jika ada absen merah
                const barColor = item.total > 0 ? 'bg-red-400' : 'bg-gray-200';
                const labelColor = item.total > 0 ? 'text-red-600 font-bold' : 'text-gray-400';

                const column = `
                    <div class="flex flex-col items-center flex-1 h-48 justify-end group">
                        <span class="mb-2 text-xs ${labelColor} opacity-0 group-hover:opacity-100 transition-opacity">${item.total}</span>
                        <div class="w-full max-w-[40px] ${barColor} rounded-t-lg transition-all duration-500 hover:bg-red-500" 
                             style="height: ${heightPercent > 0 ? heightPercent : 2}%;"></div>
                        <span class="mt-3 text-xs font-bold text-gray-500 uppercase">${item.day_label}</span>
                    </div>
                `;
                container.innerHTML += column;
            });
        }

        loadWeeklyAbsence();
    });
    </script>

</x-app-layout>