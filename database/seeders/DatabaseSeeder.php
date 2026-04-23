<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ClassRoom;
// Model Department (Jurusan) UDAH DIHAPUS karena Librify bersifat Universal
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
        // 1. Buat Data KELAS Dulu (Nggak butuh relasi ke Jurusan lagi)
        $kelasTKJ = ClassRoom::create([
            'name' => '12 TKJ 1',
            'academic_year' => '2025/2026'
        ]);

        $kelasTKR = ClassRoom::create([
            'name' => '10 TKR 2',
            'academic_year' => '2025/2026'
        ]);

        // 2. Buat Akun ADMIN PERPUSTAKAAN (Kepala Perpus)
        User::create([
            'name' => 'Pak Budi (Kepala Perpus)',
            'email' => 'admin@librify.com',
            'username' => 'admin',
            'role' => 'admin',
            'password' => Hash::make('password'),
            'status' => 'approved', // Wajib approved biar bisa login
        ]);

        // 3. Buat Akun STAFF / PETUGAS (Pengganti Toolman)
        User::create([
            'name' => 'Mas Jono (Petugas)',
            'email' => 'staff@librify.com',
            'username' => 'staff',
            'role' => 'staff',
            'password' => Hash::make('password'),
            'status' => 'approved',
        ]);

       

        // 5. Buat Akun PERWAKILAN KELAS (Akun Kolektif)
        User::create([
            'name' => 'Andi Susanto', // Pake nama ketua kelas sesuai form lu
            'email' => '12tkj1@librify.com',
            'role' => 'class',
            'class_id' => $kelasTKJ->id, // Relasi ke Kelas 12 TKJ 1
            'chairman_name' => 'Andi Susanto', 
            'vice_chairman_name' => 'Rina Melati',
            'password' => Hash::make('password'),
            'status' => 'approved',
        ]);
        
        User::create([
            'name' => 'Dimas Anggara',
            'email' => '10tkr2@librify.com',
            'role' => 'class',
            'class_id' => $kelasTKR->id,
            'chairman_name' => 'Dimas Anggara',
            'password' => Hash::make('password'),
            'status' => 'approved',
        ]);

        // 6. Buat Akun SISWA INDIVIDU (Status Approved - Langsung bisa login)
        User::create([
            'name' => 'Ahmad Siswa (Pribadi)',
            'email' => 'siswa@librify.com',
            'nisn' => '0054812233', 
            'role' => 'student',
            'class_id' => $kelasTKJ->id, 
            'password' => Hash::make('password'),
            'status' => 'approved',
        ]);

        // 7. Buat Akun SISWA INDIVIDU (Status Pending - Buat ngetes UI Approval di Admin)
        User::create([
            'name' => 'Siswa Baru Daftar',
            'email' => 'baru@librify.com',
            'nisn' => '0099887766', 
            'role' => 'student',
            'class_id' => $kelasTKR->id, 
            'password' => Hash::make('password'),
            'status' => 'pending',
        ]);
    }
}