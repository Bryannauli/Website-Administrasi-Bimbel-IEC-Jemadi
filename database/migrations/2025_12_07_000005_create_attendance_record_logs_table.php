<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendanceRecordLogsTable extends Migration 
{
    public function up(): void
    {
        Schema::create('attendance_record_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_record_id')
                ->nullable()
                ->constrained('attendance_records')
                ->nullOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('action');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_record_logs');
    }
}