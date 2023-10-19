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
        Schema::create('presupuestos', function (Blueprint $table) {
            $table->id();
            $table->string('numpero_presupuesto')->nullable();
            $table->timestamp('fecha_emision')->nullable();
            $table->integer('alumno_id')->nullable();
            $table->integer('curso_id')->nullable();
            $table->string('detalles')->nullable();
            $table->decimal('total_sin_iva')->nullable();
            $table->decimal('iva')->nullable();
            $table->decimal('descuento')->nullable();
            $table->decimal('precio')->nullable();
            $table->string('estado')->nullable();
            $table->string('observaciones')->nullable();
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
        Schema::dropIfExists('presupuestos');
    }
};
