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
        Schema::create('entrada_detalles', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('sort')->default(0);

            $table->foreignId('entrada_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('producto_id')->nullable()->constrained()->cascadeOnDelete();
            $table->integer('cantidad');
            $table->decimal('preciocompra', 10, 2)->nullable();
            $table->decimal('precioventa', 10, 2)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entrada_detalles');
    }
};
