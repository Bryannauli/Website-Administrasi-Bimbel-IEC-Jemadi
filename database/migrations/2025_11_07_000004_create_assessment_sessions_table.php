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
            CREATE TABLE assessment_sessions (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

                class_id BIGINT UNSIGNED NULL,

                type ENUM('mid', 'final') NOT NULL,

                written_date DATE NULL,

                speaking_date DATE NULL,
                speaking_topic VARCHAR(200) NULL,

                interviewer_id BIGINT UNSIGNED NULL,

                status ENUM('draft', 'submitted', 'final') NOT NULL DEFAULT 'draft',

                created_at TIMESTAMP NULL DEFAULT NULL,
                updated_at TIMESTAMP NULL DEFAULT NULL,
                deleted_at TIMESTAMP NULL DEFAULT NULL,

                UNIQUE KEY uq_assessment_class_type (class_id, type),

                INDEX idx_assessment_class_id (class_id),
                INDEX idx_assessment_interviewer (interviewer_id),

                CONSTRAINT fk_assessment_class
                    FOREIGN KEY (class_id)
                    REFERENCES classes(id)
                    ON DELETE SET NULL
                    ON UPDATE CASCADE,

                CONSTRAINT fk_assessment_interviewer
                    FOREIGN KEY (interviewer_id)
                    REFERENCES users(id)
                    ON DELETE SET NULL
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
        DB::unprepared("DROP TABLE IF EXISTS assessment_sessions;");
    }
};
