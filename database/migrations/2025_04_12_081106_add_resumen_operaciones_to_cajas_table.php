<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cajas', function (Blueprint $table) {
            // Transacciones
            $table->decimal('total_trx_efectivo', 12, 0)->default(0)->after('fue_procesado');
            $table->decimal('total_trx_tarjeta', 12, 0)->default(0);
            $table->decimal('total_trx_transferencia', 12, 0)->default(0);
            $table->decimal('total_trx_general', 12, 0)->default(0);

            // Ropas
            $table->decimal('total_ropa_efectivo', 12, 0)->default(0);
            $table->decimal('total_ropa_tarjeta', 12, 0)->default(0);
            $table->decimal('total_ropa_transferencia', 12, 0)->default(0);
            $table->decimal('total_ropa_general', 12, 0)->default(0);

            // Boletas
            $table->decimal('total_boleta_efectivo', 12, 0)->default(0);
            $table->decimal('total_boleta_tarjeta', 12, 0)->default(0);
            $table->decimal('total_boleta_transferencia', 12, 0)->default(0);
            $table->decimal('total_boleta_general', 12, 0)->default(0);

            // Cantidades
            $table->integer('cantidad_ventas_ropa')->default(0);
            $table->integer('cantidad_boletas')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('cajas', function (Blueprint $table) {
            $table->dropColumn([
                'total_trx_efectivo', 'total_trx_tarjeta', 'total_trx_transferencia', 'total_trx_general',
                'total_ropa_efectivo', 'total_ropa_tarjeta', 'total_ropa_transferencia', 'total_ropa_general',
                'total_boleta_efectivo', 'total_boleta_tarjeta', 'total_boleta_transferencia', 'total_boleta_general',
                'cantidad_ventas_ropa', 'cantidad_boletas'
            ]);
        });
    }
};
