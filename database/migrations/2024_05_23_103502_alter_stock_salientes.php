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
        //add colum almacen_destino_id
        Schema::table('stock_salientes', function (Blueprint $table) {
            $table->unsignedBigInteger('almacen_origen_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //drop column almacen_destino_id
        Schema::table('stock_salientes', function (Blueprint $table) {
            $table->dropColumn('almacen_origen_id');
        });
    }
};
