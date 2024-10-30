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
            $table->integer('cantidad_trx')->after('estado'); // Agregar antes de 'total_caja'
        });
    }

    public function down()
    {
        Schema::table('cajas', function (Blueprint $table) {
            $table->dropColumn('cantidad_trx');
        });
    }

};
