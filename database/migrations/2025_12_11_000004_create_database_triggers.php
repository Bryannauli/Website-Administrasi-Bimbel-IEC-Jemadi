<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel Audit
        DB::unprepared('
            CREATE TABLE IF NOT EXISTS db_audit_logs (
                id BIGINT AUTO_INCREMENT PRIMARY KEY,
                table_name VARCHAR(50),
                action VARCHAR(20),
                record_id BIGINT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
        ');

        // TRIGGER: Log Insert Siswa
        DB::unprepared('
            DROP TRIGGER IF EXISTS tr_after_student_insert;
            CREATE TRIGGER tr_after_student_insert
            AFTER INSERT ON students
            FOR EACH ROW
            BEGIN
                INSERT INTO db_audit_logs (table_name, action, record_id)
                VALUES ("students", "INSERT", NEW.id);
            END
        ');
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS tr_after_student_insert');
        DB::unprepared('DROP TABLE IF EXISTS db_audit_logs');
    }
};