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
        //add column isIngresoProveedor to Caja
        Schema::table('caja', function (Blueprint $table) {
            $table->boolean('isIngresoProveedor')->default(false);

            // Add foreign key to gasto_id nullable 
            $table->BigInteger('gasto_id')->nullable();
            $table->foreign('gasto_id')->references('id')->on('caja');

            

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //drop column isIngresoProveedor from Caja
        Schema::table('caja', function (Blueprint $table) {
            $table->dropColumn('isIngresoProveedor');

            // Drop foreign key to gasto_id nullable
            $table->dropForeign(['gasto_id']);
            $table->dropColumn('gasto_id');
        });
    }
};
