<!-- views/admin/dashboard -->
@php
    $total = $boys + $girls;
    $boysPercent = $total > 0 ? round($boys / $total * 100) : 0;
    $girlsPercent = $total > 0 ? round($girls / $total * 100) : 0;
@endphp
<x-app-layout>
    {{-- Slot header dari Breeze tidak digunakan di desain ini, jadi kita kosongkan --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight hidden">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    {{-- PERBAIKAN: HAPUS ml-64 dari sini. Padding diatur oleh layout utama --}}
    <div class="py-6">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Ini adalah awal dari konten dashboard kustom Anda --}}
            <div class="space-y-6 px-4 sm:px-0"> {{-- Tambah px-4 untuk padding di mobile --}}

                <div class="text-sm text-gray-500">> Dashboard</div>

                <!-- Welcome Banner -->
                <div class="w-full bg-white p-6 rounded-xl shadow-sm flex flex-col md:flex-row justify-between items-center">
                    <div class="text-gray-800 mb-4 md:mb-0 text-center md:text-left">
                        <h2 class="text-2xl font-bold bg-gradient-to-r from-blue-500 to-red-500 bg-clip-text text-transparent">Welcome, {{ Auth::user()->name }}!</h2>
                        <p class="mt-2 text-gray-700 max-w-md">
                            Manage your school operations with ease. Stay updated on academics, attendance, finances, and more all in one place.
                        </p>
                    </div>
                    <div class="hidden sm:block">
                        <img src="{{asset('images/dashboard.png') }}" alt="Dashboard Illustration" class="rounded-lg object-cover w-full max-w-xs">
                    </div>
                </div>

                <!-- Grid Utama -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    <div class="flex flex-col sm:flex-row justify-around items-center space-y-6 sm:space-y-0 sm:space-x-6 mt-4 lg:col-span-1">
                        <div class="flex flex-col items-center">
                            <div class="relative w-36 h-36">
                                <svg class="w-full h-full" viewBox="0 0 100 100">
                                    <circle class="text-gray-200" stroke-width="10" stroke="currentColor" fill="transparent" r="40" cx="50" cy="50" />
                                    <circle class="text-blue-500" stroke-width="10" stroke-linecap="round" stroke="currentColor" fill="transparent" r="40" cx="50" cy="50"
                                        stroke-dasharray="251.2"
                                        stroke-dashoffset="calc(251.2 - (251.2 * {{ $boysPercent }}) / 100)"
                                        transform="rotate(-90 50 50)" />
                                </svg>
                                <div class="absolute inset-0 flex flex-col items-center justify-center">
                                    <span class="text-2xl font-bold text-gray-900">{{ $boysPercent }}%</span>
                                </div>
                            </div>
                            <span class="mt-2 text-sm text-gray-500">≈ {{ $boys }} (boys)</span>
                        </div>

                        <div class="flex flex-col items-center">
                            <div class="relative w-36 h-36">
                                <svg class="w-full h-full" viewBox="0 0 100 100">
                                    <circle class="text-gray-200" stroke-width="10" stroke="currentColor" fill="transparent" r="40" cx="50" cy="50" />
                                    <circle class="text-pink-500" stroke-width="10" stroke-linecap="round" stroke="currentColor" fill="transparent" r="40" cx="50" cy="50"
                                        stroke-dasharray="251.2"
                                        stroke-dashoffset="calc(251.2 - (251.2 * {{ $girlsPercent }}) / 100)"
                                        transform="rotate(-90 50 50)" />
                                </svg>
                                <div class="absolute inset-0 flex flex-col items-center justify-center">
                                    <span class="text-2xl font-bold text-gray-900">{{ $girlsPercent }}%</span>
                                </div>
                            </div>
                            <span class="mt-2 text-sm text-gray-500">≈ {{ $girls }} (girls)</span>
                        </div>
                    </div>

                    <div class="lg:col-span-1 grid grid-cols-2 gap-4 sm:gap-6">
                        <div class="bg-yellow-100 p-4 rounded-xl shadow-sm flex flex-col justify-between">
                            <div class="flex justify-between items-center">
                                <h4 class="font-semibold text-yellow-800">Students</h4>
                            </div>
                            <p class="text-2xl sm:text-3xl font-bold text-yellow-900 mt-2">{{ $students }}</p>
                        </div>
                        
                        <div class="bg-purple-100 p-4 rounded-xl shadow-sm flex flex-col justify-between">
                            <div class="flex justify-between items-center">
                                <h4 class="font-semibold text-purple-800">Teachers</h4>
                            </div>
                            <p class="text-2xl sm:text-3xl font-bold text-purple-900 mt-2">{{ $teachers }}</p>
                        </div>
                        
                        <div class="bg-green-100 p-4 rounded-xl shadow-sm flex flex-col justify-between">
                            <div class="flex justify-between items-center">
                                <h4 class="font-semibold text-green-800">Employee</h4>
                            </div>
                            <p class="text-2xl sm:text-3xl font-bold text-green-900 mt-2">{{ $employees }}</p>
                        </div>
                        
                        <div class="bg-red-100 p-4 rounded-xl shadow-sm flex flex-col justify-between">
                            <div class="flex justify-between items-center">
                                <h4 class="font-semibold text-red-800">Class</h4>
                            </div>
                            <p class="text-2xl sm:text-3xl font-bold text-red-900 mt-2">{{ $classes }}</p>
                        </div>
                    </div>

                    <div class="lg:col-span-1 bg-white p-6 rounded-xl shadow-sm">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Total Attendance</h3>
                            <div class="flex space-x-2 text-sm">
                                <button id="btnToday" class="text-blue-600 font-medium">Today</button>
                                <button id="btnAll" class="text-gray-400 hover:text-gray-600">All</button>
                            </div>
                        </div>

                        <div class="flex flex-col items-center justify-center space-y-4">
                            <div class="relative w-36 h-36">
                                <div class="w-36 h-36 rounded-full" id="attendanceChart">
                                    <div class="absolute inset-4 rounded-full bg-white"></div>
                                </div>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <span class="text-gray-400 text-xs">Overview</span>
                                </div>
                            </div>
                            <ul class="space-y-1 text-sm text-gray-700 w-full px-4">
                                <li class="flex justify-between"><span class="flex items-center"><span class="w-2 h-2 rounded-full bg-blue-500 mr-2"></span>Present</span><span id="presentVal">0%</span></li>
                                <li class="flex justify-between"><span class="flex items-center"><span class="w-2 h-2 rounded-full bg-green-500 mr-2"></span>Permission</span><span id="permissionVal">0%</span></li>
                                <li class="flex justify-between"><span class="flex items-center"><span class="w-2 h-2 rounded-full bg-yellow-500 mr-2"></span>Sick</span><span id="sickVal">0%</span></li>
                                <li class="flex justify-between"><span class="flex items-center"><span class="w-2 h-2 rounded-full bg-purple-500 mr-2"></span>Late</span><span id="lateVal">0%</span></li>
                                <li class="flex justify-between"><span class="flex items-center"><span class="w-2 h-2 rounded-full bg-gray-400 mr-2"></span>Absent</span><span id="absentVal">0%</span></li>
                            </ul>
                        </div>
                    </div>

                    <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm overflow-hidden">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Weekly Absence Report</h3>
                        </div>
                        <div id="weeklyChart" class="w-full h-48 flex justify-around items-end space-x-2 border-b border-gray-200 pb-2">
                            </div>
                    </div>

                    <div class="lg:col-span-1 bg-white p-6 rounded-xl shadow-sm overflow-hidden">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h3>
                        <div class="flow-root max-h-[300px] overflow-y-auto"> <ul role="list" class="-mb-8">
                                @forelse($all_logs ?? [] as $log)
                                <li>
                                    <div class="relative pb-8">
                                        @if(!$loop->last)
                                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                        @endif
                                        <div class="relative flex space-x-3">
                                            
                                            {{-- Ikon Aksi --}}
                                            <div>
                                                @php
                                                    $color = $log->action == 'CREATE' ? 'bg-green-500' : ($log->action == 'UPDATE' ? 'bg-blue-500' : 'bg-red-500');
                                                @endphp
                                                <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white {{ $color }}">
                                                    {{-- Tentukan ikon berdasarkan Aksi --}}
                                                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        @if($log->action == 'CREATE')
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                        @elseif($log->action == 'UPDATE')
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                        @else
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        @endif
                                                    </svg>
                                                </span>
                                            </div>

                                            {{-- Detail Log --}}
                                            <div class="min-w-0 flex-1 pt-1.5 flex flex-col sm:flex-row justify-between space-x-4">
                                                <div class="text-sm text-gray-500">
                                                    {{-- Baris 1: Detail Aksi dan Item --}}
                                                    <p>
                                                        <span class="font-medium text-gray-900">{{ $log->user->name ?? 'System' }}</span>
                                                        {{ strtolower($log->action) }}d 
                                                        
                                                        {{-- Tentukan objek yang diubah berdasarkan tipe log --}}
                                                        @if($log->student)
                                                            <span class="font-medium text-blue-600">Student: {{ $log->student->name }}</span>
                                                        @elseif($log->assessmentSession)
                                                            <span class="font-medium text-purple-600">Session: {{ $log->assessmentSession->type }} on {{ $log->assessmentSession->date }}</span>
                                                        @elseif($log->assessmentForm && $log->assessmentForm->student)
                                                            <span class="font-medium text-orange-600">Form for: {{ $log->assessmentForm->student->name }}</span>
                                                        @else
                                                            <span class="font-medium text-gray-400">Unknown Item</span>
                                                        @endif
                                                    </p>
                                                    
                                                    {{-- Baris 2: Keterangan Tambahan (jika ada perubahan) --}}
                                                    @if(isset($log->new_values) && count($log->new_values) > 0)
                                                        <p class="text-xs mt-1 italic text-gray-400">
                                                            @php
                                                                $changedKeys = array_keys(is_array($log->new_values) ? $log->new_values : json_decode($log->new_values, true));
                                                                $displayKeys = implode(', ', array_slice($changedKeys, 0, 2));
                                                                if (count($changedKeys) > 2) {
                                                                    $displayKeys .= '...';
                                                                }
                                                            @endphp
                                                            Changed: {{ $displayKeys }}
                                                        </p>
                                                    @endif
                                                </div>

                                                {{-- Waktu --}}
                                                <div class="text-right text-xs whitespace-nowrap text-gray-500 pt-1 sm:pt-0">
                                                    {{ $log->created_at->diffForHumans() }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                @empty
                                    <li class="text-sm text-gray-500 text-center py-4">No recent activities recorded.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>

<script>
document.addEventListener("DOMContentLoaded", function () {

    function updateAttendanceChart(data) {
        let total = data.present + data.permission + data.sick + data.late + data.absent;
        if (total === 0) total = 1;

        const p  = (data.present / total) * 100;
        const pm = (data.permission / total) * 100;
        const s  = (data.sick / total) * 100;
        const l  = (data.late / total) * 100;
        const a  = (data.absent / total) * 100;

        // Update angka persentase di bawah chart
        document.getElementById("presentVal").innerText = p.toFixed(1) + "%";
        document.getElementById("permissionVal").innerText = pm.toFixed(1) + "%";
        document.getElementById("sickVal").innerText = s.toFixed(1) + "%";
        document.getElementById("lateVal").innerText = l.toFixed(1) + "%";
        document.getElementById("absentVal").innerText = a.toFixed(1) + "%";

        // Update tampilan chart
        const chart = document.getElementById("attendanceChart");

        chart.style.background = `
            conic-gradient(
                rgb(59, 130, 246) 0% ${p}%,
                rgb(16, 185, 129) ${p}% ${p + pm}%,
                rgb(234, 179, 8) ${p + pm}% ${p + pm + s}%,
                rgb(168, 85, 247) ${p + pm + s}% ${p + pm + s + l}%,
                rgb(156, 163, 175) ${p + pm + s + l}% 100%
            )
        `;
    }

    function loadStats(type) {
        fetch(`/admin/attendance-stats?type=${type}`)
            .then(res => res.json())
            .then(data => {
                console.log("Attendance data:", data);
                updateAttendanceChart(data);
            })
            .catch(err => console.error("Error:", err));
    }

    // Tombol
    const btnToday = document.getElementById("btnToday");
    const btnAll = document.getElementById("btnAll");

    btnToday.addEventListener("click", function () {
        btnToday.classList.add("text-blue-600");
        btnAll.classList.remove("text-blue-600");
        loadStats("today");
    });

    btnAll.addEventListener("click", function () {
        btnAll.classList.add("text-blue-600");
        btnToday.classList.remove("text-blue-600");
        loadStats("all");
    });

    // load default
    loadStats("today");

document.addEventListener("DOMContentLoaded", function () {

    function loadWeeklyAbsence() {
        fetch("/admin/weekly-absence")
            .then(res => res.json())
            .then(data => {
                renderWeeklyChart(data);
            })
            .catch(err => console.error(err));
    }

    function renderWeeklyChart(data) {
        const container = document.getElementById("weeklyChart");
        container.innerHTML = "";

        data.forEach(item => {
            const height = item.total * 10 + 5;

            const column = `
                <div class="flex flex-col items-center flex-1">
                    <div class="w-4 sm:w-6 bg-blue-500 rounded-t-lg" style="height: ${height}px;"></div>
                    <span class="mt-1 text-xs text-gray-500">${item.day_label}</span>
                </div>
            `;

            container.innerHTML += column;
        });
    }

    loadWeeklyAbsence();
});
});
</script>

</x-app-layout>