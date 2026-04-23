<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->integer('maintenance_stock')->default(0)->after('stock');
            $table->integer('broken_stock')->default(0)->after('maintenance_stock');
        });
    }

    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['maintenance_stock', 'broken_stock']);
        });
    }
};