<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_logs', function (Blueprint $table) {
            $table->id();
            // Menghubungkan log dengan siswa yang diedit
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            
            // Menghubungkan dengan user yang melakukan aksi (Admin/Teacher)
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            
            // Jenis aksi: CREATE, UPDATE, DELETE
            $table->string('action'); 
            
            // Menyimpan data perubahan dalam format JSON (Opsional, agar detail)
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            
            // Waktu aksi dilakukan (created_at)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_logs');
    }
};