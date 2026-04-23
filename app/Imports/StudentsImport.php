<?php

namespace App\Imports;

use App\Models\User;
use App\Models\ClassRoom;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows; // ✅ Tambahan biar rapi
use Maatwebsite\Excel\Concerns\SkipsOnError;    // ✅ Tambahan biar gak crash
use Maatwebsite\Excel\Concerns\SkipsErrors;     // ✅ Tambahan biar gak crash

class StudentsImport implements ToModel, WithHeadingRow, SkipsEmptyRows, SkipsOnError
{
    use SkipsErrors;

    protected $class_id;
    protected $department_id;

    public function __construct($class_id)
    {
        $this->class_id = $class_id;
        $kelas = ClassRoom::find($class_id);
        $this->department_id = $kelas ? $kelas->department_id : null;
    }

    public function model(array $row)
    {
        // 1. Abaikan kalau barisnya kosong atau gak lengkap
        if (empty($row['nama']) || empty($row['username']) || empty($row['email'])) {
            return null;
        }

        // 2. CEK DUPLIKAT MANUAL
        // Biar database gak nendang error, kita cek dulu di PHP
        $exists = User::where('email', $row['email'])
                      ->orWhere('username', $row['username']);
        
        if (!empty($row['nisn'])) {
            $exists->orWhere('nisn', $row['nisn']);
        }

        // Kalau datanya udah ada (kembar), return null (SKIP baris ini)
        if ($exists->exists()) {
            return null; 
        }

        // 3. Kalau datanya unik, baru di-insert
        return new User([
            'name'          => $row['nama'],
            'username'      => $row['username'],
            'email'         => $row['email'],
            'nisn'          => $row['nisn'] ?? null,
            'password'      => Hash::make($row['password']),
            'role'          => 'student', 
            'class_id'      => $this->class_id,
            'department_id' => $this->department_id,
        ]);
    }
}