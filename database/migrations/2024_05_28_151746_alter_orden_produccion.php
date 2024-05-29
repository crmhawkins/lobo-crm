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
        //add pedido_id to orden_produccion
        Schema::table('orden_produccion', function (Blueprint $table) {
            $table->unsignedBigInteger('pedido_id')->nullable();
            $table->foreign('pedido_id')->references('id')->on('pedidos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //drop pedido_id from orden_produccion
        Schema::table('orden_produccion', function (Blueprint $table) {
            $table->dropForeign('orden_produccion_pedido_id_foreign');
            $table->dropColumn('pedido_id');
        });
    }
};
