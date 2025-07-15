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
    Schema::table('cajas', function (Blueprint $table) {
        $table->decimal('total_caja', 15, 2)->change();
    });
}

public function down(): void
{
    Schema::table('cajas', function (Blueprint $table) {
        $table->decimal('total_caja', 8, 2)->change();
    });
}
};
