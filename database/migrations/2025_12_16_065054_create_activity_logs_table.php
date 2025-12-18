<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared("
            CREATE TABLE activity_logs (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

                -- 1. ACTOR (polymorphic)
                actor_type VARCHAR(255) NULL,
                actor_id BIGINT UNSIGNED NULL,

                -- 2. SUBJECT (polymorphic)
                subject_type VARCHAR(255) NULL,
                subject_id BIGINT UNSIGNED NULL,

                -- 3. DETAIL AKSI
                event VARCHAR(255) NOT NULL,
                description VARCHAR(255) NULL,

                -- 4. DATA LOG
                properties JSON NULL,

                -- 5. METADATA TAMBAHAN
                ip_address VARCHAR(45) NULL,
                user_agent VARCHAR(255) NULL,

                created_at TIMESTAMP NULL DEFAULT NULL,
                updated_at TIMESTAMP NULL DEFAULT NULL,

                INDEX idx_activity_logs_event (event),
                INDEX idx_activity_logs_actor (actor_type, actor_id),
                INDEX idx_activity_logs_subject (subject_type, subject_id)
            ) ENGINE=InnoDB
            DEFAULT CHARSET=utf8mb4
            COLLATE=utf8mb4_unicode_ci;
        ");
    }

    public function down(): void
    {
        DB::unprepared("DROP TABLE IF EXISTS activity_logs;");
    }
};
