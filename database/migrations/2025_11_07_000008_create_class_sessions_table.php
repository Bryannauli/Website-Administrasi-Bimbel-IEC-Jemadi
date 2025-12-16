<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. GANTI NAMA TABEL menjadi 'class_sessions'
        Schema::create('class_sessions', function (Blueprint $table) { 
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->date('date');
            
            // 2. TAMBAHKAN foreign key untuk guru yang mengajar
            $table->foreignId('teacher_id') 
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete(); 
                    
            // 3. Kolom comment (berdasarkan konteks sebelumnya)
            $table->text('comment')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::dropIfExists('teacher_attendance_records');
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // GANTI NAMA TABEL
        Schema::dropIfExists('class_sessions');
    }
};