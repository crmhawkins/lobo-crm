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
        Schema::table('clientes', function (Blueprint $table) {
            //// Agregar valores predeterminados a las columnas existentes
            $table->integer('estado')->default(1)->change();
            $table->decimal('precio_crema', 8, 2)->default(8.34)->change();
            $table->decimal('precio_vodka07l', 8, 2)->default(23.50)->change();
            $table->decimal('precio_vodka175l', 8, 2)->default(52.00)->change();
            $table->decimal('precio_vodka3l', 8, 2)->default(135.00)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clientes', function (Blueprint $table) {

            $table->integer('estado')->default(null)->change(); // Elimina el valor predeterminado
            $table->decimal('precio_crema', 8, 2)->default(null)->change(); // Elimina el valor predeterminado
            $table->decimal('precio_vodka07l', 8, 2)->default(null)->change(); // Elimina el valor predeterminado
            $table->decimal('precio_vodka175l', 8, 2)->default(null)->change(); // Elimina el valor predeterminado
            $table->decimal('precio_vodka3l', 8, 2)->default(null)->change(); // Elimina el valor predeterminado

        });
    }
};
