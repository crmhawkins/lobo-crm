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
        //alter table pedidos add column departamento_id
        Schema::table('pedidos', function (Blueprint $table) {
           //integer departamento_id
              $table->Integer('departamento_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //alter table pedidos drop column departamento_id
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn('departamento_id');});
    }
};
