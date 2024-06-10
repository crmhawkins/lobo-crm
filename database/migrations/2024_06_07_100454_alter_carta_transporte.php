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
        Schema::table('carta_transporte', function (Blueprint $table) {
            
            //add formalizado
            $table->string('formalizado')->nullable();
            //remitente_tabla
            $table->string('remitente_tabla')->nullable();
            //moneda_tabla
            $table->string('moneda_tabla')->nullable();
            //consignatario_tabla
            $table->string('consignatario_tabla')->nullable();
            //descuento
            $table->string('descuento_remitente')->nullable();
            $table->string('descuento_consignatario')->nullable();
            $table->string('descuento_moneda')->nullable();
            //lugar_entrega_22
            $table->string('lugar_entrega_22')->nullable();

            //other fields must be nullable
            $table->string('remitente')->nullable()->change();
            $table->string('cargador_contractual')->nullable()->change();
            $table->string('operador_transporte')->nullable()->change();
            $table->string('consignatario')->nullable()->change();
            $table->string('lugar_entrega')->nullable()->change();
            $table->string('lugar_fecha_carga')->nullable()->change();
            $table->string('documentos_anexos')->nullable()->change();
            $table->string('marca_numeros')->nullable()->change();
            $table->string('numero_bultos')->nullable()->change();
            $table->string('clases_embalaje')->nullable()->change();
            $table->string('naturaleza')->nullable()->change();
            $table->string('n_estadistico')->nullable()->change();
            $table->string('peso_bruto')->nullable()->change();
            $table->string('volumen')->nullable()->change();
            $table->string('instrucciones')->nullable()->change();
            $table->string('firma_transportista')->nullable()->change();
            $table->string('vehiculo')->nullable()->change();
            $table->string('porteadores_sucesivos')->nullable()->change();
            $table->string('reembolso')->nullable()->change();
            $table->string('lugar')->nullable()->change();
            $table->string('fecha')->nullable()->change();
            $table->string('precio_remitente')->nullable()->change();
            $table->string('liquido_remitente')->nullable()->change();
            $table->string('suplementos_remitente')->nullable()->change();
            $table->string('gastos_remitente')->nullable()->change();
            $table->string('precio_moneda')->nullable()->change();
            $table->string('liquido_moneda')->nullable()->change();
            $table->string('suplementos_moneda')->nullable()->change();
            $table->string('gastos_moneda')->nullable()->change();
            $table->string('precio_consignatario')->nullable()->change();
            $table->string('liquido_consignatario')->nullable()->change();
            $table->string('suplementos_consignatario')->nullable()->change();
            $table->string('gastos_consignatario')->nullable()->change();
            $table->string('total_remitente')->nullable()->change();
            $table->string('total_consignatario')->nullable()->change();
            $table->string('total_moneda')->nullable()->change();

            $table->string('lugar_entrega_16')->nullable()->change();

            $table->string('porte_pagado')->nullable()->change();
            $table->string('porte_debido')->nullable()->change();

            if (!Schema::hasColumn('carta_transporte', 'remitente_1')) {
                $table->string('remitente_1')->nullable();
            }

            //change porte_pagado y porte_debido a un valor que sea 1 o 0
            $table->boolean('porte_pagado')->default(0)->change();
            $table->boolean('porte_debido')->default(0)->change();
            //lo mismo para remitente y cargador contractual
            $table->boolean('remitente')->default(0)->change();
            $table->boolean('cargador_contractual')->default(0)->change();

            

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('carta_transporte', function (Blueprint $table) {
            $table->dropColumn('formalizado');
            $table->dropColumn('remitente_tabla');
            $table->dropColumn('moneda_tabla');
            $table->dropColumn('consignatario_tabla');
            $table->dropColumn('descuento_remitente');
            $table->dropColumn('descuento_consignatario');
            $table->dropColumn('descuento_moneda');
            $table->dropColumn('lugar_entrega_22');
            $table->dropColumn('remitente_1');

        });

    }
};
