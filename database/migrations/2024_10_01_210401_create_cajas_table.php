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
        Schema::create('cajas', function (Blueprint $table) {
            $table->id();
            $table->dateTime('fecha');
            $table->integer('numero_caja');
            $table->string('observacion')->nullable();
            $table->unsignedBigInteger('users_id');
            $table->string('estado')->default('abierto'); // Por ejemplo: 'abierto' o 'cerrado'
            $table->decimal('total_caja', 8, 2)->default(0);
            $table->dateTime('cierre')->nullable();
            $table->foreign('users_id')->references('id')->on('users');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cajas');
    }
};
