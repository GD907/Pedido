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
        Schema::table('transacciones', function (Blueprint $table) {
            $table->decimal('porcentaje_descuento', 5, 2)->nullable()->after('total_trx');
            $table->decimal('total_con_descuento', 12, 0)->nullable()->after('porcentaje_descuento');
        });
    }

    public function down()
    {
        Schema::table('transacciones', function (Blueprint $table) {
            $table->dropColumn('porcentaje_descuento');
            $table->dropColumn('total_con_descuento');
        });
    }

};
