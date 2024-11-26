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
        //create table documentos_acuerdos_comerciales
        Schema::create('documentos_acuerdos_comerciales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('acuerdo_comercial_id');
            $table->foreign('acuerdo_comercial_id')->references('id')->on('acuerdos_comerciales');
            $table->string('ruta');
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
        Schema::dropIfExists('documentos_acuerdos_comerciales');
    }
};
