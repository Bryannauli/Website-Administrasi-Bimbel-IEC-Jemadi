<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MySQLUserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Reset User
        DB::statement("DROP USER IF EXISTS 'iec_admin'@'%';");
        DB::statement("DROP USER IF EXISTS 'iec_teacher'@'%';");
        DB::statement("FLUSH PRIVILEGES;");

        // =====================================================================
        // 2. User ADMIN (Full Power)
        // =====================================================================
        DB::statement("CREATE USER 'iec_admin'@'%' IDENTIFIED BY 'IEC_Jemadi_Admin';");
        DB::statement("
            GRANT SELECT, INSERT, UPDATE, DELETE, EXECUTE,
            SHOW VIEW, REFERENCES, INDEX, CREATE ROUTINE, ALTER ROUTINE,
            DROP, CREATE, ALTER
            ON iec_jemadi.* TO 'iec_admin'@'%';
        ");

        // =====================================================================
        // 3. User TEACHER (Restricted)
        // =====================================================================
        DB::statement("CREATE USER 'iec_teacher'@'%' IDENTIFIED BY 'IEC_Teacher123';");

        // A. Akses Tabel Utama (Data Mentah)
        DB::statement("GRANT SELECT ON iec_jemadi.users TO 'iec_teacher'@'%';");
        DB::statement("
            GRANT UPDATE (name, email, phone, password, remember_token, updated_at) 
            ON iec_jemadi.users TO 'iec_teacher'@'%';
        ");

        DB::statement("GRANT SELECT ON iec_jemadi.students TO 'iec_teacher'@'%';");
        DB::statement("GRANT SELECT ON iec_jemadi.classes TO 'iec_teacher'@'%';");
        DB::statement("GRANT SELECT ON iec_jemadi.schedules TO 'iec_teacher'@'%';");

        // B. Akses Tabel Transaksi (Full CRUD)
        DB::statement("GRANT SELECT, INSERT, UPDATE ON iec_jemadi.assessment_sessions TO 'iec_teacher'@'%';");
        DB::statement("GRANT SELECT, INSERT, UPDATE ON iec_jemadi.assessment_forms TO 'iec_teacher'@'%';");
        DB::statement("GRANT SELECT, INSERT, UPDATE ON iec_jemadi.speaking_tests TO 'iec_teacher'@'%';");
        DB::statement("GRANT SELECT, INSERT, UPDATE ON iec_jemadi.speaking_test_results TO 'iec_teacher'@'%';");
        DB::statement("GRANT SELECT, INSERT, UPDATE ON iec_jemadi.class_sessions TO 'iec_teacher'@'%';");
        DB::statement("GRANT SELECT, INSERT, UPDATE ON iec_jemadi.attendance_records TO 'iec_teacher'@'%';");

        // C. Akses Tabel Log
        DB::statement("GRANT SELECT, INSERT ON iec_jemadi.activity_logs TO 'iec_teacher'@'%';");

        // ---------------------------------------------------------------------
        // D. Izin Mengakses VIEWS
        // ---------------------------------------------------------------------
        $views = [
            'v_today_schedule',       
            'v_student_grades',       
            'v_class_activity_logs',  
            'v_teacher_teaching_history' 
        ];
        
        foreach ($views as $view) {
            DB::statement("GRANT SELECT ON iec_jemadi.{$view} TO 'iec_teacher'@'%';");
        }

        // ---------------------------------------------------------------------
        // E. Izin Stored Procedures & Functions
        // ---------------------------------------------------------------------
        
        // 1. Input Nilai
        DB::statement("GRANT EXECUTE ON PROCEDURE iec_jemadi.p_UpdateStudentGrade TO 'iec_teacher'@'%';");
        
        // 2. Detail Kelas & Assessment Sheet (YANG BARU DITAMBAHKAN)
        DB::statement("GRANT EXECUTE ON PROCEDURE iec_jemadi.p_get_class_attendance_stats TO 'iec_teacher'@'%';");
        DB::statement("GRANT EXECUTE ON PROCEDURE iec_jemadi.p_GetAssessmentSheet TO 'iec_teacher'@'%';"); // <<< NEW
        
        // 3. Input Absensi
        DB::statement("GRANT EXECUTE ON PROCEDURE iec_jemadi.p_GetSessionAttendanceList TO 'iec_teacher'@'%';");
        DB::statement("GRANT EXECUTE ON PROCEDURE iec_jemadi.p_UpsertAttendance TO 'iec_teacher'@'%';");
        
        // 4. Helper Functions
        DB::statement("GRANT EXECUTE ON FUNCTION iec_jemadi.f_CalcAssessmentAvg TO 'iec_teacher'@'%';");
        DB::statement("GRANT EXECUTE ON FUNCTION iec_jemadi.f_GetGrade TO 'iec_teacher'@'%';");

        DB::statement("FLUSH PRIVILEGES;");
    }
}