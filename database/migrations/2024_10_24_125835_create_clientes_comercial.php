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
        Schema::create('clientes_comercial', function (Blueprint $table) {
            $table->id();
            //add column comercial_id
            $table->unsignedBigInteger('comercial_id');
            $table->foreign('comercial_id')->references('id')->on('users');
            //add column nombre
            $table->string('nombre');
            // add column cif
            $table->string('cif')->nullable();
            //add column direccion
            $table->string('direccion')->nullable();
            //add column provincia
            $table->string('provincia')->nullable();
            //add column localidad
            $table->string('localidad')->nullable();
            //add column cod_postal
            $table->string('cod_postal')->nullable();
            //add column telefono
            $table->string('telefono')->nullable();
            //add column email
            $table->string('email')->nullable();

            //add softDeletes
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
        Schema::dropIfExists('clientes_comercial');
    }
};
