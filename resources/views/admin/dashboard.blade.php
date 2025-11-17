@extends('layouts.admin')
@section('title', 'Dashboard - Admin IEC')
@section('breadcrumb', 'Dashboard')
@section('content')

    <div class="mb-8 p-6 bg-white rounded-xl shadow-lg flex justify-between items-center">
        <div>
            <h2 class="text-3xl font-bold text-gray-800">Welcome, Moni Roy School Team!</h2>
            <p class="text-gray-600 mt-2 max-w-lg">Manage your school operations with ease. Stay updated on academics, attendance, finances, and more—all in one place.</p>
        </div>
        <div>
            <img src="https://i.imgur.com/I2dYq1I.png" alt="Illustration" class="h-40">
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        <div class="bg-white p-6 rounded-xl shadow-lg flex justify-between items-center">
            <div>
                <p class="text-gray-500 font-medium">Students</p>
                <h3 class="text-4xl font-bold text-gray-900 mt-1">5,909</h3> 
            </div>
            <button class="bg-green-100 text-green-600 w-12 h-12 rounded-full text-xl hover:bg-green-200 transition-all">
                <i class="fa-solid fa-plus"></i>
            </button>
        </div>
        
        <div class="bg-white p-6 rounded-xl shadow-lg flex justify-between items-center">
            <div>
                <p class="text-gray-500 font-medium">Teachers</p>
                <h3 class="text-4xl font-bold text-gray-900 mt-1">60</h3>
            </div>
            <button class="bg-purple-100 text-purple-600 w-12 h-12 rounded-full text-xl hover:bg-purple-200 transition-all">
                <i class="fa-solid fa-plus"></i>
            </button>
        </div>
        
        <div class="bg-white p-6 rounded-xl shadow-lg flex justify-between items-center">
            <div>
                <p class="text-gray-500 font-medium">Employee</p>
                <h3 class="text-4xl font-bold text-gray-900 mt-1">100</h3>
            </div>
            <button class="bg-blue-100 text-blue-600 w-12 h-12 rounded-full text-xl hover:bg-blue-200 transition-all">
                <i class="fa-solid fa-plus"></i>
            </button>
        </div>
        
        <div class="bg-white p-6 rounded-xl shadow-lg flex justify-between items-center">
            <div>
                <p class="text-gray-500 font-medium">Class</p>
                <h3 class="text-4xl font-bold text-gray-900 mt-1">10</h3>
            </div>
            <button class="bg-orange-100 text-orange-600 w-12 h-12 rounded-full text-xl hover:bg-orange-200 transition-all">
                <i class="fa-solid fa-plus"></i>
            </button>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-1 flex flex-col gap-6">
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <h4 class="text-lg font-semibold text-gray-800 mb-4">Students</h4>
                {{-- CATATAN: Chart dibuat dgn JS. Ini placeholder gambar statis. --}}
                <div id="chartSiswaDonut" class="w-full h-48 flex items-center justify-center">
                    <img src="https://i.imgur.com/1Gf7gT1.png" alt="Student Chart" class="h-48">
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <h4 class="text-lg font-semibold text-gray-800 mb-4">Total Attendance</h4>
                <div id="chartAbsensiDonut" class="w-full h-48 flex items-center justify-center">
                    <img src="https://i.imgur.com/t4pY11N.png" alt="Attendance Chart" class="h-48">
                </div>
            </div>
        </div>
        
        <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-lg">
            <h4 class="text-lg font-semibold text-gray-800 mb-4">Weekly Absence Report</h4>
            <div id="chartAbsensiMingguan" class="w-full h-96 min-h-[400px]">
                {{-- CATATAN: Chart dibuat dgn JS. Ini placeholder gambar statis. --}}
                <img src="https://i.imgur.com/7gXvSjY.png" alt="Weekly Absence Chart" class="w-full h-full object-contain">
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    @endpush