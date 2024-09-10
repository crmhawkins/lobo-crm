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
        //add column gastos de transporte
        Schema::table('pedidos', function (Blueprint $table) {
            $table->decimal('gastos_transporte', 10, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //drop column gastos de transporte
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn('gastos_transporte');
        });
    }
};
