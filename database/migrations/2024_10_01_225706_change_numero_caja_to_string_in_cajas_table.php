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
            $table->string('numero_caja')->change();
        });
    }

    public function down()
    {
        Schema::table('cajas', function (Blueprint $table) {
            $table->integer('numero_caja')->change();
        });
    }

};
