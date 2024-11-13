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
            $table->unsignedBigInteger('estado_pedidos_id')->default(1)->change();
        });
    }

    public function down()
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->unsignedBigInteger('estado_pedidos_id')->default(null)->change();
        });
    }
};
