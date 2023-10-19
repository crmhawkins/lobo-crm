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
        Schema::create('programa', function (Blueprint $table) {
            $table->id();
            $table->date("dia");
            $table->bigInteger("id_servicioEvento");
            $table->bigInteger("id_monitor");
            $table->time("comienzoMontaje");
            $table->time("comienzoEvento");
            $table->integer("horas");
            $table->float("precioMonitor");
            $table->float("costoDesplazamiento");
            $table->float("pagado");
            $table->integer("descuento");
            $table->string("tiempoDesmontaje");
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
        Schema::dropIfExists('programa');
    }
};
