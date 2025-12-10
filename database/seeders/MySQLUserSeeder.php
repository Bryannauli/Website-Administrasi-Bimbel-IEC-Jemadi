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
        // Guru TIDAK BISA ubah: username, role, is_teacher, is_active, deleted_at
        DB::statement("
            GRANT UPDATE (name, photo, email, phone, password, remember_token, updated_at) 
            ON iec_jemadi.users TO 'iec_teacher'@'%';
        ");

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

        // TABEL LOGS
        // Guru hanya boleh LIHAT (SELECT) dan TULIS BARU (INSERT)
        // Guru TIDAK BOLEH ubah/hapus sejarah (No UPDATE/DELETE)
        $logTables = [
            'user_logs',
            'class_logs',
            'student_logs',
            'assessment_session_logs',
            'assessment_form_logs',
            'speaking_test_logs',
            'speaking_test_result_logs',
            'attendance_session_logs',
            'attendance_record_logs',
            'teacher_attendance_record_logs',
        ];

        foreach ($logTables as $table) {
            DB::statement("GRANT SELECT, INSERT ON iec_jemadi.{$table} TO 'iec_teacher'@'%';");
        }

        DB::statement("FLUSH PRIVILEGES;");
    }
}
