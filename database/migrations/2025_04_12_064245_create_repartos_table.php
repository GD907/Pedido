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
        Schema::create('repartos', function (Blueprint $table) {
            $table->id();

            $table->dateTime('fecha'); // Fecha de reparto
            $table->unsignedBigInteger('creado_por'); // Usuario que crea el reparto
            $table->unsignedBigInteger('repartidor'); // Usuario que hace el reparto

            $table->enum('estado_reparto', ['pendiente', 'cancelado', 'entregado'])->default('pendiente');
            $table->boolean('procesado')->default(false); // Procesado: sí/no

            $table->timestamps();
            $table->softDeletes();

            // Relaciones
            $table->foreign('creado_por')->references('id')->on('users');
            $table->foreign('repartidor')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repartos');
    }
};
