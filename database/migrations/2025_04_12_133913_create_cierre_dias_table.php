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
        Schema::create('cierre_dias', function (Blueprint $table) {
            $table->id();
            $table->dateTime('fecha_hora_cierre');
            $table->unsignedBigInteger('creado_por');

            $table->unsignedBigInteger('caja_id')->nullable()->default(0);
            $table->unsignedBigInteger('reparto_id')->nullable()->default(0);

            $table->unsignedBigInteger('dinero_inicial')->default(0);

            // Totales finales en caja
            $table->unsignedBigInteger('total_efectivo')->default(0);
            $table->unsignedBigInteger('total_transferencia')->default(0);
            $table->unsignedBigInteger('total_tarjeta')->default(0);
            $table->unsignedBigInteger('total_general')->default(0);

            // Wepa
            $table->string('wepa_lote')->nullable();
            $table->unsignedBigInteger('wepa_ingresos')->default(0);
            $table->unsignedBigInteger('wepa_egresos')->default(0);
            $table->unsignedBigInteger('wepa_cantidad')->default(0);
            $table->unsignedBigInteger('wepa_comision')->default(0);

            // Aquipago
            $table->unsignedBigInteger('aquipago_ingresos')->default(0);
            $table->unsignedBigInteger('aquipago_egresos')->default(0);
            $table->unsignedBigInteger('aquipago_cantidad')->default(0);
            $table->unsignedBigInteger('aquipago_comision')->default(0);

            $table->timestamps();

            $table->foreign('creado_por')->references('id')->on('users');
            $table->foreign('caja_id')->references('id')->on('cajas')->onDelete('set null');
            $table->foreign('reparto_id')->references('id')->on('repartos')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cierre_dias');
    }
};
