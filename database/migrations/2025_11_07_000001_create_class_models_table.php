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
            CREATE TABLE classes (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

                category ENUM('pre_level', 'level', 'step', 'private') NOT NULL,
                name VARCHAR(100) NOT NULL,
                classroom VARCHAR(50) NOT NULL,

                form_teacher_id BIGINT UNSIGNED NULL,
                local_teacher_id BIGINT UNSIGNED NULL,

                start_time TIME NOT NULL,
                end_time TIME NOT NULL,

                start_month ENUM(
                    'January', 'February', 'March', 'April', 'May', 'June',
                    'July', 'August', 'September', 'October', 'November', 'December'
                ) NOT NULL,

                end_month ENUM(
                    'January', 'February', 'March', 'April', 'May', 'June',
                    'July', 'August', 'September', 'October', 'November', 'December'
                ) NOT NULL,

                academic_year YEAR NOT NULL,

                is_active TINYINT(1) NOT NULL DEFAULT 1,

                created_at TIMESTAMP NULL DEFAULT NULL,
                updated_at TIMESTAMP NULL DEFAULT NULL,
                deleted_at TIMESTAMP NULL DEFAULT NULL,

                INDEX idx_classes_form_teacher (form_teacher_id),
                INDEX idx_classes_local_teacher (local_teacher_id),

                CONSTRAINT fk_classes_form_teacher
                    FOREIGN KEY (form_teacher_id)
                    REFERENCES users(id)
                    ON DELETE SET NULL
                    ON UPDATE CASCADE,

                CONSTRAINT fk_classes_local_teacher
                    FOREIGN KEY (local_teacher_id)
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
        DB::unprepared("DROP TABLE IF EXISTS classes;");
    }
};
