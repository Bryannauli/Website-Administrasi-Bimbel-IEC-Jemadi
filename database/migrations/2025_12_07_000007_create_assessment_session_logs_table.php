<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessment_session_logs', function (Blueprint $table) {
            $table->id();
            // Menghubungkan log dengan sesi penilaian yang diedit
            $table->foreignId('assessment_session_id')
                ->nullable()
                ->constrained('assessment_sessions')
                ->nullOnDelete();
            
            // Menghubungkan dengan user yang melakukan aksi
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            
            $table->string('action'); // CREATE, UPDATE, DELETE
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_session_logs');
    }
};