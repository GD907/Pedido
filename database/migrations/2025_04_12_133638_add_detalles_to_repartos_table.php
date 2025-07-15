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
        Schema::table('repartos', function (Blueprint $table) {
            $table->unsignedInteger('cantidad_pedidos')->default(0)->after('procesado');
            $table->unsignedBigInteger('monto_total')->default(0)->after('cantidad_pedidos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('repartos', function (Blueprint $table) {
            $table->dropColumn('cantidad_pedidos');
            $table->dropColumn('monto_total');
        });
    }
};
