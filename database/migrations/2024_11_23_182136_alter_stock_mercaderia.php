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
        //add column stock_minimo
        Schema::table('stock_mercaderia', function (Blueprint $table) {
            $table->integer('stock_minimo')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //drop column stock_minimo
        Schema::table('stock_mercaderia', function (Blueprint $table) {
            $table->dropColumn('stock_minimo');
        });
    }
};
