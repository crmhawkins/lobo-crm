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
        //add banco_id to cuadro_flujo
        Schema::table('cuadro_flujo', function (Blueprint $table) {
            $table->unsignedBigInteger('banco_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cuadro_flujo', function (Blueprint $table) {
            $table->dropColumn('banco_id');
        });
    }
};
