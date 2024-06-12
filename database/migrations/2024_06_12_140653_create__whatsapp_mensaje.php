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
        Schema::create('whatsapp_mensaje', function (Blueprint $table) {
            $table->id();
            //id mensaje varchar 255
            $table->string('id_mensaje', 255)->nullable();
            //remitente varchar 255
            $table->string('remitente', 255)->nullable();
            //mensaje text
            $table->text('mensaje')->nullable();
            //status tinyint 4
            $table->tinyInteger('status')->nullable();
            
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
        Schema::dropIfExists('whatsapp_mensaje');
    }
};
