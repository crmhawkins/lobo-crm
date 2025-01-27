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
        //add column retencion_id
        Schema::table('facturas', function (Blueprint $table) {
            $table->unsignedBigInteger('retencion_id')->nullable();
            $table->foreign('retencion_id')->references('id')->on('retencion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //drop column retencion_id
        Schema::table('facturas', function (Blueprint $table) {
            $table->dropForeign(['retencion_id']);
            $table->dropColumn('retencion_id');
        });
    }
};
