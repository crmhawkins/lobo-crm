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
        Schema::create('subalmacenes', function (Blueprint $table) {
            $table->id();
            //add colum almacen_id
            $table->BigInteger('almacen_id');
            $table->foreign('almacen_id')->references('id')->on('almacenes');
            //add colum nombre
            $table->string('almacen')->nullable();
            //add column direccion
            $table->string('direccion')->nullable();
            //add column horario
            $table->string('horario')->nullable();
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
        Schema::dropIfExists('subalmacenes');
    }
};
