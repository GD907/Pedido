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
            $table->decimal('total_trx_efectivo', 15, 0)->default(0)->change();
            $table->decimal('total_trx_tarjeta', 15, 0)->default(0)->change();
            $table->decimal('total_trx_transferencia', 15, 0)->default(0)->change();
            $table->decimal('total_trx_general', 15, 0)->default(0)->change();

            $table->decimal('total_ropa_efectivo', 15, 0)->default(0)->change();
            $table->decimal('total_ropa_tarjeta', 15, 0)->default(0)->change();
            $table->decimal('total_ropa_transferencia', 15, 0)->default(0)->change();
            $table->decimal('total_ropa_general', 15, 0)->default(0)->change();

            $table->decimal('total_boleta_efectivo', 15, 0)->default(0)->change();
            $table->decimal('total_boleta_tarjeta', 15, 0)->default(0)->change();
            $table->decimal('total_boleta_transferencia', 15, 0)->default(0)->change();
            $table->decimal('total_boleta_general', 15, 0)->default(0)->change();

            $table->decimal('total_caja', 15, 0)->default(0)->change();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cajas', function (Blueprint $table) {
            //
        });
    }
};
