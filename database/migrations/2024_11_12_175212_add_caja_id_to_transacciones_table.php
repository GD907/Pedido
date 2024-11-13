<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('transacciones', function (Blueprint $table) {
        $table->unsignedBigInteger('caja_id')->nullable()->after('id'); // Ajusta 'after' según dónde quieras ubicar la columna
        $table->foreign('caja_id')->references('id')->on('cajas')->onDelete('set null'); // onDelete('set null') o el comportamiento que prefieras
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transacciones', function (Blueprint $table) {
            //
        });
    }
};
