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
        Schema::create('cliente', function (Blueprint $table) {
            $table->id();
            $table->enum("trato", ["M", "Melle", "Mme", null]);
            $table->string("nombre");
            $table->string("apellido");
            $table->string("tipoCalle");
            $table->string("calle");
            $table->integer("numero");
            $table->string("direccionAdicional1");
            $table->string("direccionAdicional2");
            $table->string("direccionAdicional3");
            $table->integer("codigoPostal");
            $table->string("ciudad");
            $table->string("nif");
            $table->integer("tlf1");
            $table->integer("tlf2");
            $table->integer("tlf3");
            $table->string("email1");
            $table->string("email2");
            $table->string("email3");
            $table->boolean("confPostal");
            $table->boolean("confEmail");
            $table->boolean("confSms");
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
        Schema::dropIfExists('cliente');
    }
};
