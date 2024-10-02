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
        //establecer softdeletes en pedidos_incidencias
        Schema::table('pedidos_incidencias', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //drop 
        Schema::table('pedidos_incidencias', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
