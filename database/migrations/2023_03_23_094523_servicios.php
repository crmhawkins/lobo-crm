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
        Schema::create('servicios', function (Blueprint $table) {
            $table->id();
            $table->string("nombre")->nullable();
            $table->float("precioBase")->nullable();
            $table->integer("id_pack")->nullable();
            $table->integer("id_categoria")->nullable();
            $table->integer("minMonitor")->nullable();
            $table->integer("tiempoMontaje")->nullable();
            $table->integer("tiempoDesmontaje")->nullable();
            $table->float("precioMonitor")->nullable();
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
        Schema::dropIfExists('servicios');
    }
};
