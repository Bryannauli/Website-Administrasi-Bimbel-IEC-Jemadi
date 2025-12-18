<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::unprepared("
            CREATE TABLE class_sessions (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

                class_id BIGINT UNSIGNED NOT NULL,
                date DATE NOT NULL,

                teacher_id BIGINT UNSIGNED NULL,

                comment TEXT NULL,

                created_at TIMESTAMP NULL DEFAULT NULL,
                updated_at TIMESTAMP NULL DEFAULT NULL,
                deleted_at TIMESTAMP NULL DEFAULT NULL,

                INDEX idx_class_sessions_class (class_id),
                INDEX idx_class_sessions_teacher (teacher_id),

                CONSTRAINT fk_class_sessions_class
                    FOREIGN KEY (class_id)
                    REFERENCES classes(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE,

                CONSTRAINT fk_class_sessions_teacher
                    FOREIGN KEY (teacher_id)
                    REFERENCES users(id)
                    ON DELETE SET NULL
                    ON UPDATE CASCADE
            ) ENGINE=InnoDB
            DEFAULT CHARSET=utf8mb4
            COLLATE=utf8mb4_unicode_ci;
        ");

        // hapus tabel lama
        DB::unprepared("DROP TABLE IF EXISTS teacher_attendance_records;");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP TABLE IF EXISTS class_sessions;");
    }
};
