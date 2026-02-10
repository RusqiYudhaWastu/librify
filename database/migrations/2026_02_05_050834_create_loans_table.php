<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi untuk tabel peminjaman (loans).
     */
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel users (Siswa yang meminjam)
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade');
            
            // Relasi ke tabel items (Barang yang dipinjam)
            $table->foreignId('item_id')
                  ->constrained()
                  ->onDelete('cascade');

            // Detail Peminjaman
            $table->integer('quantity')->comment('Jumlah barang yang dipinjam');
            $table->text('reason')->nullable()->comment('Alasan peminjaman, misal: Praktek Pemrograman');
            
            /**
             * Status Peminjaman:
             * pending   : Baru diajukan siswa (Menunggu Toolman)
             * approved  : Disetujui (Barang silakan diambil)
             * rejected  : Ditolak (Alasan bisa di catatan)
             * returned  : Sudah dikembalikan
             */
            $table->enum('status', ['pending', 'approved', 'rejected', 'returned'])
                  ->default('pending');

            // Log Waktu
            $table->timestamp('loan_date')->nullable()->comment('Waktu barang benar-benar diambil');
            $table->timestamp('return_date')->nullable()->comment('Waktu barang dikembalikan');
            
            // Catatan tambahan dari Toolman (Misal: Kenapa ditolak atau kondisi barang pas balik)
            $table->text('admin_note')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};