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
            CREATE TABLE assessment_forms (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

                assessment_session_id BIGINT UNSIGNED NOT NULL,
                student_id BIGINT UNSIGNED NOT NULL,

                vocabulary TINYINT UNSIGNED NULL,
                grammar TINYINT UNSIGNED NULL,
                listening TINYINT UNSIGNED NULL,
                speaking TINYINT UNSIGNED NULL,
                reading TINYINT UNSIGNED NULL,
                spelling TINYINT UNSIGNED NULL,

                created_at TIMESTAMP NULL DEFAULT NULL,
                updated_at TIMESTAMP NULL DEFAULT NULL,
                deleted_at TIMESTAMP NULL DEFAULT NULL,

                UNIQUE KEY uq_assessment_forms (assessment_session_id, student_id),

                INDEX idx_assessment_forms_session (assessment_session_id),
                INDEX idx_assessment_forms_student (student_id),

                CONSTRAINT fk_assessment_forms_session
                    FOREIGN KEY (assessment_session_id)
                    REFERENCES assessment_sessions(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE,

                CONSTRAINT fk_assessment_forms_student
                    FOREIGN KEY (student_id)
                    REFERENCES students(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE
            ) ENGINE=InnoDB
            DEFAULT CHARSET=utf8mb4
            COLLATE=utf8mb4_unicode_ci;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP TABLE IF EXISTS assessment_forms;");
    }
};
