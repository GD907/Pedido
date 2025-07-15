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
        Schema::table('cierre_dias', function (Blueprint $table) {
            $table->string('aquipago_lote')->nullable()->after('aquipago_comision');
        });
    }

    public function down(): void
    {
        Schema::table('cierre_dias', function (Blueprint $table) {
            $table->dropColumn('aquipago_lote');
        });
    }
};
