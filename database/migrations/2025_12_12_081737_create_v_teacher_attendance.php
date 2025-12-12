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
        // SQL untuk membuat View
        $sql = "
            CREATE VIEW v_teacher_attendance AS
            SELECT 
                tar.id AS record_id,             -- ID Record Absensi Guru
                tar.teacher_id,
                tar.status,
                tar.created_at,
                tar.attendance_session_id,
                s.date AS session_date,          -- Data tanggal dari Session
                s.name AS session_name           -- Nama Session
            FROM teacher_attendance_records tar
            JOIN attendance_sessions s ON tar.attendance_session_id = s.id
            ORDER BY tar.id DESC;
        ";

        DB::statement($sql);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // SQL untuk menghapus View
        DB::statement("DROP VIEW IF EXISTS v_teacher_attendance");
    }
};