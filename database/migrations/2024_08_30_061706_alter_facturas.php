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
        //add column factura_intermedia_id
        Schema::table('facturas', function (Blueprint $table) {
            $table->unsignedBigInteger('factura_intermedia_id')->nullable();
            $table->foreign('factura_intermedia_id')->references('id')->on('facturas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //drop column factura_intermedia_id
        Schema::table('facturas', function (Blueprint $table) {
            $table->dropForeign(['factura_intermedia_id']);
            $table->dropColumn('factura_intermedia_id');
        });
    }
};
