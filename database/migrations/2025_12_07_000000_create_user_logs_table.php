<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_logs', function (Blueprint $table) {
            $table->id();

            // 1. TARGET (User yang datanya berubah)
            // Misal: Akun "Pak Budi" yang diedit passwordnya
            $table->foreignId('user_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

            // 2. ACTOR (Siapa yang mengubah?)
            // Misal: "Admin Andi" yang melakukan pengeditan
            // Kita beri nama beda biar gak bingung, misal 'actor_id'
            $table->foreignId('actor_id')
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
        Schema::dropIfExists('user_logs');
    }
};