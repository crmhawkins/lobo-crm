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
        Schema::create('whatsapp_mensaje_chatgpt', function (Blueprint $table) {
            $table->id();
            $table->string('id_mensaje', 255)->nullable();
            $table->string('id_three', 255)->nullable();
            $table->string('remitente', 255)->nullable();
            //mensaje type text
            $table->text('mensaje')->nullable();
            //respuesta type text
            $table->text('respuesta')->nullable();
            //status type tinyint
            $table->tinyInteger('status')->nullable();
            //type  type string 255
            $table->string('type', 255)->nullable();

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
        Schema::dropIfExists('whatsapp_mensaje_chatgpt');
    }
};
