<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_logs', function (Blueprint $table) {
            $table->id();

            // 1. Target Class: SET NULL
            // Agar jika kelas dihapus, log-nya tetap ada (class_id jadi NULL)
            $table->foreignId('class_id')
                    ->nullable()
                    ->constrained('classes')
                    ->nullOnDelete();

            // 2. Actor (User): SET NULL
            $table->foreignId('user_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

            $table->string('action'); // CREATE, UPDATE, DELETE
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_logs');
    }
};