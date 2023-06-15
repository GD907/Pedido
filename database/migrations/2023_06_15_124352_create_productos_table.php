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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo');
            $table->string('nombre');
            $table->string('descripcion')->nullable();
            $table->string('proveedor')->nullable();
            $table->decimal('precio', 8, 2);
            $table->integer('stock')->nullable();
            $table->integer('unidades_caja')->nullable();
            $table->unsignedBigInteger('categoria_productos_id');
            $table->foreign('categoria_productos_id')->references('id')->on('categoria_productos');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
