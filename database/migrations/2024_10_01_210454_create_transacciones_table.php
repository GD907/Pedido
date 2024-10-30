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
        Schema::create('transacciones', function (Blueprint $table) {
            $table->id();
            $table->dateTime('fecha');
            $table->string('numero_trx');
            $table->unsignedBigInteger('users_id');
            $table->unsignedBigInteger('clientes_id')->nullable();
            $table->string('observacion')->nullable();
            $table->decimal('total_trx', 8, 2);
            $table->foreign('users_id')->references('id')->on('users');
            $table->foreign('clientes_id')->references('id')->on('clientes');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transacciones');
    }
};
