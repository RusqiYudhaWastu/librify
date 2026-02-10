<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi untuk membuat tabel pivot Department User.
     */
    public function up(): void
    {
        Schema::create('department_user', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel users (Toolman)
            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete(); // Jika user dihapus, data di pivot ini hilang
            
            // Relasi ke tabel departments (Jurusan)
            $table->foreignId('department_id')
                  ->constrained()
                  ->cascadeOnDelete(); // Jika jurusan dihapus, data di pivot ini hilang

            $table->timestamps();
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('department_user');
    }
};