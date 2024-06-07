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
        //add gastos_envio
        Schema::table('facturas', function (Blueprint $table) {
            $table->decimal('gastos_envio', 10, 2)->nullable();
            //transporte
            $table->string('transporte')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('facturas', function (Blueprint $table) {
            $table->dropColumn('gastos_envio');
            $table->dropColumn('transporte');
        });
    }
};
