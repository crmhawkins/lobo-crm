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
        Schema::create('stock_salientes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('stock_entrante_id');
            $table->foreign('stock_entrante_id')->references('id')->on('stock_entrante')->onDelete('cascade');
            $table->integer('producto_id');
            $table->integer('cantidad_salida');
            $table->timestamp('fecha_salida');
            $table->string('motivo_salida')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_salientes');
    }
};
