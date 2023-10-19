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
        Schema::create('gastos_mensuales', function (Blueprint $table) {
            $table->id();
            $table->date("mes");
            $table->float("personalCentralYSegSoc");
            $table->float("alarma");
            $table->float("seguro");
            $table->float("telefonia");
            $table->float("gestoriaFiscal");
            $table->float("gestoriaLaboral");
            $table->float("alquileres");
            $table->float("bancos");
            $table->float("aberasConsultores");
            $table->float("informatico");
            $table->float("comunidad");
            $table->float("suministros");
            $table->float("digmep");
            $table->float("carlos");
            $table->float("mybox");
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
        Schema::dropIfExists('gastos_mensuales');
    }
};
