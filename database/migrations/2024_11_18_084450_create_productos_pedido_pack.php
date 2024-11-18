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
        Schema::create('productos_pedido_pack', function (Blueprint $table) {
            $table->id();
            //add column pedido_id
            $table->foreignId('pedido_id')->constrained('pedidos');
            //add column producto_id
            $table->foreignId('producto_id')->constrained('productos');
            //add column pack_id
            $table->foreignId('pack_id')->constrained('productos');
            //add column unidades
            $table->integer('unidades');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('productos_pedido_pack');
    }
};
