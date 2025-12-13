<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClassModel;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use App\Models\TeacherAttendanceRecord;
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
                    
                    // A. CEK JADWAL HARI (DIPERBARUI)
                    // Kita cari objek schedule spesifik untuk hari ini agar bisa baca 'teacher_type'
                    $schedule = $class->schedules->firstWhere('day_of_week', $dayName);

                    if ($schedule) {

                        // --- CEK JAM ---
                        // Jika hari ini, tapi jam kelas belum mulai, skip.
                        if ($isToday && $class->start_time > $currentTime) {
                            continue; 
                        }

                        // B. BUAT SESI
                        $session = AttendanceSession::firstOrCreate([
                            'class_id' => $class->id,
                            'date'     => $date->format('Y-m-d'),
                        ]);

                        // C. BUAT ABSENSI SISWA (Tetap sama)
                        foreach ($class->students as $student) {
                            $rand = rand(1, 100);
                            if ($rand <= 85) $status = 'present';
                            elseif ($rand <= 90) $status = 'late';
                            elseif ($rand <= 95) $status = 'permission';
                            elseif ($rand <= 98) $status = 'sick';
                            else $status = 'absent';

                            AttendanceRecord::updateOrCreate(
                                [
                                    'attendance_session_id' => $session->id,
                                    'student_id'            => $student->id
                                ],
                                [
                                    'status' => $status
                                ]
                            );
                        }

                        // D. BUAT ABSENSI GURU (LOGIKA BARU SESUAI JADWAL)
                        $teacherId = null;

                        // Cek siapa yang bertugas hari ini berdasarkan jadwal
                        if ($schedule->teacher_type === 'form') {
                            $teacherId = $class->form_teacher_id;
                        } elseif ($schedule->teacher_type === 'local') {
                            $teacherId = $class->local_teacher_id;
                        }

                        // Fallback: Jika guru yang ditugaskan kosong (misal local belum diassign),
                        // coba ambil guru yang tersedia saja agar seeder tidak error/kosong.
                        if (!$teacherId) {
                            $teacherId = $class->form_teacher_id ?? $class->local_teacher_id;
                        }

                        if ($teacherId) {
                            TeacherAttendanceRecord::updateOrCreate(
                                [
                                    'attendance_session_id' => $session->id,
                                    'teacher_id'            => $teacherId
                                ],
                                [
                                    'status'  => 'present',
                                    'comment' => 'Teaching material for ' . $dayName . ' (' . ucfirst($schedule->teacher_type) . ' Session)',
                                ]
                            );
                        }
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