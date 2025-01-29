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
        Schema::create('cuadro_flujo', function (Blueprint $table) {
            $table->id();
            //mes
            $table->integer('mes');
            //año
            $table->integer('anio');
            //saldo inicial
            $table->decimal('saldo_inicial', 10, 2);
            //saldo final
            $table->decimal('saldo_final', 10, 2);
            //softdelete
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
        Schema::dropIfExists('cuadro_flujo');
    }
};
