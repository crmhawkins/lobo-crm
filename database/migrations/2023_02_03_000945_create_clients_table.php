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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('nameEmpresa')->nullable();
            $table->string('taxNumber')->nullable();
            $table->string('address')->nullable();
            $table->string('email')->nullable();
            $table->string('postCode')->nullable();
            $table->string('ciudad')->nullable();
            $table->string('province')->nullable();
            $table->string('nameCliente')->nullable();
            $table->string('firstSurname')->nullable();
            $table->string('lastSurname')->nullable();
            $table->string('dni')->nullable();
            $table->string('emailCliente')->nullable();
            $table->string('adressCliente')->nullable();
            $table->string('ciudadCliente')->nullable();
            $table->string('provinceCliente')->nullable();
            $table->string('postCodeCliente')->nullable();
            $table->string('tipoCliente')->nullable();
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
        Schema::dropIfExists('clients');
    }
};
