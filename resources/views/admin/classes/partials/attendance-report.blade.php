<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Report</title>
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        /* CSS KHUSUS PRINT */
        @media print {
            @page {
                size: landscape;
                margin: 0;
            }
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                background-color: white !important;
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .page-break {
                break-after: page;
                page-break-after: always;
            }
            .print-wrapper {
                width: 297mm !important;
                height: 210mm !important;
                padding: 10mm !important; 
                margin: 0 !important;
                box-shadow: none !important;
                position: relative;
                overflow: hidden;
                display: block;
                page-break-inside: avoid;
            }
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            font-family: 'Times New Roman', Times, serif;
            font-size: 10px;
            table-layout: fixed; 
        }
        .report-table th, .report-table td {
            border: 1px solid #000;
            padding: 2px;
            overflow: hidden;
            white-space: nowrap; 
        }
        .info-box {
            border: 1px solid #000;
            padding: 6px;
            font-size: 10px;
            font-family: 'Arial', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-200 p-4 font-sans print:p-0 print:bg-white">

    @php
        $chunkSize = 18; 
        $sessionChunks = $teachingLogs->chunk($chunkSize);
        
        if($sessionChunks->isEmpty()) {
            $sessionChunks = collect([collect([])]);
        }
    @endphp

    @foreach($sessionChunks as $pageIndex => $chunkedSessions)
        
        @php
            $filledCount = $chunkedSessions->count();
            $emptySlots = $chunkSize - $filledCount;
            $startNumber = ($pageIndex * $chunkSize) + 1; 
        @endphp
    
        <div class="print-wrapper max-w-[297mm] mx-auto bg-white shadow-lg p-6 min-h-[210mm] relative mb-8 print:mb-0 {{ !$loop->last ? 'page-break' : '' }}">

            {{-- 1. HEADER JUDUL --}}
            <div class="border border-black mb-1">
                <h1 class="text-center font-bold text-lg uppercase py-1 bg-gray-200 border-b border-black">
                    ATTENDANCE FORM - PL, LEVEL, STEP CLASSES
                    <span class="text-xs font-normal ml-2">(Page {{ $pageIndex + 1 }} of {{ $sessionChunks->count() }})</span>
                </h1>
            </div>

            {{-- 2. INFORMASI KELAS --}}
            <div class="flex flex-row mb-2 gap-2">
                
                {{-- KIRI: Marking Guide --}}
                <div class="info-box w-1/4">
                    <div class="font-bold underline mb-1">Marking Guide:</div>
                    <div class="flex flex-col gap-0.5 text-[9px]">
                        <div><strong>/</strong> : Present</div>
                        <div><strong>O</strong> : Absent</div>
                        <div><strong>L</strong> : Late</div>
                        <div><strong>P</strong> : Permit</div>
                        <div><strong>S</strong> : Sick</div>
                    </div>
                </div>

                {{-- KANAN: Class Details --}}
                <div class="info-box w-3/4">
                    <div class="flex flex-col gap-1 h-full justify-center">
                        <div class="flex items-end">
                            <span class="w-28 font-bold text-[9px] shrink-0">TERM A/B:</span> 
                            <span class="border-b border-black border-dotted flex-1 text-[10px] uppercase">
                                {{ strtoupper($class->start_month) }} - {{ strtoupper($class->end_month) }} {{ $class->academic_year }}
                            </span>
                        </div>
                        <div class="flex items-end">
                            <span class="w-28 font-bold text-[9px] shrink-0">CLASS:</span> 
                            <span class="border-b border-black border-dotted flex-1 text-[10px] uppercase">
                                {{ $class->name }}
                            </span>
                        </div>
                        <div class="flex items-end">
                            <span class="w-28 font-bold text-[9px] shrink-0">CLASS TIMES:</span> 
                            <span class="border-b border-black border-dotted flex-1 text-[10px]">
                                {{ \Carbon\Carbon::parse($class->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($class->end_time)->format('g:i A') }}
                            </span>
                        </div>
                        <div class="flex items-end">
                            <span class="w-28 font-bold text-[9px] shrink-0">FORM TEACHER:</span> 
                            <span class="border-b border-black border-dotted flex-1 text-[10px]">
                                {{ ucwords(strtolower($teacherName)) }}
                            </span>
                        </div>
                        <div class="flex items-end">
                            <span class="w-28 font-bold text-[9px] shrink-0">LOCAL TEACHER:</span> 
                            <span class="border-b border-black border-dotted flex-1 text-[10px]">
                                {{ ucwords(strtolower($localTeacher)) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. TABEL --}}
            <table class="report-table">
                <thead>
                    <tr class="bg-gray-100 text-center">
                        <th rowspan="2" class="w-8">No</th>
                        
                        {{-- Header ID Student --}}
                        <th rowspan="2" class="w-16 whitespace-normal leading-tight px-1">
                            Student's<br>ID No:
                        </th>

                        <th rowspan="2" class="text-left px-2 w-[180px]">Student Name</th>
                        <th colspan="{{ $chunkSize }}" class="h-4">Meeting Date</th>
                        <th rowspan="2" class="w-7 text-[9px]">Pres</th>
                        <th rowspan="2" class="w-7 text-[9px]">%</th>
                    </tr>
                    <tr class="bg-gray-50">
                        @foreach($chunkedSessions as $index => $session)
                            <th class="w-7 text-center bg-white p-0.5 align-middle">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="text-[8px] font-bold bg-gray-100 border border-gray-300 w-full mb-0.5">
                                        {{ $startNumber + $index }}
                                    </div>
                                    <div class="text-[9px] leading-tight">
                                        {{ \Carbon\Carbon::parse($session->date)->format('d/m') }}
                                    </div>
                                </div>
                            </th>
                        @endforeach

                        @for($i = 0; $i < $emptySlots; $i++)
                            <th class="w-7 text-center bg-white p-0.5 align-middle">
                                <div class="flex flex-col items-center justify-center h-full">
                                    <div class="text-[8px] font-bold text-gray-400 bg-gray-50 border border-gray-200 w-full mb-0.5">
                                        {{ $startNumber + $filledCount + $i }}
                                    </div>
                                    <div class="text-[9px] text-gray-300 h-3">-</div>
                                </div>
                            </th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    @foreach($studentStats as $idx => $stat)
                        @php
                            $isInactive = (isset($stat->deleted_at) && $stat->deleted_at) || (isset($stat->is_active) && !$stat->is_active);
                            $rowClass = $isInactive ? 'text-red-600 bg-red-50' : '';
                        @endphp
                        <tr class="{{ $rowClass }}">
                            <td class="text-center font-bold">{{ $idx + 1 }}</td>
                            <td class="text-center font-mono text-[9px]">{{ $stat->student_number }}</td>
                            
                            {{-- KOLOM NAMA dengan Badge DEL / OUT --}}
                            <td class="truncate px-1 text-[10px] font-semibold">
                                {{ ucwords(strtolower($stat->student_name)) }}
                                @if($isInactive) 
                                    <span class="text-[8px] border border-red-500 rounded px-1 ml-1 font-bold">
                                        {{ $stat->deleted_at ? 'DEL' : 'OUT' }}
                                    </span> 
                                @endif
                            </td>

                            @foreach($chunkedSessions as $session)
                                @php
                                    $status = $attendanceMatrix[$stat->student_id][$session->session_id] ?? '-';
                                    $symbol = match($status) {
                                        'present' => '/', 'late' => 'L', 'sick' => 'S', 'permission' => 'P', 'absent' => 'O', default => ''
                                    };
                                @endphp
                                <td class="text-center font-bold h-5 align-middle">{{ $symbol }}</td>
                            @endforeach

                            @for($i = 0; $i < $emptySlots; $i++)
                                <td class="bg-gray-50/30"></td>
                            @endfor

                            <td class="text-center bg-gray-50">{{ $stat->total_present }}</td>
                            <td class="text-center font-bold">{{ $stat->attendance_percentage }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach

    <div class="no-print fixed bottom-8 right-8 flex gap-3">
        <a href="{{ url()->previous() }}" class="px-5 py-2 bg-gray-600 text-white rounded-full shadow hover:bg-gray-700 text-sm font-bold">Back</a>
        <button onclick="window.print()" class="px-5 py-2 bg-blue-600 text-white rounded-full shadow hover:bg-blue-700 text-sm font-bold">
            <i class="fas fa-print mr-2"></i> Print
        </button>
    </div>

</body>
</html>