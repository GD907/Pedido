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
        Schema::table('pedido_detalles', function (Blueprint $table) {
            $table->decimal('precio', 10, 0)->change();
            $table->decimal('pordescuento', 10, 0)->change();
            $table->decimal('subtotal', 10, 0)->change();
        });
    }

    public function down()
    {
        Schema::table('pedido_detalles', function (Blueprint $table) {
            $table->decimal('precio', 10, 2)->change();
            $table->decimal('pordescuento', 10, 2)->change();
            $table->decimal('subtotal', 10, 2)->change();
        });
    }
};
