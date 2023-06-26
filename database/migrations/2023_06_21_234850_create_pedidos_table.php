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
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->dateTime('fecha');
            $table->string('numero_factura');
            $table->string('observacion')->nullable();
            $table->unsignedBigInteger('users_id');
            $table->unsignedBigInteger('clientes_id');
            $table->unsignedBigInteger('estado_pedidos_id');
            $table->decimal('total_venta', 8, 2);
            $table->foreign('users_id')->references('id')->on('users');
            $table->foreign('estado_pedidos_id')->references('id')->on('estado_pedidos');
            $table->foreign('clientes_id')->references('id')->on('clientes');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
