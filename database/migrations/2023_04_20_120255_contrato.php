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
        Schema::create('contrato', function (Blueprint $table) {
            $table->id();
            $table->date("dia");
            $table->bigInteger("id_presupuesto");
            $table->integer("cuentaTransferencia")->nullable();
            $table->string("metodoPago");
            $table->string("responsableTratamiento");
            $table->boolean("authImagen");
            $table->boolean("authMenores");
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
        Schema::dropIfExists('contrato');
    }
};
