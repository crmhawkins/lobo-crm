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
        //add column npedido_cliente
        Schema::table('pedidos', function (Blueprint $table) {
            $table->string('npedido_cliente')->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //drop column npedido_cliente
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn('npedido_cliente');
        });
    }
};
