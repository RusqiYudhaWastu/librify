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
        Schema::table('loans', function (Blueprint $table) {
            // Kolom nominal denda (Default 0 rupiah)
            $table->integer('fine_amount')->default(0)->after('return_condition');
            
            // Kolom catatan detail kerusakan/kehilangan (Contoh: "Layar pecah 1 unit")
            $table->text('return_note')->nullable()->after('fine_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn(['fine_amount', 'return_note']);
        });
    }
};