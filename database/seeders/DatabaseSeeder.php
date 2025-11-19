<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(MySQLUserSeeder::class);
        
        // Admin default
        User::create([
            'username' => 'admin',
            'name' => 'Administrator',
            'photo' => null,
            'email' => null,
            'phone' => null,
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_teacher' => false,
            'status' => 'active',
        ]);

        // Akun tes teacher
        User::create([
            'username' => 'tes',
            'name' => 'AkunTest',
            'photo' => null,
            'email' => null,
            'phone' => null,
            'password' => Hash::make('tes123'),
            'role' => 'teacher',
            'is_teacher' => true,
            'status' => 'active',
        ]);
    }
}
