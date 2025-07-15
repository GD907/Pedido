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
            $table->decimal('total_efectivo', 12, 0)->default(0)->after('monto_total');
            $table->decimal('total_transferencia', 12, 0)->default(0)->after('total_efectivo');
            $table->decimal('total_tarjeta', 12, 0)->default(0)->after('total_transferencia');
        });
    }

    public function down(): void
    {
        Schema::table('repartos', function (Blueprint $table) {
            $table->dropColumn(['total_efectivo', 'total_transferencia', 'total_tarjeta']);
        });
    }
};
