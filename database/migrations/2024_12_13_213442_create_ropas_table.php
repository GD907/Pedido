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
        Schema::create('ropas', function (Blueprint $table) {
            $table->id();
            $table->date('fecha'); // Fecha
            $table->string('descripcion'); // Descripción
            $table->unsignedBigInteger('precio'); // Precio
            $table->boolean('cobrado')->default(false); // Cobrado
            $table->softDeletes(); // Soporte para Soft Deletes
            $table->timestamps(); // Campos created_at y updated_at
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ropas');
    }
};
