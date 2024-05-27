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
        Schema::create('rotura_mercaderia', function (Blueprint $table) {
            $table->id();
            //stock_mercaderia_entrante_id
            $table->bigInteger('stock_mercaderia_entrante_id')
                    ->nullable();
            $table -> foreign('stock_mercaderia_entrante_id')
                    ->references('id')
                    ->on('stock_mercaderia_entrante');

            //fecha
            $table->date('fecha');
            //motivo
            $table->string('motivo')->nullable();
            //cantidad
            $table->integer('cantidad');
            //user_id
            $table->unsignedBigInteger('user_id')
                    ->nullable();
            $table->foreign('user_id')
                    ->references('id')
                    ->on('users');
        

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
        Schema::dropIfExists('rotura_mercaderia');
    }
};
