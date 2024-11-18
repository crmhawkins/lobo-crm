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
        //add column lote_id to productos_pedido_pack
        Schema::table('productos_pedido_pack', function (Blueprint $table) {
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
        //drop column lote_id from productos_pedido_pack
        Schema::table('productos_pedido_pack', function (Blueprint $table) {
            $table->dropColumn('lote_id');
        });
    }
};
