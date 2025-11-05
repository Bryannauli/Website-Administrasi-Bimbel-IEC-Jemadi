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
            $table->enum('category', ['pre_level', 'level', 'step', 'private']);
            // Nama kelas: "Step 1", "Private Student", "Level 3"
            $table->string('name', 100);
            // Classroom: China, Italy, France, ...
            $table->string('classroom', 50);
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('status', ['active', 'inactive'])->default('active');

            // Relasi ke guru: form teacher dan local teacher
            $table->foreignId('form_teacher_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('local_teacher_id')->nullable()->constrained('users')->nullOnDelete();

            $table->enum('start_month', [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ]);
            $table->enum('end_month', [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ]);
            $table->year('academic_year');
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
