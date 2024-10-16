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
        Schema::create('productos_marketing', function (Blueprint $table) {
            $table->id();

            //add column nombre
            $table->string('nombre');
            //add column description nullable
            $table->text('description')->nullable();
            //add column materiales nullable
            $table->text('materiales')->nullable();
            //add column peso_neto_unidad nullable
            $table->decimal('peso_neto_unidad', 10, 2)->nullable();
            //add column unidades_por_caja nullable
            $table->integer('unidades_por_caja')->nullable();
            //add column cajas_por_pallet nullable
            $table->integer('cajas_por_pallet')->nullable();
            //add column foto_ruta nullable
            $table->string('foto_ruta')->nullable();

            //softDeletes   
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
        Schema::dropIfExists('productos_marketing');
    }
};
