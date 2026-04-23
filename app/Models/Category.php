<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal.
     */
    protected $fillable = ['name'];

    /**
     * Relasi: Satu kategori bisa diakses oleh banyak jurusan (Many-to-Many).
     * Ini adalah kunci keamanan filter barang per jurusan.
     */
    public function departments()
    {
        return $this->belongsToMany(Department::class, 'category_department');
    }

    /**
     * Relasi: Satu kategori memiliki banyak barang (One-to-Many).
     * Digunakan untuk menampilkan daftar inventaris di modal detail.
     */
    public function items()
{
    return $this->belongsToMany(Item::class, 'category_item');
}
}