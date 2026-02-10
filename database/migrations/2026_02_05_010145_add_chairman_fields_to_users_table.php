<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi untuk menambah kolom.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Menambahkan kolom chairman_name dan vice_chairman_name
            // Kita letakkan setelah class_id agar struktur tabel tetap rapi
            $table->string('chairman_name')->nullable()->after('class_id');
            $table->string('vice_chairman_name')->nullable()->after('chairman_name');
        });
    }

    /**
     * Batalkan migrasi (Rollback).
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Menghapus kolom jika migrasi di-rollback
            $table->dropColumn(['chairman_name', 'vice_chairman_name']);
        });
    }
};