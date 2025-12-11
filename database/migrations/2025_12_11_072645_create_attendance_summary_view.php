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
        $view = "
            CREATE VIEW attendance_summary_v AS
            SELECT
                t2.date,
                SUM(CASE WHEN t1.status = 'present' THEN 1 ELSE 0 END) AS total_present,
                SUM(CASE WHEN t1.status = 'permission' THEN 1 ELSE 0 END) AS total_permission,
                SUM(CASE WHEN t1.status = 'sick' THEN 1 ELSE 0 END) AS total_sick,
                SUM(CASE WHEN t1.status = 'late' THEN 1 ELSE 0 END) AS total_late,
                SUM(CASE WHEN t1.status = 'absent' THEN 1 ELSE 0 END) AS total_absent,
                COUNT(t1.id) AS total_records
            FROM attendance_records t1
            JOIN attendance_sessions t2 ON t1.attendance_session_id = t2.id
            GROUP BY t2.date
        ";

        DB::statement($view);
    }

    /**
     * Reverse the migrations (Menghapus View jika dilakukan rollback).
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS attendance_summary_v');
    }
};