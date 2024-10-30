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
        $table->integer('contador_ediciones')->default(0)->after('total_caja');
        $table->string('estado_transaccion')->nullable()->after('contador_ediciones');
    });
}

public function down()
{
    Schema::table('cajas', function (Blueprint $table) {
        $table->dropColumn('contador_ediciones');
        $table->dropColumn('estado_transaccion');
    });
}

};
