<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->decimal('total_venta', 8, 0)->change();
        });
    }

    public function down()
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->decimal('total_venta', 8, 2)->change();
        });
    }
};
