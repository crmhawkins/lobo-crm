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
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('direccionenvio')->nullable();
            $table->string('provinciaenvio')->nullable();
            $table->string('localidadenvio')->nullable();
            $table->string('codPostalenvio')->nullable();
            $table->tinyInteger('usarDireccionEnvio')->nullable();
            $table->integer('vencimiento_factura_pref')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn('direccionenvio');
            $table->dropColumn('provinciaenvio');
            $table->dropColumn('localidadenvio');
            $table->dropColumn('codPostalenvio');
            $table->dropColumn('usarDireccionEnvio');
            $table->dropColumn('vencimiento_factura_pref');
        });
    }
};
