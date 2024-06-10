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
        Schema::create('carta_transporte', function (Blueprint $table) {
            $table->id();

            //add column remitente
            $table->string('remitente');
            //add column cargador_contractual
            $table->string('cargador_contractual');

            //operador de transporte
            $table->string('operador_transporte');

            $table->string('consignatario');

            $table->string('lugar_entrega');

            $table->string('lugar_fecha_carga');

            $table->string('documentos_anexos');

            $table->string('marca_numeros');

            $table->string('numero_bultos');

            $table->string('clases_embalaje');

            $table->string('naturaleza');

            $table->string('n_estadistico');

            $table->string('peso_bruto');

            $table->string('volumen');

            $table->string('instrucciones');

            $table->string('forma_pago');

            //add firma
            $table->string('firma_transportista');

            $table->string('vehiculo');

            $table->string('porteadores_sucesivos');

            $table->string('reembolso');

            $table->string('lugar');

            $table->string('fecha');

            $table->string('precio_remitente');
            $table->string('liquido_remitente');
            $table->string('suplementos_remitente');
            $table->string('gastos_remitente');

            $table->string('precio_moneda');
            $table->string('liquido_moneda');
            $table->string('suplementos_moneda');
            $table->string('gastos_moneda');

            $table->string('precio_consignatario');
            $table->string('liquido_consignatario');
            $table->string('suplementos_consignatario');
            $table->string('gastos_consignatario');

            $table->string('total_remitente');
            $table->string('total_moneda');
            $table->string('total_consignatario');

            //add pedido id
            $table->unsignedBigInteger('pedido_id');
            $table->foreign('pedido_id')->references('id')->on('pedidos');

            

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
        Schema::dropIfExists('carta_transporte');
    }
};
