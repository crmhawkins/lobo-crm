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
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->string("numero_factura")->nullable();
            $table->integer("id_presupuesto")->nullable();
            $table->string("fecha_emision")->nullable();
            $table->string("fecha_vencimiento")->nullable();
            $table->string("descripcion")->nullable();
            $table->string("estado")->nullable();
            $table->string("metodo_pago")->nullable();
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
        Schema::dropIfExists('facturas');
    }
};
