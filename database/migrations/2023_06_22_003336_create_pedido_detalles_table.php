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
        Schema::create('pedido_detalles', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('sort')->default(0);
            $table->foreignId('pedido_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('producto_id')->nullable()->constrained()->cascadeOnDelete();
            $table->integer('cantidad');
            $table->decimal('precio', 10, 2);
            $table->decimal('pordescuento', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedido_detalles');
    }
};
