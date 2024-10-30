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
        Schema::table('cajas', function (Blueprint $table) {
            $table->integer('cantidad_trx')->nullable()->change(); // Hacer que el campo sea nullable
        });
    }

    public function down()
    {
        Schema::table('cajas', function (Blueprint $table) {
            $table->integer('cantidad_trx')->nullable(false)->change(); // Revertir nullable en caso de rollback
        });
    }

};
