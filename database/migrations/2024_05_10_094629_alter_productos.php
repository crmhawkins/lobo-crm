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
        Schema::table('productos', function (Blueprint $table) {
            //iva_id
            $table->unsignedBigInteger('iva_id')->nullable();
            $table->foreign('iva_id')->references('id')->on('tipos_iva');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //dropColumn
        Schema::table('productos', function (Blueprint $table) {
            $table->dropForeign(['iva_id']);
            $table->dropColumn('iva_id');
        });
    }
};
