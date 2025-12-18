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
            CREATE TABLE students (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

                student_number VARCHAR(255) NOT NULL UNIQUE,
                name VARCHAR(255) NOT NULL,

                gender ENUM('male', 'female') NOT NULL,
                phone VARCHAR(255) NULL,
                address TEXT NULL,

                is_active TINYINT(1) NOT NULL DEFAULT 1,

                class_id BIGINT UNSIGNED NULL,

                created_at TIMESTAMP NULL DEFAULT NULL,
                updated_at TIMESTAMP NULL DEFAULT NULL,
                deleted_at TIMESTAMP NULL DEFAULT NULL,

                INDEX idx_students_class_id (class_id),

                CONSTRAINT fk_students_class
                    FOREIGN KEY (class_id)
                    REFERENCES classes(id)
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
        DB::unprepared("DROP TABLE IF EXISTS students;");
    }
};
