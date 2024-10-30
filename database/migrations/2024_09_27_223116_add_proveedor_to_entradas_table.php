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
        Schema::table('entradas', function (Blueprint $table) {
            $table->string('proveedor')->nullable()->after('fecha'); // Agregar el campo proveedor después de fecha
        });
    }

    public function down()
    {
        Schema::table('entradas', function (Blueprint $table) {
            $table->dropColumn('proveedor');
        });
    }

};
