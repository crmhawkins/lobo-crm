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
        Schema::create('pedidos_comercial', function (Blueprint $table) {
            $table->id();
            $table->string('npedido')->nullable();
            $table->foreignId('cliente_id')->constrained('clientes_comercial');
            $table->foreignId('comercial_id')->constrained('users');
            $table->decimal('precio', 10, 2)->nullable();
            //subtotal y iva
            $table->decimal('subtotal', 10, 2)->nullable();
            $table->decimal('iva', 10, 2)->nullable();
            $table->decimal('total', 10, 2)->nullable();
            $table->string('direccion_entrega')->nullable();
            $table->string('localidad_entrega')->nullable();
            //cod postal entrega
            $table->string('cod_postal_entrega')->nullable();
            //provincia entrega
            $table->string('provincia_entrega')->nullable();
            //observaciones
            $table->text('observaciones')->nullable();
            //fecha
            $table->date('fecha')->nullable();
        
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
        Schema::dropIfExists('pedidos_comercial');
    }
};
