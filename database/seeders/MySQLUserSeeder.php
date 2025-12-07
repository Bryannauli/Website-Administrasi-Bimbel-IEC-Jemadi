<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MySQLUserSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus user lama
        DB::statement("DROP USER IF EXISTS 'iec_admin'@'%';");
        DB::statement("DROP USER IF EXISTS 'iec_teacher'@'%';");
        DB::statement("FLUSH PRIVILEGES;");

        // Buat user ADMIN
        DB::statement("CREATE USER 'iec_admin'@'%' IDENTIFIED BY 'IEC_Jemadi_Admin';");
        // Berikan semua hak akses kecuali yang bisa manipulasi struktur tabel
        DB::statement("
            GRANT SELECT, INSERT, UPDATE, DELETE, EXECUTE,
            SHOW VIEW, REFERENCES, INDEX
            ON iec_jemadi.* TO 'iec_admin'@'%';
        ");

        // Buat user TEACHER
        DB::statement("CREATE USER 'iec_teacher'@'%' IDENTIFIED BY 'IEC_Teacher123';");
        DB::statement("GRANT SELECT ON iec_jemadi.users TO 'iec_teacher'@'%';");
        DB::statement("GRANT SELECT ON iec_jemadi.students TO 'iec_teacher'@'%';");
        DB::statement("GRANT SELECT ON iec_jemadi.classes TO 'iec_teacher'@'%';");
        DB::statement("GRANT SELECT ON iec_jemadi.schedules TO 'iec_teacher'@'%';");
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
