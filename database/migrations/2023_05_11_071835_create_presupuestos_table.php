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
        Schema::create('presupuestos', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("id_evento");
            $table->bigInteger("id_cliente");
            $table->float("precioBase");
            $table->float("precioFinal");
            $table->integer("descuento");
            $table->integer("adelanto");
            $table->text("observaciones");
            $table->date("fechaEmision");
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
        Schema::dropIfExists('presupuestos');
    }
};
