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
        Schema::create('producto_precio_cliente', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('producto_id');
            //foreign key
            $table->foreign('producto_id')->references('id')->on('productos');
            $table->unsignedBigInteger('cliente_id');
            //foreign key
            $table->foreign('cliente_id')->references('id')->on('clientes');
            $table->decimal('precio', 10, 2);
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
        Schema::dropIfExists('producto_precio_cliente');
    }
};
