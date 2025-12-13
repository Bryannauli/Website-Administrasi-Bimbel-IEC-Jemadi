<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClassModel;
use App\Models\ClassSession; // ✅ GANTI: AttendanceSession -> ClassSession
use App\Models\AttendanceRecord;
// use App\Models\TeacherAttendanceRecord; // ❌ HAPUS: Model ini tidak lagi digunakan
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tentukan Rentang Waktu (1 Bulan ke belakang s/d Hari Ini)
        $startDate = Carbon::now()->subMonth();
        $endDate   = Carbon::now(); 

        // 2. Ambil Kelas Aktif beserta jadwal dan siswa aktif
        $classes = ClassModel::with(['schedules', 'students' => function($q) {
            $q->where('is_active', true);
        }])->where('is_active', true)->get();

        $this->command->info('Seeding attendance from ' . $startDate->format('Y-m-d') . ' to ' . $endDate->format('Y-m-d'));

        DB::beginTransaction();

        try {
            // 3. Loop Tanggal
            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                
                $dayName = $date->format('l'); // Monday, Tuesday...
                $isToday = $date->isToday();
                $currentTime = Carbon::now()->format('H:i:s');

                foreach ($classes as $class) {
                    
                    // A. CEK JADWAL HARI
                    $schedule = $class->schedules->firstWhere('day_of_week', $dayName);

                    if ($schedule) {

                        // --- CEK JAM ---
                        if ($isToday && $class->start_time > $currentTime) {
                            continue; 
                        }

                        // **LOGIKA BARU (B) - Menambahkan 'comment' saat membuat/memperbarui Sesi**
                        $comment = 'Teaching material for ' . $dayName . ' (' . ucfirst($schedule->teacher_type) . ' Session)';
                        
                        // D. TENTUKAN GURU YANG HADIR SEBELUM MEMBUAT SESI (PINDAH DARI BAWAH)
                        $teacherId = null;

                        // Cek siapa yang bertugas hari ini berdasarkan jadwal
                        if ($schedule->teacher_type === 'form') {
                            $teacherId = $class->form_teacher_id;
                        } elseif ($schedule->teacher_type === 'local') {
                            $teacherId = $class->local_teacher_id;
                        }

                        // Fallback:
                        if (!$teacherId) {
                            $teacherId = $class->form_teacher_id ?? $class->local_teacher_id;
                        }

                        // B. BUAT SESI (Menggunakan ClassSession)
                        $session = ClassSession::firstOrCreate( // ✅ GANTI: AttendanceSession -> ClassSession
                            [
                                'class_id' => $class->id,
                                'date'     => $date->format('Y-m-d'),
                            ],
                            // TAMBAHKAN COMMENT DAN teacher_id DI SINI
                            [
                                'comment' => $comment,
                                'teacher_id' => $teacherId, // ✅ teacher_id dipindahkan ke ClassSession
                            ]
                        );

                        // Jika $session sudah ada sebelumnya (firstOrCreate hanya mengambil yang lama), 
                        // kita pastikan teacher_id dan comment di-update (meski di seeder ini tidak mutlak)
                        if ($session->wasRecentlyCreated === false) {
                            $session->update([
                                'comment' => $comment,
                                'teacher_id' => $teacherId,
                            ]);
                        }


                        // C. BUAT ABSENSI SISWA (Perlu disesuaikan foreign key)
                        foreach ($class->students as $student) {
                            $rand = rand(1, 100);
                            if ($rand <= 85) $status = 'present';
                            elseif ($rand <= 90) $status = 'late';
                            elseif ($rand <= 95) $status = 'permission';
                            elseif ($rand <= 98) $status = 'sick';
                            else $status = 'absent';

                            AttendanceRecord::updateOrCreate(
                                [
                                    // Foreign key sekarang adalah 'class_session_id' di AttendanceRecord
                                    // Kita berasumsi model AttendanceRecord.php sudah disetting
                                    'class_session_id' => $session->id, // ✅ GANTI: attendance_session_id -> class_session_id
                                    'student_id'            => $student->id
                                ],
                                [
                                    'status' => $status
                                ]
                            );
                        }

                        // D. LOGIKA ABSENSI GURU SEKARANG DIHAPUS DARI SINI
                        // Karena sudah dipindahkan ke ClassSession::firstOrCreate di atas.
                        /*
                        if ($teacherId) {
                            TeacherAttendanceRecord::updateOrCreate( // ❌ BARIS INI DIHAPUS
                                // ...
                            );
                        }
                        */
                    }
                }
            }

            DB::commit();
            $this->command->info('Attendance data seeded successfully (Synced with Teacher Schedule)!');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error: ' . $e->getMessage());
        }
    }
}