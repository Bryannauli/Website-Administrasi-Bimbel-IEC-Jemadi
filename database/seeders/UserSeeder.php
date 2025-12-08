<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin default
        User::create([
            'username' => 'admin',
            'name' => 'Fita',
            'photo' => null,
            'email' => null,
            'phone' => null,
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_teacher' => false,
            'is_active' => true,
        ]);

        // Akun tes teacher
        User::create([
            'username' => 'teacher',
            'name' => 'Richard',
            'photo' => null,
            'email' => null,
            'phone' => null,
            'password' => Hash::make('password'),
            'role' => 'teacher',
            'is_teacher' => true,
            'is_active' => true,
        ]);

        // Guru 1: Form Teacher Senior
        User::create([
            'username'   => 'teacher1',
            'name'       => 'Andi Pratama',
            'photo'      => null,
            'email'      => 'andi@school.com',
            'phone'      => '081234567890',
            'password'   => Hash::make('password'),
            'role'       => 'teacher',
            'is_teacher' => true,
            'is_active'  => true,
        ]);

        User::create([
            'username'   => 'teacher2',
            'name'       => 'Bunga Citra',
            'photo'      => null,
            'email'      => 'bunga@school.com',
            'phone'      => '081298765432',
            'password'   => Hash::make('password'),
            'role'       => 'teacher',
            'is_teacher' => true,
            'is_active'  => true,
        ]);

        User::create([
            'username'   => 'teacher3',
            'name'       => 'Chandra Wijaya',
            'photo'      => null,
            'email'      => 'chandra@school.com',
            'phone'      => null,
            'password'   => Hash::make('password'),
            'role'       => 'teacher',
            'is_teacher' => true,
            'is_active'  => true,
        ]);
    }
}