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
        Schema::table('caja', function (Blueprint $table) {
            $table->string('nFactura')->nullable();
            $table->string('nInterno')->nullable();
            //Tabla para IVAs
            $table->decimal('iva', 10, 2)->nullable();
            //TABLA PARA DESCUENTO
            $table->decimal('descuento', 10, 2)->nullable();
            //TABLA PARA RETENCIÓN
            $table->decimal('retencion', 10, 2)->nullable();
            //TABLA PARA FECHA_VENCIOMIENTO
            $table->date('fechaVencimiento')->nullable();
            //TABLA PARA FECHA_PAGO
            $table->date('fechaPago')->nullable();
            //DEPARTAMENTO
            $table->string('departamento')->nullable();
            //delegacion_id
            $table->unsignedBigInteger('delegacion_id')->nullable();
            $table->foreign('delegacion_id')->references('id')->on('delegaciones');
            //cuenta
            $table->string('cuenta')->nullable();
            //documento_pdf
            $table->string('documento_pdf')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
        //generate rollback
        Schema::table('caja', function (Blueprint $table) {
            if (Schema::hasColumn('caja', 'nFactura')) {
                $table->dropColumn('nFactura');
            }
            if (Schema::hasColumn('caja', 'nInterno')) {
                $table->dropColumn('nInterno');
            }
            if (Schema::hasColumn('caja', 'iva')) {
                $table->dropColumn('iva');
            }
            if (Schema::hasColumn('caja', 'descuento')) {
                $table->dropColumn('descuento');
            }
            if (Schema::hasColumn('caja', 'retencion')) {
                $table->dropColumn('retencion');
            }
            if (Schema::hasColumn('caja', 'fechaVencimiento')) {
                $table->dropColumn('fechaVencimiento');
            }
            if (Schema::hasColumn('caja', 'fechaPago')) {
                $table->dropColumn('fechaPago');
            }
            if (Schema::hasColumn('caja', 'departamento')) {
                $table->dropColumn('departamento');
            }
            if (Schema::hasColumn('caja', 'delegacion_id')) {
                $table->dropForeign('caja_delegacion_id_foreign');
                $table->dropColumn('delegacion_id');
            }
            if (Schema::hasColumn('caja', 'cuenta')) {
                $table->dropColumn('cuenta');
            }
            if (Schema::hasColumn('caja', 'documento_pdf')) {
                $table->dropColumn('documento_pdf');
            }

            
        });


    }
};
