<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Loan extends Model
{
    use HasFactory;

    /**
     * Kolom yang dapat diisi secara massal.
     * UPDATE: Ditambahkan 'loan_code' untuk sistem validasi otomatis.
     */
    protected $fillable = [
        'user_id',
        'item_id',
        'quantity',
        'reason',
        'status',
        'loan_code',        // ✅ Tambahkan ini untuk menyimpan Token/Kode Unik
        'loan_date',
        'return_date',
        'return_condition', // Status fisik: aman/rusak/hilang
        'rating',           // Skor kepercayaan (1-5)
        'fine_amount',      // Nominal Denda (Rp)
        'return_note',      // Detail Barang Rusak (misal: "Layar Pecah")
        'lost_quantity',    // ✅ Jumlah unit yang rusak/hilang (Angka)
        'fine_status',      // ✅ Status pembayaran denda (unpaid/paid)
        'admin_note',       // Catatan umum admin/toolman

        // ✅ INI WAJIB ADA BIAR DURASI KESIMPAN:
        'duration_amount',  // Angka (misal: 3)
        'duration_unit',    // Satuan (misal: days/hours)
    ];

    /**
     * Relasi: Pinjaman ini dilakukan oleh User (Siswa/Kelas).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi: Pinjaman ini merujuk pada Item (Barang Praktikum) tertentu.
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Boot function untuk generate kode otomatis jika ingin ditaruh di Model.
     * Tapi lebih aman kita handle di Controller saat user klik 'Pinjam'.
     */
}