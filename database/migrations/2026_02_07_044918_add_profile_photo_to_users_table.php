<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi untuk menambah kolom foto profil.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Kita taruh setelah email biar urutannya rapi di database
            $table->string('profile_photo')->nullable()->after('email');
        });
    }

    /**
     * Balikkan migrasi (Rollback).
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hapus kolom jika migrasi di-rollback
            $table->dropColumn('profile_photo');
        });
    }
};