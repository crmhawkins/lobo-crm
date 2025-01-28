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
        //add column provincia y codigopostal
        Schema::table('direcciones', function (Blueprint $table) {
            $table->string('provincia')->nullable();
            $table->string('codigopostal')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //drop column provincia y codigopostal
        Schema::table('direcciones', function (Blueprint $table) {
            $table->dropColumn('provincia');
            $table->dropColumn('codigopostal');
        });
    }
};
