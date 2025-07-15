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
            $table->integer('contador_ropas_canceladas')->default(0)->after('contador_cancelados');
            $table->integer('contador_pedidos_cancelados')->default(0)->after('contador_ropas_canceladas');
        });
    }

    public function down(): void
    {
        Schema::table('cajas', function (Blueprint $table) {
            $table->dropColumn(['contador_ropas_canceladas', 'contador_pedidos_cancelados']);
        });
    }
};
