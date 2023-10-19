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
        Schema::create('servicio_presupuesto', function (Blueprint $table) {
            $table->id();
            $table->integer('servicio_id');
            $table->integer('presupuesto_id');
            $table->integer('numero_monitores');
            $table->integer('precio_final');
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
        Schema::dropIfExists('servicio_presupuesto');
    }
};
