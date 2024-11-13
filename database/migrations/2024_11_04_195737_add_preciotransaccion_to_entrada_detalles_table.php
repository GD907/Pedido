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
        Schema::table('entrada_detalles', function (Blueprint $table) {
            $table->decimal('preciotransaccion', 12, 0)->nullable()->after('precioventa')->comment('Precio de transacción en guaraníes');
        });
    }

    public function down()
    {
        Schema::table('entrada_detalles', function (Blueprint $table) {
            $table->dropColumn('preciotransaccion');
        });
    }
};
