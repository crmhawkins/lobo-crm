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
        Schema::create('productos_pedido_comercial', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->constrained('pedidos_comercial');
            $table->foreignId('producto_id')->constrained('productos');
            $table->integer('cantidad');
            //precio ud
            $table->decimal('precio_ud', 10, 2);
            //precio total
            $table->decimal('precio_total', 10, 2);

            //soft delete
            $table->softDeletes();
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
        Schema::dropIfExists('productos_pedido_comercial');
    }
};
