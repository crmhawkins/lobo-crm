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
        Schema::table('acuerdos_comerciales', function (Blueprint $table) {
            $table->dropForeign(['cliente_id']);
            $table->foreign('cliente_id')
                  ->references('id')
                  ->on('clientes_comercial')
                  ->onDelete('cascade'); // Puedes ajustar el comportamiento de borrado si es necesario
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('acuerdos_comerciales', function (Blueprint $table) {
            //
            $table->dropForeign(['cliente_id']);

            $table->foreign('cliente_id')
            ->references('id')
            ->on('clientes')
            ->onDelete('cascade');
        });
    }
};
