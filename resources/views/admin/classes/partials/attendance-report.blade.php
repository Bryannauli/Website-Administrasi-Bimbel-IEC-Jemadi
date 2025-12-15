<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Matrix Report</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        /* CSS KHUSUS PRINT */
        @media print {
            @page {
                size: landscape;
                margin: 5mm;
            }
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                background-color: white;
            }
            .no-print {
                display: none !important;
            }
            /* Paksa border hitam pekat saat print */
            table, th, td {
                border-color: #000 !important; 
            }
        }

        /* Styling Table ala Laporan Fisik */
        .report-table {
            width: 100%;
            border-collapse: collapse;
            font-family: 'Times New Roman', Times, serif;
        }
        
        .report-table th, 
        .report-table td {
            border: 1px solid #000;
            padding: 4px;
            font-size: 11px;
        }

        /* Header Grid */
        .info-box {
            border: 1px solid #000;
            padding: 5px;
            font-size: 11px;
            font-family: 'Arial', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-200 p-4 font-sans">

    {{-- WRAPPER KERTAS A4 LANDSCAPE --}}
    <div class="max-w-[297mm] mx-auto bg-white shadow-lg p-6 min-h-[210mm] relative">

        {{-- 1. HEADER JUDUL --}}
        <div class="border border-black mb-1">
            <h1 class="text-center font-bold text-lg uppercase py-1 bg-gray-200 border-b border-black">
                Attendance Roll for Class: {{ $class->name ?? 'CLASS NAME' }}
            </h1>
        </div>

        {{-- 2. INFORMASI KELAS & LEGEND --}}
        <div class="flex flex-row mb-2 gap-2">
            
            {{-- KOTAK KIRI: Marking Guide (Legend) --}}
            <div class="info-box w-1/3">
                <div class="font-bold underline mb-1">Marking Guide:</div>
                <div class="grid grid-cols-1 gap-0.5 text-[10px]">
                    <div><strong>/</strong> = Present</div>
                    <div><strong>O</strong> = Absent</div>
                    <div><strong>L</strong> = Late</div>
                    <div><strong>P</strong> = Permission</div>
                    <div><strong>S</strong> = Sick</div>
                </div>
            </div>

            {{-- KOTAK KANAN: Class Info --}}
            {{-- PERUBAHAN: Menggunakan Grid 2 Kolom (Kiri & Kanan) --}}
            <div class="info-box w-2/3">
                <div class="grid grid-cols-2 gap-x-6 h-full">
                    
                    {{-- KOLOM KIRI (Info Kelas) --}}
                    <div class="flex flex-col gap-1">
                        <div class="flex items-end">
                            <span class="w-24 font-bold shrink-0">TERM A/B:</span>
                            <span class="border-b border-black border-dotted flex-1 truncate">{{ $class->term ?? date('Y') }}</span>
                        </div>
                        <div class="flex items-end">
                            <span class="w-24 font-bold shrink-0">CLASS:</span>
                            <span class="border-b border-black border-dotted flex-1 truncate">{{ $class->name ?? '-' }}</span>
                        </div>
                        <div class="flex items-end">
                            <span class="w-24 font-bold shrink-0">CLASS TIMES:</span>
                            <span class="border-b border-black border-dotted flex-1 truncate">{{ $class->times ?? '-' }}</span>
                        </div>
                    </div>

                    {{-- KOLOM KANAN (Info Guru) --}}
                    <div class="flex flex-col gap-1">
                        <div class="flex items-end">
                            <span class="w-28 font-bold shrink-0">FORM TEACHER:</span>
                            <span class="border-b border-black border-dotted flex-1 truncate">{{ $teacherName ?? '-' }}</span>
                        </div>
                        <div class="flex items-end">
                            <span class="w-28 font-bold shrink-0">LOCAL TEACHER:</span>
                            <span class="border-b border-black border-dotted flex-1 truncate">{{ $localTeacher ?? '-' }}</span>
                        </div>
                        <div class="flex items-end">
                            <span class="w-28 font-bold shrink-0">TOTAL SESSIONS:</span>
                            <span class="border-b border-black border-dotted flex-1 truncate">{{ count($teachingLogs) }}</span>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- 3. TABEL UTAMA --}}
        <table class="report-table">
            <thead>
                <tr class="bg-gray-100 text-center">
                    <th rowspan="2" class="w-8">No</th>
                    <th rowspan="2" class="w-20">Student's ID No</th>
                    <th rowspan="2" class="w-48 text-left px-2">Student Name</th>
                    <th colspan="{{ count($teachingLogs) }}" class="h-6">Meeting No / Date</th>
                    <th rowspan="2" class="w-10 text-[10px]">Total<br>Pres</th>
                    <th rowspan="2" class="w-10 text-[10px]">%</th>
                </tr>
                <tr class="bg-gray-50">
                    @foreach($teachingLogs as $index => $session)
                        <th class="w-8 text-center bg-white align-bottom pb-1 relative group h-24 whitespace-nowrap">
                            <div class="absolute top-1 left-0 right-0 text-[9px] font-bold bg-gray-100 border-b border-gray-300">
                                {{ $index + 1 }}
                            </div>
                            <div class="writing-vertical-lr transform rotate-180 h-16 w-full flex items-center justify-center text-[10px]">
                                {{ \Carbon\Carbon::parse($session->date)->format('d/m') }}
                            </div>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($studentStats as $index => $stat)
                    <tr>
                        <td class="text-center bg-gray-50 font-bold">{{ $index + 1 }}</td>
                        <td class="text-center font-mono text-[10px]">{{ $stat->student_number }}</td>
                        <td class="uppercase font-semibold text-[10px] truncate max-w-[150px]">
                            {{ $stat->name }}
                        </td>
                        @foreach($teachingLogs as $session)
                            @php
                                $status = $attendanceMatrix[$stat->student_id][$session->session_id] ?? '-';
                                $symbol = match($status) {
                                    'present' => '/',
                                    'late' => 'L',
                                    'sick' => 'S',
                                    'permission' => 'P',
                                    'absent' => 'O',
                                    default => ''
                                };
                            @endphp
                            <td class="text-center text-sm font-bold p-0 h-6 align-middle">
                                {{ $symbol }}
                            </td>
                        @endforeach
                        @php
                            $presentCount = 0;
                            foreach($teachingLogs as $s) {
                                $st = $attendanceMatrix[$stat->student_id][$s->session_id] ?? '-';
                                if($st == 'present' || $st == 'late') $presentCount++;
                            }
                        @endphp
                        <td class="text-center bg-gray-50">{{ $presentCount }}</td>
                        <td class="text-center font-bold text-[10px]">
                            {{ round($stat->percentage) }}%
                        </td>
                    </tr>
                @endforeach
                
                {{-- Baris Kosong Pelengkap --}}
                @for($i = 0; $i < (20 - count($studentStats)); $i++)
                    <tr>
                        <td class="text-center">&nbsp;</td>
                        <td></td>
                        <td></td>
                        @foreach($teachingLogs as $session) <td></td> @endforeach
                        <td></td>
                        <td></td>
                    </tr>
                @endfor
            </tbody>
        </table>
        
        {{-- FOOTER TOMBOL --}}
        <div class="no-print fixed bottom-10 right-10 flex gap-4">
            <a href="{{ url()->previous() }}" class="bg-gray-500 text-white px-6 py-3 rounded-full shadow-lg hover:bg-gray-600 font-bold">
                Back
            </a>
            <button onclick="window.print()" class="bg-blue-600 text-white px-6 py-3 rounded-full shadow-lg hover:bg-blue-700 font-bold">
                <i class="fas fa-print mr-2"></i> Print Report
            </button>
        </div>
    </div>

    <style>
        .writing-vertical-lr {
            writing-mode: vertical-lr;
        }
    </style>
</body>
</html>