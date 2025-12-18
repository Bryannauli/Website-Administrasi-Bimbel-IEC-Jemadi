<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared("
            CREATE TABLE attendance_records (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

                class_session_id BIGINT UNSIGNED NOT NULL,
                student_id BIGINT UNSIGNED NOT NULL,

                status ENUM(
                    'present',
                    'absent',
                    'late',
                    'permission',
                    'sick'
                ) NOT NULL,

                created_at TIMESTAMP NULL DEFAULT NULL,
                updated_at TIMESTAMP NULL DEFAULT NULL,

                UNIQUE KEY uq_attendance_records (class_session_id, student_id),

                INDEX idx_attendance_class_session (class_session_id),
                INDEX idx_attendance_student (student_id),

                CONSTRAINT fk_attendance_class_session
                    FOREIGN KEY (class_session_id)
                    REFERENCES class_sessions(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE,

                CONSTRAINT fk_attendance_student
                    FOREIGN KEY (student_id)
                    REFERENCES students(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE
            ) ENGINE=InnoDB
            DEFAULT CHARSET=utf8mb4
            COLLATE=utf8mb4_unicode_ci;
        ");
    }

    public function down(): void
    {
        DB::unprepared("DROP TABLE IF EXISTS attendance_records;");
    }
};
