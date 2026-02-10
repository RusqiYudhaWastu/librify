<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProblemReport extends Model
{
    use HasFactory;

    // Mass assignment protection
    protected $fillable = [
        'user_id',
        'item_id',
        'photo_path',
        'description',
        'status',
        'admin_note',
    ];

    /**
     * Relasi ke User: Satu laporan dimiliki oleh satu Siswa.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Item: Satu laporan merujuk pada satu Barang.
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}