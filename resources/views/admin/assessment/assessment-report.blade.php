<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment Form Report</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        /* CSS KHUSUS PRINT */
        @media print {
            @page {
                size: A4 landscape;
                margin: 0; /* Nol-kan margin kertas browser */
            }
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                background-color: white !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            
            /* Sembunyikan header/footer default browser (URL, Tanggal, Title) */
            header, footer, aside, nav, form {
                display: none !important;
            }

            /* Reset container agar pas layar */
            .print-container {
                width: 100% !important;
                max-width: 100% !important;
                box-shadow: none !important;
                margin: 0 !important;
                padding: 10mm !important; /* Kita atur margin kertas manual di sini (1cm) */
                min-height: auto !important; /* Jangan paksa tinggi */
                border: none !important;
            }

            /* Pastikan border tabel hitam pekat */
            table, th, td, .border-black {
                border-color: #000 !important;
            }
        }

        /* Styling Table ala Laporan Fisik */
        .report-table {
            width: 100%;
            border-collapse: collapse;
            font-family: 'Times New Roman', Times, serif; /* Font formal */
        }
        
        .report-table th, 
        .report-table td {
            border: 1px solid #000;
            padding: 4px;
            font-size: 12px;
            vertical-align: middle;
        }

        /* Garis titik-titik untuk isian manual header */
        .dotted-line {
            border-bottom: 1px solid #000;
            display: inline-block;
            width: 100%;
            height: 18px; /* Tinggi baris */
        }
    </style>
</head>
<body class="bg-gray-200 p-8 font-sans">

    {{-- WRAPPER KERTAS A4 LANDSCAPE --}}
    <div class="max-w-[297mm] mx-auto bg-white shadow-lg p-8 min-h-[210mm] relative">

        {{-- 1. HEADER JUDUL --}}
        <div class="text-center mb-6">
            <h1 class="font-bold text-2xl uppercase tracking-wide" style="font-family: Arial, sans-serif;">
                ASSESSMENT FORM - PL, LEVEL, STEP CLASSES
            </h1>
        </div>

        {{-- 2. INFORMASI KELAS (HEADER FORM) --}}
        <div class="grid grid-cols-2 gap-12 mb-4 font-bold text-sm" style="font-family: Arial, sans-serif;">
            
            {{-- KOLOM KIRI --}}
            <div class="flex flex-col gap-2">
                <div class="flex items-end">
                    <span class="w-32 shrink-0">MONTH</span>
                    <span class="border-b border-black flex-1 pl-2">
                        {{ strtoupper($headerData->start_month ?? '') }} - {{ strtoupper($headerData->end_month ?? '') }} {{ $headerData->academic_year ?? '' }}
                    </span>
                </div>
                <div class="flex items-end">
                    <span class="w-32 shrink-0">FORM TEACHER</span>
                    <span class="border-b border-black flex-1 pl-2">
                        {{ $headerData->form_teacher ?? '-' }}
                    </span>
                </div>
                <div class="flex items-end">
                    <span class="w-32 shrink-0">OTHER TEACHER </span>
                    <span class="border-b border-black flex-1 pl-2">
                        {{ $headerData->other_teacher ?? '-' }}
                    </span>
                </div>
            </div>

            {{-- KOLOM KANAN --}}
            <div class="flex flex-col gap-2">
                <div class="flex items-end">
                    <span class="w-24 shrink-0">CLASS</span>
                    <span class="border-b border-black flex-1 pl-2">
                        {{ $headerData->class_name ?? '' }}
                    </span>
                </div>
                <div class="flex items-end">
                    <span class="w-24 shrink-0">CLASS TIME</span>
                    <span class="border-b border-black flex-1 pl-2 font-bold">
                        {{ \Carbon\Carbon::parse($headerData->start_time)->format('H:i') }} - 
                        {{ \Carbon\Carbon::parse($headerData->end_time)->format('H:i') }}
                    </span>
                </div>
                <div class="flex items-end">
                    <span class="w-24 shrink-0">CLASS DAYS</span>
                    <span class="border-b border-black flex-1 pl-2 font-bold uppercase">
                        {{ $headerData->class_days ?? '-' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- 3. TABEL UTAMA --}}
        <table class="report-table mt-4">
            <thead>
                {{-- Baris Header 1 --}}
                <tr class="bg-gray-200 text-center font-bold font-sans">
                    <th rowspan="2" class="w-10">No.</th>
                    <th rowspan="2" class="w-32">Student No.</th>
                    <th rowspan="2" class="w-64">Name</th>
                    <th colspan="7" class="h-8 uppercase tracking-wider border-b-2 border-black">
                        {{ strtoupper($headerData->assessment_type ?? 'ASSESSMENT') }}                    
                    </th>
                </tr>
                {{-- Baris Header 2 (Sub-kolom FINAL) --}}
                <tr class="bg-gray-100 text-center font-semibold text-[11px] font-sans">
                    <th class="w-20">Vocabulary</th>
                    <th class="w-20">Grammar</th>
                    <th class="w-20">Listening</th>
                    <th class="w-20">Speaking</th>
                    <th class="w-20">Reading</th>
                    <th class="w-20">Spelling</th>
                    <th class="w-24 bg-gray-200">AVERAGE</th>
                </tr>
            </thead>
            <tbody>
                {{-- 1. Looping Data Siswa yang Ada Nilainya --}}
                @foreach($students as $index => $student)
                <tr class="h-8">
                    <td class="text-center font-bold bg-gray-50">{{ $index + 1 }}</td>
                    <td class="text-center font-mono">{{ $student->student_number }}</td>
                    <td class="pl-2 uppercase truncate max-w-[200px]">{{ $student->student_name }}</td>
                    
                    {{-- Kolom Nilai (Ambil dari View v_student_grades) --}}
                    <td class="text-center">{{ $student->vocabulary ?? '' }}</td>
                    <td class="text-center">{{ $student->grammar ?? '' }}</td>
                    <td class="text-center">{{ $student->listening ?? '' }}</td>
                    <td class="text-center">{{ $student->speaking ?? '' }}</td>
                    <td class="text-center">{{ $student->reading ?? '' }}</td>
                    <td class="text-center">{{ $student->spelling ?? '' }}</td>
                    
                    {{-- Kolom Average / Final Score --}}
                    <td class="text-center font-bold bg-gray-50">
                        {{ $student->final_score ? round($student->final_score) : '' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
    {{-- FOOTER TOMBOL (Tidak ikut ter-print) --}}
        <div class="no-print fixed bottom-10 right-10 flex gap-4">
            
            {{-- TOMBOL CANCEL / BACK --}}
            <a href="{{ url()->previous() }}" class="bg-gray-500 text-white px-6 py-3 rounded-full shadow-lg hover:bg-gray-600 font-bold transition flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Cancel
            </a>

            {{-- TOMBOL PRINT --}}
            <button onclick="window.print()" class="bg-blue-600 text-white px-6 py-3 rounded-full shadow-lg hover:bg-blue-700 font-bold transition flex items-center gap-2">
                <i class="fas fa-print"></i> Print Form
            </button>

    </div>

</body>
</html>