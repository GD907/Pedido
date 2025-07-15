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
        $table->decimal('total_efectivo_general', 12, 0)->default(0)->after('total_boleta_general');
        $table->decimal('total_tarjeta_general', 12, 0)->default(0)->after('total_efectivo_general');
        $table->decimal('total_transferencia_general', 12, 0)->default(0)->after('total_tarjeta_general');
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
{
    Schema::table('cajas', function (Blueprint $table) {
        $table->dropColumn([
            'total_efectivo_general',
            'total_tarjeta_general',
            'total_transferencia_general',
        ]);
    });
}
};
