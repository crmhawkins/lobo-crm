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
        //add column delegacion_id
        Schema::table('clientes_comercial', function (Blueprint $table) {
            $table->unsignedBigInteger('delegacion_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //drop column delegacion_id
        Schema::table('clientes_comercial', function (Blueprint $table) {
            $table->dropColumn('delegacion_id');
        });
    }
};
