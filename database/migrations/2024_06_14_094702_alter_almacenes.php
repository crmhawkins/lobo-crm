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
        //add column direccion
        Schema::table('almacenes', function (Blueprint $table) {
            $table->string('direccion')->nullable();
            //add horario
            $table->string('horario')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //drop column direccion
        Schema::table('almacenes', function (Blueprint $table) {
            $table->dropColumn('direccion');
            //drop horario
            $table->dropColumn('horario');
        });
    }
};
