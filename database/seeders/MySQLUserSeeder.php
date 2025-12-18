<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MySQLUserSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil kredensial TANPA memberikan nilai default yang sensitif
        // Jika di .env tidak ada, maka variabel akan bernilai null atau string kosong
        $adminUser   = env('DB_IEC_ADMIN_USERNAME');
        $adminPass   = env('DB_IEC_ADMIN_PASSWORD');
        $teacherUser = env('DB_IEC_TEACHER_USERNAME');
        $teacherPass = env('DB_IEC_TEACHER_PASSWORD');
        $dbName      = env('DB_DATABASE', 'iec_jemadi');

        // Validasi sederhana: Jika password kosong di .env, hentikan proses demi keamanan
        if (empty($adminPass) || empty($teacherPass)) {
            throw new \Exception("Kredensial database (Admin/Teacher) belum diatur di file .env!");
        }

        // 2. Reset User (Menghapus user lama jika ada)
        DB::statement("DROP USER IF EXISTS '{$adminUser}'@'%';");
        DB::statement("DROP USER IF EXISTS '{$teacherUser}'@'%';");
        DB::statement("FLUSH PRIVILEGES;");

        // =====================================================================
        // 3. User ADMIN (Full Power)
        // =====================================================================
        DB::statement("CREATE USER '{$adminUser}'@'%' IDENTIFIED BY '{$adminPass}';");
        DB::statement("
            GRANT SELECT, INSERT, UPDATE, DELETE, EXECUTE,
            SHOW VIEW, REFERENCES, INDEX, CREATE ROUTINE, ALTER ROUTINE,
            DROP, CREATE, ALTER
            ON {$dbName}.* TO '{$adminUser}'@'%';
        ");

        // =====================================================================
        // 4. User TEACHER (Restricted)
        // =====================================================================
        DB::statement("CREATE USER '{$teacherUser}'@'%' IDENTIFIED BY '{$teacherPass}';");

        // A. Akses Tabel Utama (Data Mentah)
        DB::statement("GRANT SELECT ON {$dbName}.users TO '{$teacherUser}'@'%';");
        DB::statement("
            GRANT UPDATE (name, email, phone, address, password, remember_token, updated_at) 
            ON {$dbName}.users TO '{$teacherUser}'@'%';
        ");

        DB::statement("GRANT SELECT ON {$dbName}.students TO '{$teacherUser}'@'%';");
        DB::statement("GRANT SELECT ON {$dbName}.classes TO '{$teacherUser}'@'%';");
        DB::statement("GRANT SELECT ON {$dbName}.schedules TO '{$teacherUser}'@'%';");

        // B. Akses Tabel Transaksi (Full CRUD)
        DB::statement("GRANT SELECT, INSERT, UPDATE ON {$dbName}.assessment_sessions TO '{$teacherUser}'@'%';");
        DB::statement("GRANT SELECT, INSERT, UPDATE ON {$dbName}.assessment_forms TO '{$teacherUser}'@'%';");
        DB::statement("GRANT SELECT, INSERT, UPDATE ON {$dbName}.speaking_test_results TO '{$teacherUser}'@'%';");
        DB::statement("GRANT SELECT, INSERT, UPDATE ON {$dbName}.class_sessions TO '{$teacherUser}'@'%';");
        DB::statement("GRANT SELECT, INSERT, UPDATE ON {$dbName}.attendance_records TO '{$teacherUser}'@'%';");

        // C. Akses Tabel Log
        DB::statement("GRANT SELECT, INSERT ON {$dbName}.activity_logs TO '{$teacherUser}'@'%';");

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
            DB::statement("GRANT SELECT ON {$dbName}.{$view} TO '{$teacherUser}'@'%';");
        }

        // ---------------------------------------------------------------------
        // E. Izin Stored Procedures & Functions
        // ---------------------------------------------------------------------
        
        // 1. Input Nilai
        DB::statement("GRANT EXECUTE ON PROCEDURE {$dbName}.p_UpdateStudentGrade TO '{$teacherUser}'@'%';");
        
        // 2. Detail Kelas & Assessment Sheet
        DB::statement("GRANT EXECUTE ON PROCEDURE {$dbName}.p_get_class_attendance_stats TO '{$teacherUser}'@'%';");
        DB::statement("GRANT EXECUTE ON PROCEDURE {$dbName}.p_GetAssessmentSheet TO '{$teacherUser}'@'%';");
        
        // 3. Input Absensi
        DB::statement("GRANT EXECUTE ON PROCEDURE {$dbName}.p_GetSessionAttendanceList TO '{$teacherUser}'@'%';");
        DB::statement("GRANT EXECUTE ON PROCEDURE {$dbName}.p_UpsertAttendance TO '{$teacherUser}'@'%';");
        
        // 4. Helper Functions
        DB::statement("GRANT EXECUTE ON FUNCTION {$dbName}.f_CalcAssessmentAvg TO '{$teacherUser}'@'%';");
        DB::statement("GRANT EXECUTE ON FUNCTION {$dbName}.f_GetGrade TO '{$teacherUser}'@'%';");

        DB::statement("FLUSH PRIVILEGES;");
    }
}