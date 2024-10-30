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
        Schema::create('entradas', function (Blueprint $table) {
        $table->id();
        $table->dateTime('fecha');
        $table->unsignedBigInteger('users_id'); // Agregar el campo users_id
        $table->foreign('users_id')->references('id')->on('users'); // Definir la clave foránea
        $table->string('observacion')->nullable();
        $table->softDeletes();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entradas');
    }
};
