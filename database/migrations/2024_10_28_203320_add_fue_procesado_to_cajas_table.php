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
        Schema::table('cajas', function (Blueprint $table) {
            $table->integer('fue_procesado')->default(0); // Agrega el campo como entero
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('cajas', function (Blueprint $table) {
            $table->dropColumn('fue_procesado'); // Elimina el campo
        });
    }
};
