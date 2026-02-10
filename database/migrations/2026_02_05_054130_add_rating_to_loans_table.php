<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan perubahan pada tabel loans.
     */
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            // Menambahkan kolom kondisi pengembalian setelah kolom status
            $table->enum('return_condition', ['aman', 'rusak', 'hilang'])
                  ->nullable()
                  ->after('status');

            // Menambahkan kolom rating (1-5) setelah kolom return_condition
            $table->integer('rating')
                  ->nullable()
                  ->after('return_condition');
        });
    }

    /**
     * Batalkan perubahan (Rollback).
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            // Hapus kolom jika migrasi di-rollback
            $table->dropColumn(['return_condition', 'rating']);
        });
    }
};