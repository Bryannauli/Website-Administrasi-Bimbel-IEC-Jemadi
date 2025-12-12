<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. User & Admin (Wajib pertama biar ada guru/admin)
        $this->call(MySQLUserSeeder::class); 
        $this->call(UserSeeder::class);

        // 2. Kelas (Butuh User/Guru untuk wali kelas)
        $this->call(ClassSeeder::class);

        // 3. Siswa (Butuh Kelas untuk assign class_id)
        $this->call(StudentSeeder::class);

        // 4. Assessment & Speaking Test (Butuh Kelas & Siswa untuk generate resultnya)
        $this->call(AssessmentSeeder::class);

        // 5. Absensi (Butuh Kelas & Siswa untuk digenerate absennya)
        $this->call(AttendanceSeeder::class);
    }
}