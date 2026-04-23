<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    use HasFactory;

    // PENTING: Karena nama model 'ClassRoom' beda dengan nama tabel 'class_rooms',
    // kita wajib kasih tau Laravel secara manual.
    protected $table = 'class_rooms';

    // Kolom mana saja yang boleh diisi lewat formulir/kodingan
    protected $fillable = [
        'name',           // Nama Kelas (Contoh: 10 PPLG 1 atau XII RPL 2)
        'academic_year'   // Tahun Ajaran
    ];

    /**
     * Relasi ke User (Child)
     * Satu Kelas punya BANYAK User (Bisa Siswa, Akun Kelas, atau Wali Kelas).
     */
    public function users()
    {
        return $this->hasMany(User::class, 'class_id');
    }
}