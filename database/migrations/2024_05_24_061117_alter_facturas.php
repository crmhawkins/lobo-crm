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
        //add factura id
        Schema::table('facturas', function (Blueprint $table) {
            $table->unsignedBigInteger('factura_id')->nullable();
            $table->foreign('factura_id')->references('id')->on('facturas');
            //add factura_rectificativa_id 
            $table->unsignedBigInteger('factura_rectificativa_id')->nullable();
            $table->foreign('factura_rectificativa_id')->references('id')->on('facturas');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //drop factura id
        Schema::table('facturas', function (Blueprint $table) {
            $table->dropForeign(['factura_id']);
            $table->dropColumn('factura_id');
            //drop factura_rectificativa_id
            $table->dropForeign(['factura_rectificativa_id']);
            $table->dropColumn('factura_rectificativa_id');
        });        
    }
};
