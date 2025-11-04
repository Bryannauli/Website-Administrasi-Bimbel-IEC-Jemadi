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
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            // Jenjang kelas
            $table->enum('category', ['pre_level', 'level', 'step']);
            // Nomor kelas (1–10, pre_level maksimal 5)
            $table->unsignedTinyInteger('number');
            // Classroom: nama negara (China, Italy, France, ...)
            $table->string('classroom', 50);
            $table->enum('status', ['active', 'inactive'])->default('active');

            // Relasi ke guru: form teacher dan local teacher
            $table->foreignId('form_teacher_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('local_teacher_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
