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
        //eliminar columna stock_id y añadir columna tipo
        Schema::table('stock_mercaderia_entrante', function (Blueprint $table) {

            if (Schema::hasColumn('stock_mercaderia_entrante', 'stock_id')) {
                $table->dropColumn('stock_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //añadir columna stock_id y eliminar columna tipo
        Schema::table('stock_mercaderia_entrante', function (Blueprint $table) {
            $table->unsignedBigInteger('stock_id')->nullable();
            $table->foreign('stock_id')->references('id')->on('stock');
        });
    }
};
