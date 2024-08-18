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
        Schema::table('logs', function (Blueprint $table) {
            $table->unsignedBigInteger('logs_action_id')->nullable();
            $table->unsignedBigInteger('pedido_id')->nullable();
            $table->unsignedBigInteger('factura_id')->nullable();
            $table->unsignedBigInteger('albaran_id')->nullable();
            $table->BigInteger('caja_id')->nullable();
            $table->unsignedBigInteger('cliente_id')->nullable();
            $table->unsignedBigInteger('registroemail_id')->nullable();
            $table->unsignedBigInteger('modificaciones_mercaderia_id')->nullable();
            $table->unsignedBigInteger('modificaciones_stock_id')->nullable();
            $table->unsignedBigInteger('producto_id')->nullable();
            $table->unsignedBigInteger('proveedor_id')->nullable();
            $table->unsignedBigInteger('rotura_stock_id')->nullable();
            $table->unsignedBigInteger('servicios_facturas_id')->nullable();
            $table->BigInteger('stock_id')->nullable();
            $table->BigInteger('stock_entrante_id')->nullable();
            $table->unsignedBigInteger('stock_registro_id')->nullable();
            $table->unsignedBigInteger('user_create_id')->nullable();
        });
    
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //drop columns
        Schema::table('logs', function (Blueprint $table) {
           


        });

    }
};
