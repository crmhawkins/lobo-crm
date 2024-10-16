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
        Schema::create('stock_subalmacen', function (Blueprint $table) {
            $table->id();
            //add column subalmacen_id
            $table->unsignedBigInteger('subalmacen_id')->nullable();
            $table->foreign('subalmacen_id')->references('id')->on('subalmacenes');
            //add column producto_id
            $table->unsignedBigInteger('producto_id')->nullable();
            $table->foreign('producto_id')->references('id')->on('productos_marketing');

            //add column cantidad
            $table->decimal('cantidad', 10, 2);
            //add column fecha
            $table->date('fecha');

            $table->string('observaciones')->nullable();

            $table->string('tipo_entrada')->nullable();

            $table->string('tipo_salida')->nullable();

            //softDeletes
            $table->softDeletes();

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
        Schema::dropIfExists('stock_subalmacen');
    }
};
