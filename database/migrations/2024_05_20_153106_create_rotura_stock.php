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
        Schema::create('rotura_stock', function (Blueprint $table) {
            $table->id();
            //stock id que hace referencia a id en la tabla stock
            $table->bigInteger('stock_id');
            $table->foreign('stock_id')->references('id')->on('stock');
            //cantidad
            $table->integer('cantidad');
            //observaciones
            $table->string('observaciones')->nullable();
            //fecha
            $table->date('fecha');
            //almacen_id
            $table->bigInteger('almacen_id');
            $table->foreign('almacen_id')->references('id')->on('almacenes');
            //user_id
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');


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
        Schema::dropIfExists('rotura_stock');
    }
};
