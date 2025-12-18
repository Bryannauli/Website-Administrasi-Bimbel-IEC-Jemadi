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
            CREATE TABLE speaking_test_results (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

                assessment_session_id BIGINT UNSIGNED NOT NULL,
                student_id BIGINT UNSIGNED NOT NULL,

                content_score TINYINT UNSIGNED NULL,
                participation_score TINYINT UNSIGNED NULL,

                created_at TIMESTAMP NULL DEFAULT NULL,
                updated_at TIMESTAMP NULL DEFAULT NULL,
                deleted_at TIMESTAMP NULL DEFAULT NULL,

                UNIQUE KEY uq_speaking_test_results (assessment_session_id, student_id),

                INDEX idx_speaking_test_results_session (assessment_session_id),
                INDEX idx_speaking_test_results_student (student_id),

                CONSTRAINT fk_speaking_test_results_session
                    FOREIGN KEY (assessment_session_id)
                    REFERENCES assessment_sessions(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE,

                CONSTRAINT fk_speaking_test_results_student
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
        DB::unprepared("DROP TABLE IF EXISTS speaking_test_results;");
    }
};
