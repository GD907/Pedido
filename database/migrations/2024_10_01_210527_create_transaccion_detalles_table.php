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
        Schema::create('transaccion_detalles', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('sort')->default(0);
            $table->foreignId('transacciones_id');
            $table->foreignId('producto_id');
            $table->integer('cantidad');
            $table->integer('disponible');
            $table->decimal('precio', 8, 2);
            $table->decimal('pordescuento', 8, 2)->default(0);
            $table->decimal('subtotal', 8, 2);
            $table->foreign('transacciones_id')->references('id')->on('transacciones');
            $table->foreign('producto_id')->references('id')->on('productos');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaccion_detalles');
    }
};
