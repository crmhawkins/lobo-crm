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
        Schema::create('alumnos', function (Blueprint $table) {
            $table->id();
            $table->integer("empresa_id")->nullable();
            $table->string("nombre");
            $table->string("apellidos")->nullable();
            $table->string("dni")->nullable();
            $table->timestamp("fecha_nac")->nullable();
            $table->string("direccion")->nullable();
            $table->string("localidad")->nullable();
            $table->string("provincia")->nullable();
            $table->string("cod_postal")->nullable();
            $table->string("pais")->nullable();
            $table->integer("telefono")->nullable();
            $table->integer("movil")->nullable();
            $table->string("email")->nullable();
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
        Schema::dropIfExists('alumnos');
    }
};
