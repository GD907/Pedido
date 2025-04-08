<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('calendario_cheques', function (Blueprint $table) {
            $table->id();
            $table->string('numero_cheque');
            $table->bigInteger('monto'); // Monto en guaraníes, sin decimales
            $table->string('banco');
            $table->date('fecha_emitida');
            $table->date('fecha_vencimiento');
            $table->string('proveedor');
            $table->enum('estado', ['pendiente', 'cobrado', 'rechazado'])->default('pendiente');
            $table->string('firmado_por');
            $table->text('concepto')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calendario_cheques');
    }
};
