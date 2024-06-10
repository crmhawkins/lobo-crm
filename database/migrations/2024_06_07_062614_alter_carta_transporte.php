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
            //add lugar_entrega_16
            $table->string('lugar_entrega_16');
            $table->string('porte_pagado');
            $table->string('porte_debido');
            $table->dropColumn('forma_pago');
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
            $table->dropColumn('lugar_entrega_16');
            $table->dropColumn('porte_pagado');
            $table->dropColumn('porte_debido');
            $table->string('forma_pago');

        });
    }
};
