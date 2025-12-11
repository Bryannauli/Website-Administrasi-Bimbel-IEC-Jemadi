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
        $endDate   = Carbon::now(); // Hari ini

        // 2. Ambil Kelas Aktif
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
                    $hasSchedule = $class->schedules->contains('day_of_week', $dayName);

                    if ($hasSchedule) {

                        // --- LOGIKA BARU: CEK JAM (REALISTIS) ---
                        // Jika hari ini, tapi jam kelas belum mulai, JANGAN buat absen.
                        if ($isToday && $class->start_time > $currentTime) {
                            // Skip kelas ini karena belum waktunya
                            continue; 
                        }

                        // B. BUAT SESI
                        $session = AttendanceSession::firstOrCreate([
                            'class_id' => $class->id,
                            'date'     => $date->format('Y-m-d'),
                        ]);

                        // C. BUAT ABSENSI SISWA
                        foreach ($class->students as $student) {
                            // Probabilitas status (biar data variatif)
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

                        // D. BUAT ABSENSI GURU
                        $teacherId = $class->form_teacher_id ?? $class->local_teacher_id;
                        if ($teacherId) {
                            TeacherAttendanceRecord::updateOrCreate(
                                [
                                    'attendance_session_id' => $session->id,
                                    'teacher_id'            => $teacherId
                                ],
                                [
                                    'status'  => 'present',
                                    'comment' => 'Teaching material for ' . $dayName,
                                ]
                            );
                        }
                    }
                }
            }

            DB::commit();
            $this->command->info('Attendance data seeded successfully (Time adjusted)!');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error: ' . $e->getMessage());
        }
    }
}