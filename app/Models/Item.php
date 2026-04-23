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
        'stock',               // ✅ Stok buku yang SIAP PINJAM / Tersedia
        'maintenance_stock',   // ✅ Jumlah buku yang lagi dirawat / diperbaiki
        'broken_stock',        // ✅ Jumlah buku yang rusak / hilang
        'status',              // Status global (opsional)
        'description',
        'maintenance_date',
        'maintenance_note',
        'image',
        
        // ✅ FIELD BARU UNTUK KATALOG BUKU (LIBRIFY)
        'author',              // Penulis Buku
        'publisher',           // Penerbit Buku
        'publish_year'         // Tahun Terbit
    ];

    /**
     * Relasi ke Model Category (Satu buku punya satu kategori/genre).
     */
   public function categories()
{
    return $this->belongsToMany(Category::class, 'category_item');
}

    /**
     * Relasi ke Model Loan (Satu buku bisa dipinjam berkali-kali).
     * Ini digunakan untuk sistem riwayat peminjaman siswa.
     */
    public function loans()
    {
        return $this->hasMany(Loan::class);
    }
}