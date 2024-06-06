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
        //add column pedido_id
        Schema::table('stock_registro', function (Blueprint $table) {
            $table->unsignedBigInteger('pedido_id')->nullable();
            $table->foreign('pedido_id')->references('id')->on('pedidos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //drop column pedido_id
        Schema::table('stock_registro', function (Blueprint $table) {
            $table->dropForeign(['pedido_id']);
            $table->dropColumn('pedido_id');
        });
    }
};
