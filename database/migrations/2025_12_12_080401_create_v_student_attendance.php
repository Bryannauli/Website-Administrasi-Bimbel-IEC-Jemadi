<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Membuat View untuk menggabungkan records dan sessions
        $sql = "
            CREATE VIEW v_student_attendance AS
            SELECT 
                ar.student_id,
                ar.status,
                ar.created_at,
                ar.updated_at,
                s.date AS session_date,
                s.name AS session_name
            FROM attendance_records ar
            JOIN attendance_sessions s ON ar.attendance_session_id = s.id;
        ";

        DB::statement($sql);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Menghapus View
        DB::statement("DROP VIEW IF EXISTS v_student_attendance");
    }
};