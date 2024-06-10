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
        //add firma
        Schema::table('configuracion', function (Blueprint $table) {
            $table->string('firma')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //drop firma
        Schema::table('configuracion', function (Blueprint $table) {
            $table->dropColumn('firma');
        });
    }
};
