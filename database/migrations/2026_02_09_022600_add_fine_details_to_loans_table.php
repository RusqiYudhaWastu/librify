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
            // 1. Kolom jumlah unit yang rusak atau hilang (Input Angka)
            // Default 0 karena kalau kondisi 'aman', ini tidak terisi.
            $table->integer('lost_quantity')->default(0)->after('return_condition'); 

            // 2. Kolom status pembayaran denda
            // 'unpaid' = Belum Lunas (Merah), 'paid' = Lunas (Hijau)
            $table->enum('fine_status', ['unpaid', 'paid'])->default('unpaid')->after('fine_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn(['lost_quantity', 'fine_status']);
        });
    }
};