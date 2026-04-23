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
            // Nambahin kolom durasi setelah status
            $table->integer('duration_amount')->nullable()->after('status'); 
            $table->string('duration_unit')->nullable()->after('duration_amount'); // 'hours' atau 'days'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn(['duration_amount', 'duration_unit']);
        });
    }
};