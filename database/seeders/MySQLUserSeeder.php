<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MySQLUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement("DROP USER IF EXISTS 'iec_user'@'%';");
        
        DB::statement("
            CREATE USER IF NOT EXISTS 'iec_admin'@'%' IDENTIFIED BY 'IEC_Jemadi_Admin';
        ");

        DB::statement("
            GRANT SELECT, INSERT, UPDATE, DELETE, EXECUTE, SHOW VIEW 
            ON iec_jemadi.* TO 'iec_admin'@'%';
        ");

        DB::statement("FLUSH PRIVILEGES;");

        DB::statement("
            CREATE USER IF NOT EXISTS 'iec_teacher'@'%' IDENTIFIED BY 'IEC_Teacher123';
        ");

        // Hak Akses BACA SAJA (SELECT) untuk login dan data referensi
        // Guru hanya bisa lihat daftar murid, kelas, dan jadwal
        DB::statement("GRANT SELECT ON iec_jemadi.users TO 'iec_teacher'@'%';");
        DB::statement("GRANT SELECT ON iec_jemadi.students TO 'iec_teacher'@'%';");
        DB::statement("GRANT SELECT ON iec_jemadi.classes TO 'iec_teacher'@'%';");
        DB::statement("GRANT SELECT ON iec_jemadi.schedules TO 'iec_teacher'@'%';");
        
        // Hak Akses INPUT DATA (SELECT, INSERT, UPDATE)
        // Guru bisa mengisi nilai dan absen
        DB::statement("GRANT SELECT, INSERT, UPDATE ON iec_jemadi.assessment_sessions TO 'iec_teacher'@'%';");
        DB::statement("GRANT SELECT, INSERT, UPDATE ON iec_jemadi.assessment_forms TO 'iec_teacher'@'%';");

        DB::statement("GRANT SELECT, INSERT, UPDATE ON iec_jemadi.speaking_tests TO 'iec_teacher'@'%';");
        DB::statement("GRANT SELECT, INSERT, UPDATE ON iec_jemadi.speaking_test_results TO 'iec_teacher'@'%';");

        DB::statement("GRANT SELECT, INSERT, UPDATE ON iec_jemadi.attendance_sessions TO 'iec_teacher'@'%';");
        DB::statement("GRANT SELECT, INSERT, UPDATE ON iec_jemadi.attendance_records TO 'iec_teacher'@'%';");
        DB::statement("GRANT SELECT, INSERT, UPDATE ON iec_jemadi.teacher_attendance_records TO 'iec_teacher'@'%';");

        DB::statement("FLUSH PRIVILEGES;");
    }
}
