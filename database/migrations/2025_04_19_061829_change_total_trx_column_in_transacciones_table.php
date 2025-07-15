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
        Schema::table('transacciones', function (Blueprint $table) {
            $table->decimal('total_trx', 9, 0)->change();
        });
    }

    public function down()
    {
        Schema::table('transacciones', function (Blueprint $table) {
            $table->decimal('total_trx', 8, 2)->change();
        });
    }

};
