<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // =========================================================
        // URUTAN 1: Tabel Class Rooms (Ganti nama dari classes biar match sama Model ClassRoom)
        // =========================================================
        Schema::create('class_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Contoh: XII IPA 1, 10 PPLG 2
            $table->string('academic_year')->nullable(); // Contoh: 2025/2026
            $table->timestamps();
        });

        // =========================================================
        // URUTAN 2: Tabel Users
        // =========================================================
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name'); 
            $table->string('email')->unique(); 
            
            $table->string('username')->nullable()->unique(); 
            $table->string('nisn')->nullable()->unique();     
            
            // Role sesuai Librify
            $table->enum('role', ['admin', 'staff', 'teacher', 'class', 'student'])->default('student');
            
            // ✅ ADDED: Status untuk Sistem Approval
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            
            // Relasi ke tabel class_rooms
            $table->foreignId('class_id')->nullable()->constrained('class_rooms')->onDelete('set null');

            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        // =========================================================
        // Tabel Bawaan Laravel (Reset Password & Sessions)
        // =========================================================
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus harus dibalik urutannya (Child dulu baru Parent)
        Schema::dropIfExists('users');
        Schema::dropIfExists('class_rooms');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};