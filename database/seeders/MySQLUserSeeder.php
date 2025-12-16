<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MySQLUserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Hapus user lama (Reset)
        DB::statement("DROP USER IF EXISTS 'iec_admin'@'%';");
        DB::statement("DROP USER IF EXISTS 'iec_teacher'@'%';");
        DB::statement("FLUSH PRIVILEGES;");

        // =====================================================================
        // 2. Buat user ADMIN (Full Power)
        // =====================================================================
        DB::statement("CREATE USER 'iec_admin'@'%' IDENTIFIED BY 'IEC_Jemadi_Admin';");
        DB::statement("
            GRANT SELECT, INSERT, UPDATE, DELETE, EXECUTE,
            SHOW VIEW, REFERENCES, INDEX, CREATE ROUTINE, ALTER ROUTINE
            ON iec_jemadi.* TO 'iec_admin'@'%';
        ");

        // =====================================================================
        // 3. Buat user TEACHER (Restricted)
        // =====================================================================
        DB::statement("CREATE USER 'iec_teacher'@'%' IDENTIFIED BY 'IEC_Teacher123';");

        // A. Izin ke Tabel (Data Mentah)
        DB::statement("GRANT SELECT ON iec_jemadi.users TO 'iec_teacher'@'%';");
        DB::statement("
            GRANT UPDATE (name, email, phone, password, remember_token, updated_at) 
            ON iec_jemadi.users TO 'iec_teacher'@'%';
        ");

        DB::statement("GRANT SELECT ON iec_jemadi.students TO 'iec_teacher'@'%';");
        DB::statement("GRANT SELECT ON iec_jemadi.classes TO 'iec_teacher'@'%';");
        DB::statement("GRANT SELECT ON iec_jemadi.schedules TO 'iec_teacher'@'%';");

        // Transaction Tables (Full CRUD)
        DB::statement("GRANT SELECT, INSERT, UPDATE ON iec_jemadi.assessment_sessions TO 'iec_teacher'@'%';");
        DB::statement("GRANT SELECT, INSERT, UPDATE ON iec_jemadi.assessment_forms TO 'iec_teacher'@'%';");
        DB::statement("GRANT SELECT, INSERT, UPDATE ON iec_jemadi.speaking_tests TO 'iec_teacher'@'%';");
        DB::statement("GRANT SELECT, INSERT, UPDATE ON iec_jemadi.speaking_test_results TO 'iec_teacher'@'%';");
        DB::statement("GRANT SELECT, INSERT, UPDATE ON iec_jemadi.class_sessions TO 'iec_teacher'@'%';");
        DB::statement("GRANT SELECT, INSERT, UPDATE ON iec_jemadi.attendance_records TO 'iec_teacher'@'%';");

        // Log Tables (View & Insert Only)
        $logTables = [
            'user_logs', 'class_logs', 'student_logs',
            'assessment_session_logs', 'assessment_form_logs',
            'speaking_test_logs', 'speaking_test_result_logs',
            'class_session_logs', 'attendance_record_logs',
        ];
        foreach ($logTables as $table) {
            DB::statement("GRANT SELECT, INSERT ON iec_jemadi.{$table} TO 'iec_teacher'@'%';");
        }

        // ---------------------------------------------------------------------
        // B. Izin Mengakses VIEWS
        // ---------------------------------------------------------------------
        $views = [
            'v_today_schedule',       // Dashboard
            'v_student_grades',       // Assessment Detail
            'v_class_activity_logs',  // Class Detail History
            // 'v_weekly_absence',    // (Opsional: Jika teacher punya analytics)
            // 'v_attendance_summary' // (Opsional: Jika teacher punya analytics)
        ];
        
        foreach ($views as $view) {
            DB::statement("GRANT SELECT ON iec_jemadi.{$view} TO 'iec_teacher'@'%';");
        }

        // ---------------------------------------------------------------------
        // C. Izin Menjalankan STORED PROCEDURES (Hanya yang diperlukan Teacher)
        // ---------------------------------------------------------------------
        
        // 1. Input Nilai
        DB::statement("GRANT EXECUTE ON PROCEDURE iec_jemadi.p_UpdateStudentGrade TO 'iec_teacher'@'%';");
        
        // 2. Detail Kelas (List Siswa & Stats Kehadiran)
        DB::statement("GRANT EXECUTE ON PROCEDURE iec_jemadi.p_get_class_attendance_stats TO 'iec_teacher'@'%';");
        
        // 3. Input Absensi (NEW)
        DB::statement("GRANT EXECUTE ON PROCEDURE iec_jemadi.p_GetSessionAttendanceList TO 'iec_teacher'@'%';");
        DB::statement("GRANT EXECUTE ON PROCEDURE iec_jemadi.p_UpsertAttendance TO 'iec_teacher'@'%';");
        
        // ---------------------------------------------------------------------
        // D. Izin Menjalankan FUNCTIONS
        // ---------------------------------------------------------------------
        DB::statement("GRANT EXECUTE ON FUNCTION iec_jemadi.f_CalcAssessmentAvg TO 'iec_teacher'@'%';");
        DB::statement("GRANT EXECUTE ON FUNCTION iec_jemadi.f_GetGrade TO 'iec_teacher'@'%';");

        DB::statement("FLUSH PRIVILEGES;");
    }
}