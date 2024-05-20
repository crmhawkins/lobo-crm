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
        //añadir columna 
        Schema::table('facturas', function (Blueprint $table) {
            //subtotal
            $table->decimal('subtotal_pedido', 10, 2);
            //iva total
            $table->decimal('iva_total_pedido', 10, 2);
            //descuento total
            $table->decimal('descuento_total_pedido', 10, 2);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //drop columnas
        Schema::table('facturas', function (Blueprint $table) {
            $table->dropColumn('subtotal_pedido');
            $table->dropColumn('iva_total_pedido');
            $table->dropColumn('descuento_total_pedido');
        });
    }
};
