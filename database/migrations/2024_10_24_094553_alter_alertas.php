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
        //add column popup to alertas
        Schema::table('alertas', function (Blueprint $table) {
            $table->boolean('popup')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //drop column popup from alertas
        Schema::table('alertas', function (Blueprint $table) {
            $table->dropColumn('popup');
        });
    }
};
