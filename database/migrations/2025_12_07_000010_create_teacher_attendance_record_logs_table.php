<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeacherAttendanceRecordLogsTable extends Migration 
{
    public function up(): void
    {
        Schema::create('teacher_attendance_record_logs', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('teacher_attendance_record_id')->nullable(); 
            
            $table->foreign('teacher_attendance_record_id', 'teacher_log_record_fk')
                    ->references('id')
                    ->on('teacher_attendance_records')
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
        Schema::dropIfExists('teacher_attendance_record_logs');
    }
}