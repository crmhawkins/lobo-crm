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
        Schema::create('productos_marketing_pedido_pack', function (Blueprint $table) {
            $table->id();
            //pedido_id
            $table->unsignedBigInteger('pedido_id');
            $table->foreign('pedido_id')->references('id')->on('pedidos');
            //producto_id
            $table->unsignedBigInteger('producto_id');
            $table->foreign('producto_id')->references('id')->on('productos_marketing');
            //pack_id
            $table->unsignedBigInteger('pack_id');
            $table->foreign('pack_id')->references('id')->on('productos');
            //unidades
            $table->integer('unidades');
            //lote_id
            $table->string('lote_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('productos_marketing_pedido_pack');
    }
};
