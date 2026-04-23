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
        Schema::table('items', function (Blueprint $table) {
            // Tambahin 3 kolom baru setelah kolom 'stock'
            $table->string('author')->nullable()->after('stock');
            $table->string('publisher')->nullable()->after('author');
            $table->string('publish_year', 4)->nullable()->after('publisher');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            // Kalau di-rollback, hapus 3 kolom ini
            $table->dropColumn(['author', 'publisher', 'publish_year']);
        });
    }
};