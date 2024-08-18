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
            $table->foreign('pedido_id', 'fk_logs_pedido_id')->references('id')->on('pedidos');
            $table->foreign('factura_id', 'fk_logs_factura_id')->references('id')->on('facturas');
            $table->foreign('albaran_id', 'fk_logs_albaran_id')->references('id')->on('albaranes');
            $table->foreign('caja_id', 'fk_logs_caja_id')->references('id')->on('caja');
            $table->foreign('cliente_id', 'fk_logs_cliente_id')->references('id')->on('clientes');
            $table->foreign('registroemail_id', 'fk_logs_registroemail_id')->references('id')->on('registro_email');
            $table->foreign('modificaciones_mercaderia_id', 'fk_logs_modificaciones_mercaderia_id')->references('id')->on('modificaciones_mercaderia');
            $table->foreign('modificaciones_stock_id', 'fk_logs_modificaciones_stock_id')->references('id')->on('modificaciones_stock');
            $table->foreign('producto_id', 'fk_logs_producto_id')->references('id')->on('productos');
            $table->foreign('proveedor_id', 'fk_logs_proveedor_id')->references('id')->on('proveedores');
            $table->foreign('rotura_stock_id', 'fk_logs_rotura_stock_id')->references('id')->on('rotura_stock');
            $table->foreign('servicios_facturas_id', 'fk_logs_servicios_facturas_id')->references('id')->on('servicios_facturas');
            $table->foreign('stock_id', 'fk_logs_stock_id')->references('id')->on('stock');
            $table->foreign('stock_entrante_id', 'fk_logs_stock_entrante_id')->references('id')->on('stock_entrante');
            $table->foreign('stock_registro_id', 'fk_logs_stock_registro_id')->references('id')->on('stock_registro');
            $table->foreign('user_create_id', 'fk_logs_user_create_id')->references('id')->on('users');
            $table->foreign('logs_action_id', 'fk_logs_logs_action_id')->references('id')->on('log_actions');
        });
    
    }
    

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        

    }
};
