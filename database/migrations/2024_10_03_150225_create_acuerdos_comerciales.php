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
        Schema::create('acuerdos_comerciales', function (Blueprint $table) {
            $table->id();
            // Add soft deletes
            $table->softDeletes();
            
            // Add column user_id to acuerdos_comerciales
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            
            // Add column cliente_id to acuerdos_comerciales
            $table->unsignedBigInteger('cliente_id');
            $table->foreign('cliente_id')->references('id')->on('clientes');

            // Add other columns
            $table->string('nAcuerdo');
            $table->string('nombre_empresa')->nullable();
            $table->string('cif_empresa')->nullable();
            $table->string('nombre')->nullable();
            $table->string('dni')->nullable();
            $table->string('email')->nullable();
            $table->string('telefono')->nullable();
            $table->string('domicilio')->nullable();
            $table->string('establecimiento')->nullable();
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->text('prductos_lobo')->nullable();
            $table->text('productos_otros')->nullable();
            $table->text('marketing')->nullable();
            $table->text('observaciones')->nullable();
            $table->text('firma_comercial_lobo')->nullable();
            $table->text('firma_comercial')->nullable();
            $table->text('firma_cliente')->nullable();
            $table->text('firma_distribuidor')->nullable();
            
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
        Schema::dropIfExists('acuerdos_comerciales');
    }
};
