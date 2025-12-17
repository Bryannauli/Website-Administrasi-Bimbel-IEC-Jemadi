<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment Form Print</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0; padding: 0;
        }

        @media print {
            @page {
                size: landscape;
                margin: 5mm; 
            }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
            table, th, td { border-color: black !important; }
            
            /* Paksa pindah halaman setelah setiap wrapper */
            .page-break {
                page-break-after: always;
                break-after: page;
            }
        }

        .assessment-table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid black;
            font-size: 9px;
        }
        .assessment-table th, .assessment-table td {
            border: 1px solid black;
            padding: 2px 1px;
            text-align: center;
            height: 18px;
        }
        .header-gray { background-color: #e5e7eb; font-weight: bold; }
        .col-no { width: 25px; }
        .col-id { width: 50px; }
        
        /* Nama Siswa: Rata Kiri di tabel */
        .col-name { width: 180px; text-align: left !important; padding-left: 5px !important; }
        
        .col-score { width: 28px; }
        .col-ave { width: 30px; background-color: #f3f4f6; }
        .col-rank { width: 30px; }

        /* Wrapper per halaman */
        .print-wrapper {
            max-width: 297mm;
            margin: 0 auto;
            padding: 8mm;
            background: white;
        }
    </style>
</head>
<body class="bg-gray-100 print:bg-white">

    @php
        // Tentukan jumlah siswa per halaman (18 siswa agar sisa space aman)
        $chunkSize = 18; 
        $studentChunks = $students->chunk($chunkSize);
        
        if($studentChunks->isEmpty()) { $studentChunks = collect([collect([])]); }
    @endphp

    @foreach($studentChunks as $pageIndex => $chunk)
        <div class="print-wrapper {{ !$loop->last ? 'page-break' : '' }}">
            
            {{-- 1. JUDUL FORM --}}
            <div class="text-center font-bold text-xl uppercase mb-4 tracking-wide">
                Assessment Form - PL, Level, Step Classes
                <span class="text-sm normal-case font-normal ml-2">(Page {{ $pageIndex + 1 }} of {{ $studentChunks->count() }})</span>
            </div>

            {{-- 2. HEADER INFO (DETAIL KELAS) --}}
            <div class="flex justify-between items-start mb-5 font-bold text-[11px] leading-tight px-1">
                {{-- KIRI --}}
                <div class="w-[45%] grid grid-cols-[130px_1fr] gap-y-1">
                    <div>MONTH</div>
                    <div class="border-b border-black uppercase">{{ $header->month }}</div>
                    <div>FORM TEACHER</div>
                    <div class="border-b border-black">{{ $header->form_teacher }}</div>
                    <div>OTHER TEACHER(S)</div>
                    <div class="border-b border-black">{{ $header->other_teacher }}</div>
                </div>

                {{-- KANAN --}}
                <div class="w-[35%] grid grid-cols-[100px_1fr] gap-y-1">
                    <div>CLASS</div>
                    <div class="border-b border-black uppercase">{{ $header->class_name }}</div>
                    <div>CLASS TIME</div>
                    <div class="border-b border-black">{{ $header->class_time }}</div>
                    <div>CLASS DAYS</div>
                    <div class="border-b border-black">{{ $header->class_days }}</div>
                </div>
            </div>

            {{-- 3. TABEL PENILAIAN --}}
            <table class="assessment-table">
                <thead>
                    <tr class="header-gray">
                        <th rowspan="2" class="col-no">No.</th>
                        <th rowspan="2" class="col-id">STUDENTS<br>NO.</th>
                        <th rowspan="2" class="w-[180px] !text-center !pl-0">Name</th>

                        @foreach($subjects as $subj)
                            <th colspan="3" class="uppercase">{{ $subj }}</th>
                        @endforeach

                        <th rowspan="2" class="col-score">TOTAL<br>AVE</th>
                        <th rowspan="2" class="col-rank">RANK</th>
                        <th rowspan="2" class="col-score">AT</th>
                    </tr>
                    <tr class="header-gray text-[8px]">
                        @foreach($subjects as $subj)
                            <th class="col-score">Mid</th>
                            <th class="col-score">Final</th>
                            <th class="col-ave">AVE</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($chunk as $s)
                        @php
                            $isDeleted = ($s->deleted_at != null);
                            $isInactive = ($s->is_active == 0);
                            $hasStatusIssue = $isDeleted || $isInactive;

                            $rowClass = $hasStatusIssue ? 'text-red-600 bg-red-50' : '';
                            $nameStyle = $hasStatusIssue ? 'text-red-600 line-through decoration-red-400' : 'font-semibold';
                        @endphp

                        <tr class="{{ $rowClass }}">
                            <td class="font-bold {{ $hasStatusIssue ? 'bg-red-50' : '' }}">{{ $s->no }}</td>
                            <td class="font-mono {{ $hasStatusIssue ? 'text-red-600' : '' }}">{{ $s->student_number }}</td>
                            
                            <td class="col-name uppercase truncate {{ $nameStyle }}">
                                {{ $s->name }}
                                @if($hasStatusIssue)
                                    <span class="ml-1 text-[8px] border border-red-500 rounded px-1 no-underline inline-block align-middle font-bold" style="text-decoration: none !important;">
                                        {{ $isDeleted ? 'DEL' : 'OUT' }}
                                    </span>
                                @endif
                            </td>

                            @foreach($subjects as $subj)
                                <td>{{ $s->marks[$subj]->mid }}</td>
                                <td>{{ $s->marks[$subj]->final }}</td>
                                <td class="{{ $hasStatusIssue ? 'bg-red-100/50' : 'bg-gray-50' }} font-bold">{{ $s->marks[$subj]->ave }}</td>
                            @endforeach

                            <td class="font-bold border-l-2 border-black {{ $hasStatusIssue ? 'bg-red-100' : 'bg-gray-100' }}">{{ $s->total_ave }}</td>
                            <td class="font-bold">{{ $s->rank }}</td>
                            <td>{{ $s->at }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach

    {{-- TOMBOL PRINT --}}
    <div class="no-print fixed bottom-10 right-10 flex gap-4">
        <a href="{{ url()->previous() }}" class="bg-gray-500 text-white px-6 py-3 rounded-full shadow-lg hover:bg-gray-600 font-bold transition flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Back
        </a>
        <button onclick="window.print()" class="bg-blue-600 text-white px-6 py-3 rounded-full shadow-lg hover:bg-blue-700 font-bold transition flex items-center gap-2">
            <i class="fas fa-print"></i> Print Form
        </button>
    </div>

</body>
</html>