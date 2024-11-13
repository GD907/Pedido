<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            // Elimina la restricción de clave foránea en estado_pedidos_id
            $table->dropForeign(['estado_pedidos_id']);

            // Luego elimina la columna estado_pedidos_id si ya no la necesitas
            $table->dropColumn('estado_pedidos_id');
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            // Restaurar el campo y la clave foránea en caso de reversión
            $table->unsignedBigInteger('estado_pedidos_id')->nullable();
            $table->foreign('estado_pedidos_id')->references('id')->on('estado_pedidos')->onDelete('cascade');
        });
    }
};
