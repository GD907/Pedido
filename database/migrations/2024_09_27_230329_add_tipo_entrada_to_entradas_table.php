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
            $table->string('tipo_entrada')->nullable()->after('fecha'); // Campo tipo_entrada antes de observacion
        });
    }

    public function down()
    {
        Schema::table('entradas', function (Blueprint $table) {
            $table->dropColumn('tipo_entrada');
        });
    }

};
