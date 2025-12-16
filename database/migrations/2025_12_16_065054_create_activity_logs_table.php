<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();

            // 1. ACTOR (Siapa yang melakukan?)
            // Menggantikan 'causer' menjadi 'actor'
            $table->nullableMorphs('actor'); 
            // Otomatis bikin: actor_type (string), actor_id (bigint)

            // 2. SUBJECT (Apa yang diubah?)
            $table->nullableMorphs('subject'); 
            // Otomatis bikin: subject_type (string), subject_id (bigint)

            // 3. DETAIL AKSI
            $table->string('event'); // create, update, delete, restore
            $table->string('description')->nullable(); // Deskripsi tambahan

            // 4. DATA LOG (JSON)
            // Menyimpan 'old' dan 'attributes' (new)
            $table->json('properties')->nullable();

            // 5. METADATA TAMBAHAN (Opsional tapi berguna)
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();

            $table->timestamps();
            
            // Indexing untuk performa pencarian log
            $table->index('event');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};