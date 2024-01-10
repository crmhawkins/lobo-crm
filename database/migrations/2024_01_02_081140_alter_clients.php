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
           // Agregar nuevas columnas

            $table->integer('estado');
            $table->decimal('precio_crema', 8, 2);
            $table->decimal('precio_vodka07l', 8, 2);
            $table->decimal('precio_vodka175l', 8, 2);
            $table->decimal('precio_vodka3l', 8, 2);
            $table->text('nota');



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
            //// Revertir los cambios si es necesario
            $table->dropColumn('estado');
            $table->dropColumn('precio_crema');
            $table->dropColumn('precio_vodka07l');
            $table->dropColumn('precio_vodka175l');
            $table->dropColumn('precio_vodka3l');
            $table->dropColumn('nota');

        });
    }
};
