<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke Kategori (Penting buat fitur filter & view)
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            
            $table->string('name');
            $table->string('asset_code')->unique();
            $table->integer('stock');
            
            // ✅ KOLOM BARU: FOTO BARANG
            $table->string('image')->nullable(); 

            // Kondisi: ready (Siap Pakai), maintenance, broken (Rusak), lost (Hilang)
            // Gua tambahin 'lost' biar sinkron sama view badge merah tadi
            $table->enum('status', ['ready', 'maintenance', 'broken', 'lost'])->default('ready');
            
            $table->text('description')->nullable();
            
            // Kolom Maintenance
            $table->date('maintenance_date')->nullable();
            $table->text('maintenance_note')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};