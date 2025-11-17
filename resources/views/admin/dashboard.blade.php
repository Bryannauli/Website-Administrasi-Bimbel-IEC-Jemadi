{{-- resources/views/admin/dashboard.blade.php --}}

@extends('layouts.admin')

@section('title', 'Dashboard - Admin IEC')
@section('breadcrumb', 'Dashboard')

@section('content')

    {{-- Welcome Banner --}}
    <div class="mb-8 p-6 bg-white rounded-xl shadow-lg flex justify-between items-center">
        <div>
            <h2 class="text-3xl font-bold text-gray-800 mb-2">Welcome, Moni Roy School Team!</h2> {{-- Ukuran dan margin disesuaikan --}}
            <p class="text-gray-600 text-base leading-relaxed max-w-lg">Manage your school operations with ease. Stay updated on academics, attendance, finances, and more—all in one place. Let's keep shaping a brighter future together!</p> {{-- Font size dan line-height --}}
        </div>
        <div>
            <img src="https://i.imgur.com/I2dYq1I.png" alt="Illustration" class="h-40">
        </div>
    </div>

    {{-- Stat Cards Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8"> {{-- Grid dan gap disesuaikan --}}
        
        {{-- Students Card --}}
        <div class="bg-white p-6 rounded-xl shadow-lg flex justify-between items-center">
            <div>
                <p class="text-gray-500 text-sm font-semibold mb-1">Students</p> {{-- Font size dan weight disesuaikan --}}
                <h3 class="text-4xl font-extrabold text-gray-900">5,909</h3> {{-- Font size dan weight disesuaikan --}}
            </div>
            <button class="bg-[#EBF7F0] text-[#47AD7C] w-12 h-12 rounded-full text-xl hover:bg-[#e0f1e7] transition-all"> {{-- Warna dan hover disesuaikan --}}
                <i class="fa-solid fa-plus"></i>
            </button>
        </div>
        
        {{-- Teachers Card --}}
        <div class="bg-white p-6 rounded-xl shadow-lg flex justify-between items-center">
            <div>
                <p class="text-gray-500 text-sm font-semibold mb-1">Teachers</p>
                <h3 class="text-4xl font-extrabold text-gray-900">60</h3>
            </div>
            <button class="bg-[#F0EBFF] text-[#865EFF] w-12 h-12 rounded-full text-xl hover:bg-[#e7e1f4] transition-all"> {{-- Warna dan hover disesuaikan --}}
                <i class="fa-solid fa-plus"></i>
            </button>
        </div>
        
        {{-- Employee Card --}}
        <div class="bg-white p-6 rounded-xl shadow-lg flex justify-between items-center">
            <div>
                <p class="text-gray-500 text-sm font-semibold mb-1">Employee</p>
                <h3 class="text-4xl font-extrabold text-gray-900">100</h3>
            </div>
            <button class="bg-[#EBF1FF] text-[#477CFF] w-12 h-12 rounded-full text-xl hover:bg-[#e0e7f7] transition-all"> {{-- Warna dan hover disesuaikan --}}
                <i class="fa-solid fa-plus"></i>
            </button>
        </div>
        
        {{-- Class Card --}}
        <div class="bg-white p-6 rounded-xl shadow-lg flex justify-between items-center">
            <div>
                <p class="text-gray-500 text-sm font-semibold mb-1">Class</p>
                <h3 class="text-4xl font-extrabold text-gray-900">10</h3>
            </div>
            <button class="bg-[#FFF3E8] text-[#FF8A00] w-12 h-12 rounded-full text-xl hover:bg-[#fae6d3] transition-all"> {{-- Warna dan hover disesuaikan --}}
                <i class="fa-solid fa-plus"></i>
            </button>
        </div>
    </div>
    
    {{-- Charts Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-1 flex flex-col gap-6">
            {{-- Students Chart Card --}}
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <h4 class="text-lg font-semibold text-gray-800 mb-4">Students</h4>
                {{-- Placeholder untuk chart siswa --}}
                <div id="chartSiswaDonut" class="w-full flex justify-center py-4">
                    <img src="https://i.imgur.com/gK9J6t7.png" alt="Student Chart" class="w-[200px] h-[200px] object-contain"> {{-- Gambar dari Figma --}}
                </div>
            </div>
            
            {{-- Total Attendance Chart Card --}}
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="text-lg font-semibold text-gray-800">Total Attendance</h4>
                    <div class="flex space-x-2 text-sm">
                        <button class="bg-[#EBF1FF] text-[#477CFF] font-medium px-3 py-1 rounded-md">Today</button> {{-- Warna disesuaikan --}}
                        <button class="text-gray-500 font-medium px-3 py-1 rounded-md hover:bg-gray-100">All</button>
                        <button class="text-gray-500 font-medium px-3 py-1 rounded-md hover:bg-gray-100"> <i class="fa-solid fa-chevron-down fa-xs ml-1"></i></button> {{-- Tambah ikon --}}
                    </div>
                </div>
                {{-- Placeholder untuk chart absensi --}}
                <div id="chartAbsensiDonut" class="w-full flex justify-center py-4">
                    <img src="https://i.imgur.com/uRj0p1q.png" alt="Attendance Chart" class="w-[200px] h-[200px] object-contain"> {{-- Gambar dari Figma --}}
                </div>
                <button class="w-full mt-4 bg-[#477CFF] text-white py-2 rounded-lg font-medium hover:bg-[#3b66d9] transition-all flex items-center justify-center space-x-2"> {{-- Tombol Export --}}
                    <i class="fa-solid fa-download"></i> <span>Export</span>
                </button>
            </div>
        </div>
        
        {{-- Weekly Absence Report Chart Card --}}
        <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-lg">
            <div class="flex justify-between items-center mb-4">
                <h4 class="text-lg font-semibold text-gray-800">Weekly Absence Report</h4>
                <button class="text-gray-500 text-sm font-medium px-3 py-1 rounded-md hover:bg-gray-100">
                    This Week <i class="fa-solid fa-chevron-down fa-xs ml-1"></i>
                </button>
            </div>
            {{-- Placeholder untuk chart batang --}}
            <div id="chartAbsensiMingguan" class="w-full h-96 min-h-[400px]">
                <img src="https://i.imgur.com/39l21zZ.png" alt="Weekly Absence Chart" class="w-full h-full object-contain"> {{-- Gambar dari Figma --}}
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    {{-- Ini adalah tempat untuk script JS ApexCharts jika nanti digunakan --}}
@endpush