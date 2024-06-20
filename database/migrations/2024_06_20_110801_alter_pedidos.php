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
        //add fecha_entrega to pedidos
        Schema::table('pedidos', function (Blueprint $table) {
            $table->date('fecha_entrega')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //remove fecha_entrega from pedidos
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn('fecha_entrega');
        });
    }
};
