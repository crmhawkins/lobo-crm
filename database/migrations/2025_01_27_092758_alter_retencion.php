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
        //add column dias_retencion
        Schema::table('retencion', function (Blueprint $table) {
            $table->integer('dias_retencion')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //drop column dias_retencion
        Schema::table('retencion', function (Blueprint $table) {
            $table->dropColumn('dias_retencion');
        });
    }
};
