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
        Schema::table('productos_pedido', function (Blueprint $table) {
            $table->renameColumn('producto_lote_id','producto_pedido_id');
            $table->string('lote_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('productos_pedido', function (Blueprint $table) {
            $table->renameColumn('producto_pedido_id','producto_lote_id');
            $table->string('lote_id');
        });
    }
};
