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
        Schema::table('productos', function (Blueprint $table) {
            $table->decimal('preciocompra', 8, 0)->change();
            $table->decimal('precio', 8, 0)->change();
        });
    }

    public function down()
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->decimal('preciocompra', 8, 2)->change();
            $table->decimal('precio', 8, 2)->change();
        });
    }
};
