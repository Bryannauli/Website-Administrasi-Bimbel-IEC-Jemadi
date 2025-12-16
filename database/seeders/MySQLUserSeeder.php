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
            GRANT UPDATE (name, email, phone, password, remember_token, updated_at) 
            ON iec_jemadi.users TO 'iec_teacher'@'%';
        ");

        DB::statement("GRANT SELECT ON iec_jemadi.students TO 'iec_teacher'@'%';");
        DB::statement("GRANT SELECT ON iec_jemadi.classes TO 'iec_teacher'@'%';");
        DB::statement("GRANT SELECT ON iec_jemadi.schedules TO 'iec_teacher'@'%';");

        DB::statement("GRANT SELECT, INSERT, UPDATE ON iec_jemadi.assessment_sessions TO 'iec_teacher'@'%';");
        DB::statement("GRANT SELECT, INSERT, UPDATE ON iec_jemadi.assessment_forms TO 'iec_teacher'@'%';");
        DB::statement("GRANT SELECT, INSERT, UPDATE ON iec_jemadi.speaking_tests TO 'iec_teacher'@'%';");
        DB::statement("GRANT SELECT, INSERT, UPDATE ON iec_jemadi.speaking_test_results TO 'iec_teacher'@'%';");
        DB::statement("GRANT SELECT, INSERT, UPDATE ON iec_jemadi.class_sessions TO 'iec_teacher'@'%';");
        DB::statement("GRANT SELECT, INSERT, UPDATE ON iec_jemadi.attendance_records TO 'iec_teacher'@'%';");

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
            'class_session_logs',
            'attendance_record_logs',
        ];

        foreach ($logTables as $table) {
            DB::statement("GRANT SELECT, INSERT ON iec_jemadi.{$table} TO 'iec_teacher'@'%';");
        }

        DB::statement("FLUSH PRIVILEGES;");

        // A. VIEW: Teacher butuh ini untuk melihat data
        $views = [
            'v_today_schedule',       // Dashboard
            'v_student_grades',       // Assessment
            'v_class_activity_logs',  // Class Detail
        ];
        foreach ($views as $view) {
            DB::statement("GRANT SELECT ON iec_jemadi.{$view} TO 'iec_teacher'@'%';");
        }

        // B. PROCEDURE: HANYA BERIKAN YANG DIPAKAI
        // Hapus p_GetDashboardStats, p_GetAttendanceStats, dll (itu punya Admin)

        // 1. Input Nilai (PENTING)
        DB::statement("GRANT EXECUTE ON PROCEDURE iec_jemadi.p_UpdateStudentGrade TO 'iec_teacher'@'%';");

        // 2. Detail Kelas (PENTING)
        DB::statement("GRANT EXECUTE ON PROCEDURE iec_jemadi.p_get_class_attendance_stats TO 'iec_teacher'@'%';");

        // C. FUNCTIONS
        DB::statement("GRANT EXECUTE ON FUNCTION iec_jemadi.f_CalcAssessmentAvg TO 'iec_teacher'@'%';");
        DB::statement("GRANT EXECUTE ON FUNCTION iec_jemadi.f_GetGrade TO 'iec_teacher'@'%';");

        DB::statement("FLUSH PRIVILEGES;");
    }
}
