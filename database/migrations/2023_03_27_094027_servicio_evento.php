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
        Schema::create('servicio_evento', function (Blueprint $table) {
            $table->id();
            $table->integer("id_servicio");
            $table->integer("id_evento");
            $table->time("horaInicio");
            $table->time("horaMontaje");
            $table->integer("tiempo");
            $table->date("dia");
            $table->integer("numMonitores");
            $table->float("importe");
            $table->float("importeBase");
            $table->integer("descuento");
            $table->integer("tiempoMontaje");
            $table->integer("tiempoDesmontaje");
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
        Schema::dropIfExists('servicio_evento');
    }
};
