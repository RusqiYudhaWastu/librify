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
            // Menambahkan kolom photo_path yang boleh kosong (nullable)
            // Diletakkan setelah kolom description agar rapi di database
            $table->string('photo_path')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('problem_reports', function (Blueprint $table) {
            // Hapus kolom jika rollback
            $table->dropColumn('photo_path');
        });
    }
};