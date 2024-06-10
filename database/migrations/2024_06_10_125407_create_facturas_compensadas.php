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
        Schema::create('facturas_compensadas', function (Blueprint $table) {
            $table->id();
            //caja_id
            //big integer
            $table->bigInteger('caja_id');
            $table->foreign('caja_id')->references('id')->on('caja');
            //factura_id
            $table->unsignedBigInteger('factura_id');
            $table->foreign('factura_id')->references('id')->on('facturas');
            //importe
            $table->decimal('importe', 10, 2);
            //pagado
            $table->decimal('pagado', 10, 2);
            //pendiente
            $table->decimal('pendiente', 10, 2);
            //fecha
            $table->date('fecha');


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
        Schema::dropIfExists('facturas_compensadas');
    }
};
