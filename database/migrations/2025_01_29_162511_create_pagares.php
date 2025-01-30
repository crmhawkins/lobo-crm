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
        Schema::create('pagares', function (Blueprint $table) {
            $table->id();
            $table->BigInteger('caja_id');
            $table->foreign('caja_id')->references('id')->on('caja');

            $table->integer('nPagos')->nullable();
            $table->date('fecha_efecto')->nullable();
            //add NEfecto text
            $table->text('nEfecto')->nullable();
            //foreign banco_id
            $table->unsignedBigInteger('banco_id')->nullable();
            $table->foreign('banco_id')->references('id')->on('bancos');
            $table->string('estado')->nullable();
            //softdelete
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
        Schema::dropIfExists('pagares');
    }
};
