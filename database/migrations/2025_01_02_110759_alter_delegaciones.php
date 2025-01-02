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
        //add column COD_numero to delegaciones table
        Schema::table('delegaciones', function (Blueprint $table) {
            $table->string('COD_numero')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //drop column COD_numero from delegaciones table
        Schema::table('delegaciones', function (Blueprint $table) {
            $table->dropColumn('COD_numero');
        });
    }
};
