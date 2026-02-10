<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    use HasFactory;

    // PENTING: Karena nama model 'ClassRoom' beda dengan nama tabel 'classes',
    // kita wajib kasih tau Laravel secara manual.
    protected $table = 'classes';

    // Kolom mana saja yang boleh diisi lewat formulir/kodingan
    protected $fillable = [
        'department_id',  // ✅ NEW: Relasi ke Jurusan (Parent)
        'name',           // Nama Kelas (Contoh: 10 PPLG 1)
        'academic_year'   // Tahun Ajaran
    ];

    /**
     * Relasi ke Jurusan (Parent)
     * Satu Kelas milik Satu Jurusan.
     * Contoh: Kelas '10 PPLG 1' milik Jurusan 'PPLG'.
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * Relasi ke Siswa (Child)
     * Satu Kelas punya BANYAK Siswa (User).
     */
    public function users()
    {
        return $this->hasMany(User::class, 'class_id');
    }
}