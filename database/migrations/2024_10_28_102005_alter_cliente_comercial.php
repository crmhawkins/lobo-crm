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
        //alter cliente_comercial
        Schema::table('clientes_comercial', function (Blueprint $table) {
            //add column distribuidor_id
            $table->unsignedBigInteger('distribuidor_id')->nullable();
            //add foreign key
            $table->foreign('distribuidor_id')->references('id')->on('clientes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //drop foreign key
        Schema::table('cliente_comercial', function (Blueprint $table) {
            $table->dropForeign(['distribuidor_id']);
            $table->dropColumn('distribuidor_id');
        });
    }
};
