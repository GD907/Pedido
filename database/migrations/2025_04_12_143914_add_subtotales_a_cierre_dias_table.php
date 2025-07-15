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
        Schema::table('cierre_dias', function (Blueprint $table) {
            // Subtotales desde Caja
            $table->unsignedBigInteger('caja_efectivo')->default(0);
            $table->unsignedBigInteger('caja_transferencia')->default(0);
            $table->unsignedBigInteger('caja_tarjeta')->default(0);
            $table->unsignedBigInteger('caja_total')->default(0);

            // Subtotales desde Reparto
            $table->unsignedBigInteger('reparto_efectivo')->default(0);
            $table->unsignedBigInteger('reparto_transferencia')->default(0);
            $table->unsignedBigInteger('reparto_tarjeta')->default(0);
            $table->unsignedBigInteger('reparto_total')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('cierre_dias', function (Blueprint $table) {
            $table->dropColumn([
                'caja_efectivo', 'caja_transferencia', 'caja_tarjeta', 'caja_total',
                'reparto_efectivo', 'reparto_transferencia', 'reparto_tarjeta', 'reparto_total',
            ]);
        });
    }
};
