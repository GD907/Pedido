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
        Schema::table('pedidos', function (Blueprint $table) {
            $table->enum('cobrado_por', ['caja', 'reparto'])
                ->nullable()
                ->after('estado_pedido');

            $table->foreignId('caja_id')
                ->nullable()
                ->after('cobrado_por')
                ->constrained('cajas')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropForeign(['caja_id']);
            $table->dropColumn(['cobrado_por', 'caja_id']);
        });
    }
};
