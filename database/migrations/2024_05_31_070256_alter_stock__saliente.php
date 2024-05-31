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
        //add pedido id to stock_saliente
        Schema::table('stock_salientes', function (Blueprint $table) {
            $table->unsignedBigInteger('pedido_id')->nullable();
            $table->foreign('pedido_id')->references('id')->on('pedidos');
            //add tipo
            $table->string('tipo')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //drop pedido id from stock_saliente
        Schema::table('stock_salientes', function (Blueprint $table) {
            $table->dropForeign('stock_saliente_pedido_id_foreign');
            $table->dropColumn('pedido_id');
            $table->dropColumn('tipo');
        });
    }
};
