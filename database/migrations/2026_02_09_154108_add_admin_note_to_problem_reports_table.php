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
        Schema::table('problem_reports', function (Blueprint $table) {
            // Menambahkan kolom admin_note yang boleh kosong (nullable)
            // Diletakkan setelah kolom 'status' biar rapi
            $table->text('admin_note')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('problem_reports', function (Blueprint $table) {
            // Menghapus kolom jika command rollback dijalankan
            $table->dropColumn('admin_note');
        });
    }
};