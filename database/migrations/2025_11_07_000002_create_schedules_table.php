<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared("
            CREATE TABLE schedules (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

                class_id BIGINT UNSIGNED NOT NULL,

                day_of_week ENUM(
                    'Monday', 'Tuesday', 'Wednesday',
                    'Thursday', 'Friday', 'Saturday', 'Sunday'
                ) NOT NULL,

                teacher_type ENUM('form', 'local') NOT NULL DEFAULT 'form',

                created_at TIMESTAMP NULL DEFAULT NULL,
                updated_at TIMESTAMP NULL DEFAULT NULL,

                INDEX idx_schedules_day_of_week (day_of_week),
                INDEX idx_schedules_class_id (class_id),

                CONSTRAINT fk_schedules_class
                    FOREIGN KEY (class_id)
                    REFERENCES classes(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE
            ) ENGINE=InnoDB
            DEFAULT CHARSET=utf8mb4
            COLLATE=utf8mb4_unicode_ci;
        ");
    }

    public function down(): void
    {
        DB::unprepared("DROP TABLE IF EXISTS schedules;");
    }
};
