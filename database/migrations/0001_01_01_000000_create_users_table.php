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
        // URUTAN 1: Tabel Departments (HARUS PERTAMA KARENA PARENT)
        // =========================================================
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->nullable(); // Slug buat URL friendly
            $table->timestamps();
        });

        // =========================================================
        // URUTAN 2: Tabel Classes (Punya Relasi ke Departments)
        // =========================================================
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Contoh: 10 PPLG 1
            
            // Relasi ke Departments (Wajib ada tabel departments dulu)
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            
            $table->string('academic_year'); // Contoh: 2025/2026
            $table->timestamps();
        });

        // =========================================================
        // URUTAN 3: Tabel Users (Punya Relasi ke Classes & Departments)
        // =========================================================
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name'); 
            $table->string('email')->unique(); 
            
            $table->string('username')->nullable()->unique(); 
            $table->string('nisn')->nullable()->unique();     
            
            $table->enum('role', ['admin', 'toolman', 'class', 'student'])->default('student');
            
            // Relasi ke tabel classes
            $table->foreignId('class_id')->nullable()->constrained('classes')->onDelete('set null');

            // Relasi ke tabel departments (Opsional)
            $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('set null');

            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        // Tabel Bawaan Laravel (Reset Password & Sessions)
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
        Schema::dropIfExists('classes');
        Schema::dropIfExists('departments'); // Hapus departments terakhir
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};