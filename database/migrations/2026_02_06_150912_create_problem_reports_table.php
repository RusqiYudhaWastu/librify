<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi untuk menciptakan tabel laporan kendala.
     */
    public function up(): void
    {
        Schema::create('problem_reports', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke Siswa yang melapor
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade');
            
            // Relasi ke Barang yang bermasalah
            $table->foreignId('item_id')
                  ->constrained()
                  ->onDelete('cascade');
            
            // Detail kendala/masalah yang dialami
            $table->text('description');
            
            // Status penanganan oleh Admin/Toolman
            $table->enum('status', ['pending', 'checked', 'fixed'])
                  ->default('pending');
            
            $table->timestamps();
        });
    }

    /**
     * Batalkan migrasi (Rollback).
     */
    public function down(): void
    {
        Schema::dropIfExists('problem_reports');
    }
};