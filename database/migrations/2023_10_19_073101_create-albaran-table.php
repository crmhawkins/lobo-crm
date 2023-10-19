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
        Schema::create('albaranes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('pedido_id');
            $table->bigInteger('num_albaran');
            $table->date('fecha');
            $table->string('observaciones');
            $table->string('estado');
            $table->integer('total_factura');
            $table->timestamps();
        });    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('albaranes');
    }
};
