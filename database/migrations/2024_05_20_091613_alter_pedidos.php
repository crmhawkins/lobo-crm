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
        Schema::table('pedidos', function (Blueprint $table) {
            //subtotal
            $table->decimal('subtotal', 10, 2);
            //iva total
            $table->decimal('iva_total', 10, 2);
            //descuento total
            $table->decimal('descuento_total', 10, 2);

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
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn('subtotal');
            $table->dropColumn('iva_total');
            $table->dropColumn('descuento_total');
        });
    }
};
