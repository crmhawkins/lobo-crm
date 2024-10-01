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
        //add column to table caja
        Schema::table('caja', function (Blueprint $table) {
            //add  asientoContable
            $table->string('asientoContable')->nullable();//0001/2024
            //add cuentaContable id
            $table->unsignedBigInteger('cuentaContable_id')->nullable(); 

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //drop column to table caja
        Schema::table('caja', function (Blueprint $table) {
            //drop  asientoContable
            $table->dropColumn('asientoContable');
            //drop cuentaContable id
            $table->dropColumn('cuentaContable_id');
        });
    }
};
