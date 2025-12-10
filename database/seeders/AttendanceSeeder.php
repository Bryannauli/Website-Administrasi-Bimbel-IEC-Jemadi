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
        // 1. Tentukan Rentang Waktu (Misal: 1 Bulan ke belakang sampai hari ini)
        $startDate = Carbon::now()->subMonth();
        $endDate   = Carbon::now();

        // 2. Ambil Kelas Aktif beserta Jadwal dan Siswanya
        $classes = ClassModel::with(['schedules', 'students' => function($q) {
            $q->where('is_active', true);
        }])->where('is_active', true)->get();

        $this->command->info('Seeding attendance from ' . $startDate->format('Y-m-d') . ' to ' . $endDate->format('Y-m-d'));

        DB::beginTransaction();

        try {
            // 3. Loop Tanggal per Hari
            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                
                // Ambil nama hari (Monday, Tuesday, dst) untuk dicocokkan dengan Schedule
                $dayName = $date->format('l');

                foreach ($classes as $class) {
                    
                    // A. CEK JADWAL: Apakah kelas ini punya jadwal di hari tersebut?
                    $hasSchedule = $class->schedules->contains('day_of_week', $dayName);

                    if ($hasSchedule) {
                        
                        // B. BUAT SESI (AttendanceSession)
                        // Pastikan tidak duplikat (opsional, tapi aman untuk seeder berulang)
                        $session = AttendanceSession::firstOrCreate([
                            'class_id' => $class->id,
                            'date'     => $date->format('Y-m-d'),
                        ]);

                        // C. BUAT ABSENSI SISWA (AttendanceRecord)
                        foreach ($class->students as $student) {
                            // Random Status dengan probabilitas
                            $rand = rand(1, 100);
                            if ($rand <= 85) $status = 'present';      // 85% Hadir
                            elseif ($rand <= 90) $status = 'late';     // 5% Telat
                            elseif ($rand <= 95) $status = 'permission'; // 5% Izin
                            elseif ($rand <= 98) $status = 'sick';     // 3% Sakit
                            else $status = 'absent';                   // 2% Bolos

                            // Gunakan updateOrCreate agar tidak error jika dijalankan 2x
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

                        // D. BUAT ABSENSI GURU (TeacherAttendanceRecord)
                        // Ambil Guru: Prioritas Wali Kelas, kalau null ambil Guru Lokal
                        $teacherId = $class->form_teacher_id ?? $class->local_teacher_id;

                        if ($teacherId) {
                            TeacherAttendanceRecord::updateOrCreate(
                                [
                                    'attendance_session_id' => $session->id,
                                    'teacher_id'            => $teacherId
                                ],
                                [
                                    'status'  => 'present',
                                    'comment' => 'Teaching material for ' . $dayName, // Kolom ini ADA di tabel guru
                                ]
                            );
                        }
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