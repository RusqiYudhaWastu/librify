<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug'];

    /**
     * Relasi: Satu jurusan bisa memiliki banyak kategori alat.
     * (Many-to-Many dengan tabel pivot category_department)
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_department');
    }

    /**
     * ✅ UPDATE: Relasi ke Kelas (One-to-Many)
     * Satu Jurusan memiliki BANYAK Kelas.
     * Contoh: Jurusan PPLG punya kelas: 10 PPLG 1, 10 PPLG 2, 11 PPLG 1.
     */
    public function classRooms()
    {
        // Parameter kedua 'department_id' adalah foreign key di tabel classes
        return $this->hasMany(ClassRoom::class, 'department_id');
    }
}