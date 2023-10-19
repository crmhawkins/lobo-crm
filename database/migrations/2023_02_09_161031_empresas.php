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
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->nullable();
            $table->integer('telefono1')->nullable();
            $table->integer('telefono2')->nullable();
            $table->string('email')->nullable();
            $table->string('direccion')->nullable();
            $table->string('cif')->nullable();
            $table->string('cod_postal')->nullable();
            $table->string('localidad')->nullable();
            $table->string('pais')->nullable();
            $table->string('legal1')->nullable();
            $table->string('legal2')->nullable();
            $table->string('legal3')->nullable();
            $table->string('legal4')->nullable();
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
        Schema::dropIfExists('empresas');

    }
};
