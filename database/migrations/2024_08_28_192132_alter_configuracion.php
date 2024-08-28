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
        //add column texto_factura, texto_pedido, texto_albaran, texto_email
        Schema::table('configuracion', function (Blueprint $table) {
            $table->text('texto_factura')->nullable();
            $table->text('texto_pedido')->nullable();
            $table->text('texto_albaran')->nullable();
            $table->text('texto_email')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
