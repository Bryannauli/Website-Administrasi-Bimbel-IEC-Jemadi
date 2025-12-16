<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment Form Print</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        /* FONT SEPERTI MESIN TIK / FORMULIR LAMA */
        body {
            font-family: Arial, Helvetica, sans-serif;
        }

        @media print {
            @page {
                size: landscape; /* WAJIB LANDSCAPE */
                margin: 5mm;     /* Margin tipis agar muat */
            }
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .no-print { display: none !important; }
            
            /* Paksa border hitam pekat */
            table, th, td { border-color: black !important; }
        }

        /* Styling Table */
        .assessment-table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid black;
            font-size: 9px; /* Font diperkecil agar muat 6 mapel */
        }

        .assessment-table th, 
        .assessment-table td {
            border: 1px solid black;
            padding: 2px 1px; /* Padding sangat tipis */
            text-align: center;
            height: 18px; /* Tinggi baris dipaksa fix */
        }

        /* Kolom Header Mapel */
        .header-gray { background-color: #e5e7eb; font-weight: bold; }
        
        /* Lebar Kolom Spesifik */
        .col-no { width: 25px; }
        .col-id { width: 50px; }
        .col-name { width: 180px; text-align: left !important; padding-left: 5px !important; }
        .col-score { width: 28px; } /* Lebar kolom nilai */
        .col-ave { width: 30px; background-color: #f3f4f6; } /* Kolom rata-rata sedikit gelap */
        .col-rank { width: 30px; }

        /* Input Underline Style */
        .input-line {
            border-bottom: 1px solid black;
            padding-left: 5px;
            display: inline-block;
        }
    </style>
</head>
<body class="bg-white p-4">

    <div class="max-w-[297mm] mx-auto min-h-[210mm] relative">

        {{-- 1. JUDUL FORM --}}
        <div class="text-center font-bold text-xl uppercase mb-4 tracking-wide">
            Assessment Form - PL, Level, Step Classes
        </div>

        {{-- 2. HEADER INFO (Kiri & Kanan) --}}
        <div class="flex justify-between items-start mb-2 font-bold text-[11px] leading-tight px-1">
            
            {{-- KIRI --}}
            <div class="w-[45%] grid grid-cols-[130px_1fr] gap-y-1">
                <div>MONTH</div>
                <div class="border-b border-black">{{ $header->month }}</div>

                <div>FORM TEACHER</div>
                <div class="border-b border-black">{{ $header->form_teacher }}</div>

                <div>OTHER TEACHER(S)</div>
                <div class="border-b border-black">{{ $header->other_teacher }}</div>
            </div>

            {{-- KANAN --}}
            <div class="w-[35%] grid grid-cols-[100px_1fr] gap-y-1">
                <div>CLASS</div>
                <div class="border-b border-black">{{ $header->class_name }}</div>

                <div>CLASS TIME</div>
                <div class="border-b border-black">{{ $header->class_time }}</div>

                <div>CLASS DAYS</div>
                <div class="border-b border-black">{{ $header->class_days }}</div>
            </div>
        </div>

        {{-- 3. TABEL PENILAIAN GABUNGAN --}}
        <table class="assessment-table">
            <thead>
                {{-- Row 1: Judul Mapel --}}
                <tr class="header-gray">
                    <th rowspan="2" class="col-no">No.</th>
                    <th rowspan="2" class="col-id">STUDENTS<br>NO.</th>
                    <th rowspan="2" class="col-name text-center !pl-0">Name</th>

                    {{-- LOOP SUBJECT HEADERS --}}
                    @foreach($subjects as $subj)
                        <th colspan="3" class="uppercase">{{ $subj }}</th>
                    @endforeach

                    <th rowspan="2" class="col-score">TOTAL<br>AVE</th>
                    <th rowspan="2" class="col-rank">RANK</th>
                    <th rowspan="2" class="col-score">AT</th> {{-- Attendance / Remarks --}}
                </tr>

                {{-- Row 2: Mid/Final/Ave --}}
                <tr class="header-gray text-[8px]">
                    @foreach($subjects as $subj)
                        <th class="col-score">Mid</th>
                        <th class="col-score">Final</th>
                        <th class="col-ave">AVE</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                {{-- ISI DATA SISWA --}}
                @foreach($students as $s)
                    <tr>
                        <td>{{ $s->no }}</td>
                        <td>{{ $s->student_number }}</td>
                        <td class="col-name uppercase truncate">{{ $s->name }}</td>

                        {{-- Loop Nilai Mapel --}}
                        @foreach($subjects as $subj)
                            <td>{{ $s->marks[$subj]->mid }}</td>
                            <td>{{ $s->marks[$subj]->final }}</td>
                            <td class="bg-gray-50 font-bold">{{ $s->marks[$subj]->ave }}</td>
                        @endforeach

                        {{-- Total & Rank --}}
                        <td class="font-bold border-l-2 border-black">{{ $s->total_ave }}</td>
                        <td>{{ $s->rank }}</td>
                        <td>{{ $s->at }}</td>
                    </tr>
                @endforeach

                {{-- BARIS KOSONG (FILLER) --}}
                {{-- Kita isi sisa kertas agar terlihat penuh --}}
                @for($i = 0; $i < (20 - count($students)); $i++)
                    <tr>
                        <td>{{ count($students) + $i + 1 }}</td>
                        <td></td>
                        <td></td>
                        @foreach($subjects as $subj)
                            <td></td><td></td><td class="bg-gray-50"></td>
                        @endforeach
                        <td class="border-l-2 border-black"></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endfor
            </tbody>
        </table>

        {{-- TOMBOL PRINT --}}
        <div class="no-print fixed bottom-10 right-10 flex gap-4">
            <a href="{{ url()->previous() }}" class="bg-gray-500 text-white px-6 py-3 rounded-full shadow-lg hover:bg-gray-600 font-bold">Back</a>
            <button onclick="window.print()" class="bg-blue-600 text-white px-6 py-3 rounded-full shadow-lg hover:bg-blue-700 font-bold">
                <i class="fas fa-print mr-2"></i> Print Form
            </button>
        </div>

    </div>
</body>
</html>