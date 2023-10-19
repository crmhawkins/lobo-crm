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
        Schema::create('eventos', function (Blueprint $table) {
            $table->id();
            $table->string("eventoNombre");
            $table->string("eventoProtagonista")->nullable();
            $table->integer("eventoNiños");
            $table->string("eventoContacto");
            $table->string("eventoAdulto");
            // $table->string("id_contacto");
            $table->string("eventoParentesco")->nullable();
            $table->integer("eventoTelefono")->nullable();
            $table->string("eventoLugar")->nullable();
            $table->string("eventoLocalidad")->nullable();
            $table->string("eventoMontaje")->nullable();
            $table->date("diaEvento");
            $table->date("diaFinal");
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
        Schema::dropIfExists('eventos');
    }
};
