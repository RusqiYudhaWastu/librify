<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ClassRoom;
use App\Models\Department; // ✅ Jangan lupa import ini
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Buat Data JURUSAN (Department) Dulu (Wajib ada sebelum bikin Kelas)
        $jurusanTKJ = Department::create([
            'name' => 'Teknik Komputer Jaringan',
            'slug' => Str::slug('Teknik Komputer Jaringan')
        ]);

        $jurusanTKR = Department::create([
            'name' => 'Teknik Kendaraan Ringan',
            'slug' => Str::slug('Teknik Kendaraan Ringan')
        ]);

        // 2. Buat Data KELAS (Sekarang pakai department_id)
        $kelasTKJ = ClassRoom::create([
            'name' => '12 TKJ 1',
            'department_id' => $jurusanTKJ->id, // ✅ Relasi ke Jurusan TKJ
            'academic_year' => '2025/2026'
        ]);

        $kelasTKR = ClassRoom::create([
            'name' => '10 TKR 2',
            'department_id' => $jurusanTKR->id, // ✅ Relasi ke Jurusan TKR
            'academic_year' => '2025/2026'
        ]);

        // 3. Buat Akun SUPER ADMIN (Guru)
        User::create([
            'name' => 'Pak Budi (Guru)',
            'email' => 'admin@teknilog.com',
            'username' => 'admin',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        // 4. Buat Akun TOOLMAN (Bisa akses semua/beberapa jurusan)
        $toolman = User::create([
            'name' => 'Mas Jono (Toolman)',
            'email' => 'toolman@teknilog.com',
            'username' => 'toolman',
            'role' => 'toolman',
            'password' => Hash::make('password'),
        ]);
        
        // (Optional) Hubungkan Toolman ke Jurusan (Lewat Pivot Table kalau ada)
        // $toolman->assignedDepartments()->attach([$jurusanTKJ->id, $jurusanTKR->id]);

        // 5. Buat Akun PERWAKILAN KELAS (Ketua Kelas)
        User::create([
            'name' => 'Ketua Kelas 12 TKJ 1',
            'email' => '12tkj1@teknilog.com',
            'role' => 'class',
            'class_id' => $kelasTKJ->id, // Relasi ke Kelas
            'department_id' => $jurusanTKJ->id, // Relasi ke Jurusan (Optional tapi bagus)
            'password' => Hash::make('password'),
        ]);
        
        User::create([
            'name' => 'Ketua Kelas 10 TKR 2',
            'email' => '10tkr2@teknilog.com',
            'role' => 'class',
            'class_id' => $kelasTKR->id,
            'department_id' => $jurusanTKR->id,
            'password' => Hash::make('password'),
        ]);

        // 6. Buat Akun SISWA INDIVIDU
        User::create([
            'name' => 'Ahmad Siswa (Pribadi)',
            'email' => 'siswa@teknilog.com',
            'nisn' => '0054812233', // Contoh NISN
            'role' => 'student',
            'class_id' => $kelasTKJ->id, // Siswa anak 12 TKJ 1
            'password' => Hash::make('password'),
        ]);
    }
}