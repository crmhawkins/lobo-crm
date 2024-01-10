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
            $table->decimal('precio_ud', 20, 2)->change(); // Cambia precio_ud a decimal
            $table->decimal('precio_total', 20, 2); // Añade la columna precio_total
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
            $table->integer('precio_ud')->change(); // Cambia precio_ud de vuelta a entero
            $table->dropColumn('precio_total'); // Elimina la columna precio_total
        });
    }
};
