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
        Schema::create('conceptos_facturas', function (Blueprint $table) {
            $table->id();
            $table->integer("id_factura")->nullable();
            $table->integer("id_producto")->nullable();
            $table->double("precio", 8,2)->nullable();
            $table->integer('cantidad')->nullable();
            $table->integer('iva')->nullable();
            $table->integer('descuento')->nullable();
            $table->double("total", 8,2)->nullable();
            $table->double("total_sin_iva", 8,2)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('conceptos_facturas');
    }
};
