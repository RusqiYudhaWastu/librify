<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    /**
     * Kolom yang dapat diisi secara massal.
     */
    protected $fillable = [
        'category_id', 
        'name',
        'asset_code',
        'stock',
        'status',
        'description',
        'maintenance_date',
        'maintenance_note',
        'image' // ✅ WAJIB ADA: Agar foto bisa disimpan
    ];

    /**
     * Relasi ke Model Category (Satu barang punya satu kategori).
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relasi ke Model Loan (Satu barang bisa dipinjam berkali-kali).
     * Ini digunakan untuk sistem request peminjaman siswa.
     */
    public function loans()
    {
        return $this->hasMany(Loan::class);
    }
}