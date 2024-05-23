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
        Schema::create('modificaciones_stock', function (Blueprint $table) {
            $table->id();
    
            // Foreign key for stock table (bigint)
           $table->bigInteger('stock_id');
           $table->foreign('stock_id')->references('id')->on('stock');
    
            // Foreign key for user table
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            //almacen_id
            $table->bigInteger('almacen_id');
            $table->foreign('almacen_id')->references('id')->on('almacenes');

            //fecha
            $table->date('fecha');
            // Other columns
            $table->string('tipo');
            $table->integer('cantidad');
            $table->string('motivo');
    
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
        Schema::dropIfExists('modificaciones_stock');
    }
};
