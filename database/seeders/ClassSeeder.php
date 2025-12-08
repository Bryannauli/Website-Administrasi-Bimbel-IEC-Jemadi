<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClassModel;
use App\Models\User;
use App\Models\Schedule;

class ClassSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil semua data guru
        $teachers = User::where('role', 'teacher')->get();

        // Pastikan minimal ada 2 guru untuk diacak (untuk form & local)
        if ($teachers->count() == 0) {
            $this->command->info('Tidak ada guru ditemukan. Jalankan TeacherSeeder dulu!');
            return;
        }

        // --- KELAS 1: Level 1 (Senin & Rabu) ---
        // Acak 2 guru berbeda
        $t1_form = $teachers->random(); 
        $t1_local = $teachers->where('id', '!=', $t1_form->id)->random() ?? $teachers->random();

        $class1 = ClassModel::create([
            'category'       => 'level',
            'name'           => 'Level 5',
            'classroom'      => 'Italy',
            'form_teacher_id'=> $t1_form->id,
            'local_teacher_id' => $t1_local->id,
            'start_time'     => '09:00:00',
            'end_time'       => '10:30:00',
            'start_month'    => 'July',
            'end_month'      => 'December',
            'academic_year'  => 2025,
            'is_active'      => true,
        ]);
        
        // Jadwal Kelas 1
        Schedule::create(['class_id' => $class1->id, 'day_of_week' => 'Monday']);
        Schedule::create(['class_id' => $class1->id, 'day_of_week' => 'Wednesday']);


        // --- KELAS 2: Pre-Level (Selasa & Kamis) ---
        // Acak guru lagi
        $t2_form = $teachers->random();
        // Local teacher boleh null atau random (di sini kita buat random)
        $t2_local = $teachers->where('id', '!=', $t2_form->id)->random();

        $class2 = ClassModel::create([
            'category'       => 'pre_level',
            'name'           => 'Pre-Level 5',
            'classroom'      => 'France',
            'form_teacher_id'=> $t2_form->id,
            'local_teacher_id' => $t2_local->id,
            'start_time'     => '16:00:00',
            'end_time'       => '17:30:00',
            'start_month'    => 'July',
            'end_month'      => 'December',
            'academic_year'  => 2025,
            'is_active'      => true,
        ]);

        // Jadwal Kelas 2
        Schedule::create(['class_id' => $class2->id, 'day_of_week' => 'Tuesday']);
        Schedule::create(['class_id' => $class2->id, 'day_of_week' => 'Thursday']);


        // --- KELAS 3: Step (Jumat & Sabtu) ---
        // Acak guru lagi
        $t3_form = $teachers->random();
        
        $class3 = ClassModel::create([
            'category'       => 'step',
            'name'           => 'Step 2',
            'classroom'      => 'China',
            'form_teacher_id'=> $t3_form->id,
            'local_teacher_id' => null, // Contoh kalau kelas ini cuma punya 1 guru
            'start_time'     => '16:00:00',
            'end_time'       => '18:00:00',
            'start_month'    => 'January',
            'end_month'      => 'June',
            'academic_year'  => 2025,
            'is_active'      => true,
        ]);

        // Jadwal Kelas 3
        Schedule::create(['class_id' => $class3->id, 'day_of_week' => 'Friday']);
        Schedule::create(['class_id' => $class3->id, 'day_of_week' => 'Saturday']);
    }
}