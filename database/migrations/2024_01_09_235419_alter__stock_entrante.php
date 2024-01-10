<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_entrante', function (Blueprint $table) {
            $table->string('orden_numero'); // Añade la columna precio_total
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_entrante', function (Blueprint $table) {
            $table->dropColumn('orden_numero'); // Elimina la columna precio_total
        });
    }
};
