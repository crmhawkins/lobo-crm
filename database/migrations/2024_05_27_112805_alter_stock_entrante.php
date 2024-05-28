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
        //add column to stock_entrante table tipo
        Schema::table('stock_entrante', function (Blueprint $table) {
            $table->string('tipo')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //drop column tipo from stock_entrante table
        Schema::table('stock_entrante', function (Blueprint $table) {
            $table->dropColumn('tipo');
        });
    }
};
