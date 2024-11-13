<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            // Eliminar el campo actual si es necesario
            $table->dropColumn('estado_pedidos_id');

            // Agregar el nuevo campo como string con valor por defecto
            $table->string('estado_pedido')->default('Pendiente');
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            // Revertir cambios: eliminar estado_pedido y restaurar estado_pedidos_id como integer
            $table->dropColumn('estado_pedido');
            $table->unsignedBigInteger('estado_pedidos_id')->nullable();
        });
    }
};
