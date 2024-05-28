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
        Schema::create('productos_factura', function (Blueprint $table) {
            $table->id();
            //facturaid
            $table->unsignedBigInteger('factura_id');
            $table->foreign('factura_id')->references('id')->on('facturas');
            //producto id
            $table->unsignedBigInteger('producto_id');
            $table->foreign('producto_id')->references('id')->on('productos');
            //unidades
            $table->integer('cantidad');
            //unidades
            $table->integer('unidades');
            //precio
            $table->decimal('precio_ud', 10, 2);
            //total
            $table->decimal('total', 10, 2);
            //stock_entrante_id
            $table->bigInteger('stock_entrante_id');
            $table->foreign('stock_entrante_id')->references('id')->on('stock_entrante');
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
        Schema::dropIfExists('productos_factura');
    }
};
