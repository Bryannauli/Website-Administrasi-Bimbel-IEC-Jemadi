<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClassModel;
use App\Models\ClassSession; // GANTI: Menggunakan model ClassSession yang baru
use App\Models\AttendanceRecord;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
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

                        // --- CEK JAM (Skip jika jadwal kelas masih di masa depan hari ini) ---
                        if ($isToday && $class->start_time > $currentTime) {
                            continue; 
                        }

                        // **LOGIKA 1: TENTUKAN GURU DAN KOMENTAR**
                        $comment = 'Teaching material for ' . $dayName . ' (' . ucfirst($schedule->teacher_type) . ' Session)';
                        
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

                        // **LOGIKA 2: BUAT/UPDATE SESI (TERMASUK GURU YANG HADIR)**
                        $session = ClassSession::firstOrCreate(
                            [
                                'class_id' => $class->id,
                                'date'     => $date->format('Y-m-d'),
                            ],
                            [
                                'comment' => $comment,
                                'teacher_id' => $teacherId, // teacher_id dipindahkan ke ClassSession
                            ]
                        );

                        // Pastikan data di-update jika record sudah ada
                        if ($session->wasRecentlyCreated === false) {
                             $session->update([
                                'comment' => $comment,
                                'teacher_id' => $teacherId,
                            ]);
                        }


                        // **LOGIKA 3: BUAT ABSENSI SISWA**
                        foreach ($class->students as $student) {
                            $rand = rand(1, 100);
                            if ($rand <= 85) $status = 'present';
                            elseif ($rand <= 90) $status = 'late';
                            elseif ($rand <= 95) $status = 'permission';
                            elseif ($rand <= 98) $status = 'sick';
                            else $status = 'absent';

                            AttendanceRecord::updateOrCreate(
                                [
                                    // Foreign key sudah diubah menjadi 'class_session_id'
                                    'class_session_id' => $session->id, 
                                    'student_id'            => $student->id
                                ],
                                [
                                    'status' => $status
                                ]
                            );
                        }

                        // **LOGIKA ABSENSI GURU LAMA DIHAPUS TOTAL**
                    }
                }
            }

            DB::commit();
            $this->command->info('Attendance data seeded successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error: ' . $e->getMessage());
        }
    }
}