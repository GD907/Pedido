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
            $table->integer('contador_cancelados')->default(0)->after('contador_ediciones');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('cajas', function (Blueprint $table) {
            $table->dropColumn('contador_cancelados');
        });
    }
};

