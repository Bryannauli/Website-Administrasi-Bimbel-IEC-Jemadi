<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendanceSessionLogsTable extends Migration 
{
    public function up(): void
    {
        Schema::create('attendance_session_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_session_id')
                ->constrained('attendance_sessions')
                ->onDelete('cascade');

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
        Schema::dropIfExists('attendance_session_logs');
    }
}