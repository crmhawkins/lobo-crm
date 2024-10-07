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
        //add column fecha_firma
        Schema::table('acuerdos_comerciales', function (Blueprint $table) {
            $table->date('fecha_firma')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //drop column fecha_firma
        Schema::table('acuerdos_comerciales', function (Blueprint $table) {
            $table->dropColumn('fecha_firma')->nullable();
        });
    }
};
