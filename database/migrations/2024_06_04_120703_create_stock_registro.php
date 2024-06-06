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
        Schema::create('stock_registro', function (Blueprint $table) {
            $table->id();
            //stock entrante id
            $table->bigInteger('stock_entrante_id');
            $table->foreign('stock_entrante_id')->references('id')->on('stock_entrante');
            //cantidad
            $table->integer('cantidad');
            //tipo
            $table->string('tipo');
            $table->string('motivo');

            //factura_id
            $table->unsignedBigInteger('factura_id')->nullable();
            $table->foreign('factura_id')->references('id')->on('facturas');
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
        Schema::dropIfExists('stock_registro');
    }
};
